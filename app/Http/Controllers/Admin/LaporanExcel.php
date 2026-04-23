<?php

namespace App\Http\Controllers\Admin;

use App\Exports\LaporanExport;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class LaporanExcel extends Controller
{
    public function laporanExcel()
{
    return Excel::download(new LaporanExport(), 'laporan.xlsx');
}
}
