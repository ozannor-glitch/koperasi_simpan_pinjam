<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Account;
use Illuminate\Database\Seeder;

class AccountSeeder extends Seeder
{
    public function run()
    {
    $accounts = [

        // 🔵 ASSET (1xxx)
        ['code' => '1001', 'name' => 'Kas', 'type' => 'asset'],
        ['code' => '1002', 'name' => 'Bank', 'type' => 'asset'],
        ['code' => '1101', 'name' => 'Piutang Pinjaman', 'type' => 'asset'],

        // 🔴 LIABILITY (2xxx)
        ['code' => '2001', 'name' => 'Hutang', 'type' => 'liability'],

        // 🟢 EQUITY (3xxx)
        ['code' => '3001', 'name' => 'Modal', 'type' => 'equity'],
        ['code' => '3002', 'name' => 'SHU Ditahan', 'type' => 'equity'],

        // 🟡 INCOME (4xxx)
        ['code' => '4001', 'name' => 'Pendapatan Bunga', 'type' => 'income'],

        // ⚫ EXPENSE (5xxx)
        ['code' => '5001', 'name' => 'Beban Operasional', 'type' => 'expense'],
    ];

        foreach ($accounts as $acc) {
    Account::updateOrCreate(
        ['code' => $acc['code']], // pakai code sebagai unique
        $acc
    );
        }
    }
}
