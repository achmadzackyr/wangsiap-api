<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\GatewayResource;
use App\Http\Traits\WhatsappTrait;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderedProduct;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class GatewayController extends Controller
{
    use WhatsappTrait;

    public function orderList(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'order_status_id' => 'required',
            'no_penerima' => 'required',
            'date_from' => 'required',
            'date_to' => 'required',
            'page' => 'required',
            'per_page' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $dateFrom = date_format(date_create(request()->date_from), "Y-m-d") . " 00:00:00";
        $dateTo = date_format(date_create(request()->date_to), "Y-m-d") . " 23:59:59";

        $per_page = $request->per_page ? $request->per_page : 10;
        $page = $request->page ? (($request->page - 1) * $per_page) : 0;
        $data = DB::select('CALL order_list(?,?,?,?,?,?)', [$request->order_status_id, $request->no_penerima, $dateFrom, $dateTo, $per_page, $page]);

        // $offSet = ($page * $paginate) - $paginate;
        // $itemsForCurrentPage = array_slice($data, $offSet, $paginate, true);
        // $data = new \Illuminate\Pagination\LengthAwarePaginator($itemsForCurrentPage, count($data), $paginate, $page);
        return new GatewayResource(true, 'Order Successfully Loaded!', $data);
    }

    public function order(Request $request)
    {
        $message = $request->message;
        $sender = explode("@", $request->sender, 2)[0];
        $receiver = explode("@", $request->receiver, 2)[0];
        $order_status_id = $request->order_status_id;

        $mEx = explode('#', $message);

        //get user from to number
        $user = User::where('hp', $receiver)->first();
        if ($user == null) {
            return response()->json(new GatewayResource(false, 'User Not Found', null), 404);
        }

        //search product by sku per user
        $product = Product::where('sku', $mEx[5])->where('user_id', $user->id)->first();
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
            //'kecamatan' => $destination->DISTRICT_NAME,
            'kota' => $destination->CITY_NAME,
            'provinsi' => $destination->PROVINCE_NAME,
            'kodepos' => $mEx[3],
            'hp' => $mEx[4],
            'order_date_string' => date("d-m-Y H:i:s"),
            'user_id' => $user->id,
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
                'from' => $user->from,
                'thru' => $destination->TARIFF_CODE,
                'weight' => $total_berat,
            ]);
            $tarif_response = json_decode($tarif_response_raw, true);
            $collection = collect($tarif_response['price']);
            $filtered = $collection->whereIn('service_display', ['CTC', 'REG']);
            if (count($filtered) < 1) {
                return response()->json(new GatewayResource(false, 'Tarif With REG or CTC Not Found', null), 404);
            }
            $total_ongkir = $filtered->first()['price'];
        }

        $order = Order::create([
            'customer_id' => $customer->id,
            'payment_id' => $payment_method,
            'user_id' => $user->id,
            'order_status_id' => $order_status_id ? $order_status_id : 1,
            'tanggal_pesan_string' => date("d-m-Y H:i:s"),
            'total_harga' => $total_harga,
            'total_berat' => $total_berat,
            'total_pcs' => $mEx[6],
            'from' => $user->from,
            'thru' => $destination->TARIFF_CODE,
            'ongkir' => $total_ongkir,
            'no_pengirim' => $sender,
            'no_penerima' => $receiver,
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
        $validator = Validator::make($request->all(), [
            'order_status_id' => 'required',
            'no_penerima' => 'required',
            'date_from' => 'required',
            'date_to' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $dateFrom = date_format(date_create(request()->date_from), "Y-m-d") . " 00:00:00";
        $dateTo = date_format(date_create(request()->date_to), "Y-m-d") . " 23:59:59";

        header('Access-Control-Allow-Origin: *');
        $reader = IOFactory::createReader('Xlsx');
        $spreadsheet = $reader->load("../resources/template/template-loader-jne.xlsx");

        $contentStartRow = 2;
        $currentContentRow = 2;

        $items = DB::select('CALL generate_loader(?,?,?,?)', [$request->order_status_id, $request->no_penerima, $dateFrom, $dateTo]);

        foreach ($items as $item) {
            $pecah_belah = "General Goods";
            if ($item->keterangan == 1) {
                $pecah_belah = "BARANG PECAH BELAH";
            }

            $id_jne = $item->jne_id;

            $iscod = "N";
            $hargacod = "";
            if ($item->payment_id == 1) {
                $iscod = "Y";
                $id_jne = $item->jne_id_cod;
                $hargacod = $item->total_harga + $item->ongkir;
            }

            $spreadsheet->getSheetByName('Template Unggah Loader')->insertNewRowBefore($currentContentRow + 1, 1);
            $spreadsheet->getSheetByName('Template Unggah Loader')
                ->setCellValue('A' . $currentContentRow, $item->nama)
                ->setCellValue('B' . $currentContentRow, $item->alamat)
                ->setCellValue('C' . $currentContentRow, $item->kota)
                ->setCellValue('D' . $currentContentRow, $item->kodepos)
                ->setCellValue('E' . $currentContentRow, $item->provinsi)
                ->setCellValueExplicit(
                    'G' . $currentContentRow,
                    $item->hp,
                    \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING
                )
                ->setCellValue('H' . $currentContentRow, '1')
                ->setCellValue('I' . $currentContentRow, $item->total_berat)
                ->setCellValue('J' . $currentContentRow, $item->deskripsi . ' ' . $item->total_pcs)
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
                ->setCellValueExplicit(
                    'Y' . $currentContentRow,
                    $item->hp_pengirim,
                    \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING
                )
                ->setCellValueExplicit(
                    'Z' . $currentContentRow,
                    $id_jne,
                    \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC
                );

            $currentContentRow++;
        }

        $spreadsheet->getSheetByName('Template Unggah Loader')->removeRow($currentContentRow, 1);

        $writer = new Xlsx($spreadsheet);
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="loader.xlsx"');
        $writer->save('php://output');
    }

    public function getDestinationsByZip(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'kodepos' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $destination = DB::table('jne_destination')->where('ZIP_CODE', $request->kodepos)->get();
        if ($destination == null) {
            return response()->json(new GatewayResource(false, 'Destination Not Found', null), 422);
        }
        return new GatewayResource(true, 'Destination Found!', $destination);
    }

    public function getZipByDestination(Request $request)
    {
        $destination = DB::table('jne_destination');

        switch ($request->search_by) {
            case 'city':
                $destination = $destination->where('CITY_NAME', 'like', $request->search_value . '%')
                    ->orderBy('PROVINCE_NAME')
                    ->orderBy('CITY_NAME')
                    ->orderBy('SUBDISTRICT_NAME')
                    ->get();
                break;
            default:
                $destination = $destination->where('DISTRICT_NAME', 'like', $request->search_value . '%')
                    ->orderBy('PROVINCE_NAME')
                    ->orderBy('CITY_NAME')
                    ->orderBy('SUBDISTRICT_NAME')
                    ->get();
                break;
        }

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

    public function getTarif(Request $request)
    {
        $tarif_response_raw = Http::acceptJson()->asForm()->post('http://apiv2.jne.co.id:10101/tracing/api/pricedev', [
            'username' => env('JNE_USERNAME'),
            'api_key' => env('JNE_API_KEY'),
            'from' => $request->from,
            'thru' => $request->thru,
            'weight' => $request->weight,
        ]);
        return $tarif_response = json_decode($tarif_response_raw, true);
    }

    public function orderFull(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'sender' => 'required',
            'receiver' => 'required',
            'message' => 'required',
            'order_status_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(new GatewayResource(false, 'Request Invalid', $validator->errors()), 400);
        }

        $message = $request->message;
        $sender = explode("@", $request->sender, 2)[0];
        $receiver = explode("@", $request->receiver, 2)[0];
        $order_status_id = $request->order_status_id;
        $mEx = explode('#', $message);

        $user = User::where('hp', $receiver)->first();
        if ($user == null) {
            return response()->json(new GatewayResource(false, 'User Not Found', null), 404);
        }

        $product = Product::where('sku', $mEx[5])->where('user_id', $user->id)->first();
        if ($product == null) {
            return response()->json(new GatewayResource(false, 'Product Not Found', null), 404);
        }

        $destination = DB::table('jne_destination')->where('ZIP_CODE', $mEx[3])->first();
        if ($destination == null) {
            return response()->json(new GatewayResource(false, 'Destination Not Found', null), 404);
        }

        $customer = Customer::create([
            'customer_status_id' => 1,
            'nama' => $mEx[1],
            'alamat' => $mEx[2],
            'kota' => $destination->CITY_NAME,
            'provinsi' => $destination->PROVINCE_NAME,
            'kodepos' => $mEx[3],
            'hp' => $mEx[4],
            'order_date_string' => date("d-m-Y H:i:s"),
            'user_id' => $user->id,
        ]);

        $total_harga = $product->harga * $mEx[6];
        $total_berat = $product->berat * $mEx[6];
        $total_ongkir = null;

        //insert to orders
        $payment_method = 1;
        if ($mEx[7] != "Y" && $mEx[7] != "y") {
            $payment_method = 2;
        } else {
            // return $reply = [
            //     'username' => env('JNE_USERNAME'),
            //     'api_key' => env('JNE_API_KEY'),
            //     'from' => $user->from,
            //     'thru' => $destination->TARIFF_CODE,
            //     'weight' => $total_berat,
            // ];
            //call api get tarif
            $tarif_response_raw = Http::acceptJson()->asForm()->post('http://apiv2.jne.co.id:10101/tracing/api/pricedev', [
                'username' => env('JNE_USERNAME'),
                'api_key' => env('JNE_API_KEY'),
                'from' => $user->from,
                'thru' => $destination->TARIFF_CODE,
                'weight' => $total_berat,
            ]);
            $tarif_response = json_decode($tarif_response_raw, true);
            if (!$tarif_response['price']) {
                return response()->json(new GatewayResource(false, $tarif_response['error'], null), 404);
            }
            $collection = collect($tarif_response['price']);
            $filtered = $collection->whereIn('service_display', ['CTC', 'REG']);
            if (count($filtered) < 1) {
                return response()->json(new GatewayResource(false, 'Tarif With REG or CTC Not Found', null), 404);
            }
            $total_ongkir = $filtered->first()['price'];
        }

        $order = Order::create([
            'customer_id' => $customer->id,
            'payment_id' => $payment_method,
            'user_id' => $user->id,
            'order_status_id' => $order_status_id ? $order_status_id : 1,
            'tanggal_pesan_string' => date("d-m-Y H:i:s"),
            'total_harga' => $total_harga,
            'total_berat' => $total_berat,
            'total_pcs' => $mEx[6],
            'from' => $user->from,
            'thru' => $destination->TARIFF_CODE,
            'ongkir' => $total_ongkir,
            'no_pengirim' => $sender,
            'no_penerima' => $receiver,
        ]);

        $orderedProduct = OrderedProduct::create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'pcs' => $mEx[6],
        ]);

        $COD = $mEx[7] === 'Y' ? 'Ya' : 'Tidak';

        if ($order_status_id == 1) {
            $messageObject = $this->getWaReply($receiver, 1);
            $messageObject->text = str_replace(array('((NAMA))', '((ALAMAT))', '((KODEPOS))', '((HP))',
                '((COD))', '((KABUPATEN))', '((KECAMATAN))', '((PRODUK))', '((HARGAPRODUK))', '((JUMLAHPRODUK))',
                '((ONGKIR))', '((TOTALHARGA))', '((TANGGALKADALUARSA))', '*1*', '*2*'),
                array($mEx[1], $mEx[2], $mEx[3], $mEx[4], $COD, $destination->CITY_NAME, $destination->DISTRICT_NAME,
                    $product->nama, $product->harga, $mEx[6], $total_ongkir, $total_harga, '30-08-2022 18.00', '1️⃣', '2️⃣'), $messageObject->text);

            $sendMessageResponse = $this->sendMessage($receiver, $sender, json_encode($messageObject));
        } else if ($order_status_id == 2) {
            $messageObject = $this->getWaReply($receiver, 2);
            $sendMessageResponse = $this->sendMessage($receiver, $sender, json_encode($messageObject));
        }

        return new GatewayResource(true, 'Order Successfully Added!', $sendMessageResponse);
    }

    public function orderx(Request $request)
    {
        $receiver = explode("@", $request->receiver, 2)[0];
        $sender = explode("@", $request->sender, 2)[0];
        $messageObject = $this->getWaReply($receiver, 1);

        $messageObject->text = str_replace(array('((NAMA))', '((ALAMAT))', '((KODEPOS))', '((HP))',
            '((COD))', '((KABUPATEN))', '((KECAMATAN))', '((PRODUK))', '((HARGAPRODUK))', '((JUMLAHPRODUK))',
            '((ONGKIR))', '((TOTALHARGA))', '((TANGGALKADALUARSA))', '*1*', '*2*'),
            array('Achmad Zacky', 'Ciamis', '46271', '085223670378', 'COD', 'CIAMIS', 'CIJEUNGJING',
                'Emas 10 g', 'Rp5.000.000', '1', 'Rp15.000', 'Rp5.015.000', '30-08-2022 18.00', '1️⃣', '2️⃣'), $messageObject->text);
        return $this->sendMessage($sender, $receiver, json_encode($messageObject));
    }
}
