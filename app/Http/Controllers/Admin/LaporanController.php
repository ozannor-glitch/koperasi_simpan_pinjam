<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\SavingTransaction;

class LaporanController extends Controller
{
    public function anggota()
    {
        $users = User::where('role','anggota')->get();

        return view('superadmin.pages.laporan.anggota.index', compact('users'));
    }
    public function keuangan()
    {
        $transactions = SavingTransaction::with('user','savingType')->latest()->get();

        return view('superadmin.pages.laporan.keuangan', compact('transactions'));
    }
}
