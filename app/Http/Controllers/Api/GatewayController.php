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

        $mEx = explode('#', $message);

        //search product by sku
        $product = Product::where('sku', $mEx[5])->first();
        if ($product == null) {
            return response()->json(new GatewayResource(false, 'Product Not Found', null), 404);
        }

        $customer = Customer::create([
            'customer_status_id' => 1,
            'nama' => $mEx[1],
            'alamat' => $mEx[2],
            //'kecamatan' => $request->kecamatan,
            //'kota' => $request->kota,
            'kodepos' => $mEx[3],
            'hp' => $mEx[4],
            'order_date_string' => date("d-m-Y H:i:s", strtotime('+7 hours')),
        ]);

        //insert to orders
        $payment_method = 1;
        if ($mEx[7] != "Y" && $mEx[7] != "y") {
            $payment_method = 2;
        }

        $total_harga = $product->harga * $mEx[6];
        $total_berat = ($product->berat * $mEx[6]) / 1000;

        $order = Order::create([
            'customer_id' => $customer->id,
            'payment_id' => $payment_method,
            'user_id' => 1,
            'order_status_id' => 1,
            'tanggal_pesan_string' => date("d-m-Y H:i:s", strtotime('+7 hours')),
            'total_harga' => $total_harga,
            'total_berat' => $total_berat,
            'total_pcs' => $mEx[6],
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
}
