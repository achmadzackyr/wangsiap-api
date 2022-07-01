<?php

namespace App\Exports;

//use Illuminate\Support\Facades\DB;
use App\Models\Customer;
use Maatwebsite\Excel\Concerns\FromCollection;

class CustomersExport implements FromCollection
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return Customer::all();
        //return DB::table('today_loader_view')->get();
    }
}
