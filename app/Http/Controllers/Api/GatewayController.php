<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\GatewayResource;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderedProduct;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class GatewayController extends Controller
{
    public function orderList(Request $request)
    {
        $items = DB::table('all_loader_view')->where('order_status_id', '1')->paginate(10);
        return new GatewayResource(true, 'Order Successfully Loaded!', $items);
    }

    public function order(Request $request)
    {
        $message = $request->message;
        $sender = $request->sender;
        $order_status_id = $request->order_status_id;

        $mEx = explode('#', $message);

        //get user from auth

        //search product by sku
        $product = Product::where('sku', $mEx[5])->first();
        if ($product == null) {
            return response()->json(new GatewayResource(false, 'Product Not Found', null), 404);
        }

        //get destination
        $destination = DB::table('jne_destination')->where('ZIP_CODE', $mEx[3])->first();
        if ($destination == null) {
            return response()->json(new GatewayResource(false, 'Destination Not Found', null), 404);
        }

        $customer = Customer::create([
            'customer_status_id' => 1,
            'nama' => $mEx[1],
            'alamat' => $mEx[2],
            'kecamatan' => $destination->DISTRICT_NAME,
            'kota' => $destination->CITY_NAME,
            'kodepos' => $mEx[3],
            'hp' => $mEx[4],
            'order_date_string' => date("d-m-Y H:i:s"),
        ]);

        $total_harga = $product->harga * $mEx[6];
        $total_berat = $product->berat * $mEx[6];
        $total_ongkir = null;

        //insert to orders
        $payment_method = 1;
        if ($mEx[7] != "Y" && $mEx[7] != "y") {
            $payment_method = 2;
        } else {
            //call api get tarif
            $tarif_response_raw = Http::acceptJson()->asForm()->post('http://apiv2.jne.co.id:10101/tracing/api/pricedev', [
                'username' => env('JNE_USERNAME'),
                'api_key' => env('JNE_API_KEY'),
                'from' => 'TSM30000',
                'thru' => $destination->TARIFF_CODE,
                'weight' => $total_berat,
            ]);
            $tarif_response = json_decode($tarif_response_raw, true);
            $collection = collect($tarif_response['price']);
            $filtered = $collection->whereIn('service_code', ['CTC19', 'REG19']);
            if (count($filtered) < 1) {
                return response()->json(new GatewayResource(false, 'Tarif With REG or CTC Not Found', null), 404);
            }
            $total_ongkir = $filtered->first()['price'];
        }

        $order = Order::create([
            'customer_id' => $customer->id,
            'payment_id' => $payment_method,
            'user_id' => 1,
            'order_status_id' => $order_status_id ? $order_status_id : 1,
            'tanggal_pesan_string' => date("d-m-Y H:i:s"),
            'total_harga' => $total_harga,
            'total_berat' => $total_berat,
            'total_pcs' => $mEx[6],
            'from' => 'TSM30000',
            'thru' => $destination->TARIFF_CODE,
            'ongkir' => $total_ongkir,
        ]);

        //insert to ordered product
        $orderedProduct = OrderedProduct::create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'pcs' => $mEx[6],
        ]);
        return new GatewayResource(true, 'Order Successfully Added!', $customer);
    }

    public function downloadLoader(Request $request)
    {
        header('Access-Control-Allow-Origin: *');
        $reader = IOFactory::createReader('Xlsx');
        $spreadsheet = $reader->load("../resources/template/template-loader-jne.xlsx");

        $contentStartRow = 2;
        $currentContentRow = 2;

        $items = DB::table('today_loader_view')->get();

        foreach ($items as $item) {
            $pecah_belah = "General Goods";
            if ($item->keterangan == 1) {
                $pecah_belah = "BARANG PECAH BELAH";
            }

            $iscod = "N";
            $hargacod = "";
            if ($item->payment_id == 1) {
                $iscod = "Y";
                $hargacod = $item->total_harga;
            }

            $spreadsheet->getSheetByName('Template Unggah Loader')->insertNewRowBefore($currentContentRow + 1, 1);
            $spreadsheet->getSheetByName('Template Unggah Loader')
                ->setCellValue('A' . $currentContentRow, $item->nama)
                ->setCellValue('B' . $currentContentRow, $item->alamat)
                ->setCellValue('D' . $currentContentRow, $item->kodepos)
                ->setCellValue('G' . $currentContentRow, $item->hp)
                ->setCellValue('H' . $currentContentRow, $item->total_pcs)
                ->setCellValue('I' . $currentContentRow, $item->total_berat)
                ->setCellValue('J' . $currentContentRow, $item->deskripsi)
                ->setCellValue('K' . $currentContentRow, $item->total_harga)
                ->setCellValue('L' . $currentContentRow, $pecah_belah)
                ->setCellValue('M' . $currentContentRow, 'REG')
                ->setCellValue('Q' . $currentContentRow, $iscod)
                ->setCellValue('R' . $currentContentRow, $hargacod)
                ->setCellValue('S' . $currentContentRow, $item->nama_pengirim)
                ->setCellValue('T' . $currentContentRow, $item->alamat_pengirim)
                ->setCellValue('U' . $currentContentRow, $item->kota_pengirim)
                ->setCellValue('V' . $currentContentRow, $item->kodepos_pengirim)
                ->setCellValue('W' . $currentContentRow, $item->provinsi_pengirim)
                ->setCellValue('X' . $currentContentRow, $item->nama_pengirim)
                ->setCellValue('Y' . $currentContentRow, $item->hp_pengirim)
                ->setCellValue('Z' . $currentContentRow, $item->jne_id);

            $currentContentRow++;
        }

        $spreadsheet->getSheetByName('Template Unggah Loader')->removeRow($currentContentRow, 1);

        $writer = new Xlsx($spreadsheet);
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="loader.xlsx"');
        $writer->save('php://output');
    }

    public function getDestinationByZip(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'kodepos' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $destination = DB::table('jne_destination')->where('ZIP_CODE', $request->kodepos)->first();
        if ($destination == null) {
            return response()->json(new GatewayResource(false, 'Destination Not Found', null), 422);
        }
        return new GatewayResource(true, 'Destination Found!', $destination);
    }

    public function getOrigin()
    {
        $origin_response_raw = Http::acceptJson()->asForm()->post('http://apiv2.jne.co.id:10101/insert/getorigin', [
            'username' => env('JNE_USERNAME'),
            'api_key' => env('JNE_API_KEY'),
        ]);
        return $origin_response = json_decode($origin_response_raw, true);
    }
}
