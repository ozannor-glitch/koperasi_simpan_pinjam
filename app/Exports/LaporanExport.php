<?php

namespace App\Exports;

use App\Models\SavingTransaction;
use Maatwebsite\Excel\Concerns\FromCollection;

class LaporanExport implements FromCollection
{
    public function collection()
    {
        return SavingTransaction::all();
    }
}
