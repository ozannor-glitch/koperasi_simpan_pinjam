<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\JournalItem;

class BalanceSheetController extends Controller
{
    public function index()
    {
        $accounts = Account::orderBy('code')->get();

        $assets = [];
        $liabilities = [];
        $equities = [];

        $totalAsset = 0;
        $totalLiability = 0;
        $totalEquity = 0;

        foreach ($accounts as $account) {

            $items = JournalItem::where('account_id', $account->id)->get();

            $debit = $items->sum('debit');
            $credit = $items->sum('credit');

            // 🔥 hitung saldo
            if (in_array($account->type, ['asset'])) {
                $balance = $debit - $credit;
            } else {
                $balance = $credit - $debit;
            }

            if ($account->type == 'asset') {
                $assets[] = ['account' => $account, 'balance' => $balance];
                $totalAsset += $balance;
            }

            if ($account->type == 'liability') {
                $liabilities[] = ['account' => $account, 'balance' => $balance];
                $totalLiability += $balance;
            }

            if ($account->type == 'equity') {
                $equities[] = ['account' => $account, 'balance' => $balance];
                $totalEquity += $balance;
            }
        }

        // 🔥 ambil laba rugi (dari sebelumnya)
        $incomeAccounts = Account::where('type','income')->get();
        $expenseAccounts = Account::where('type','expense')->get();

        $totalIncome = 0;
        $totalExpense = 0;

        foreach ($incomeAccounts as $acc) {
            $items = JournalItem::where('account_id', $acc->id)->get();
            $totalIncome += $items->sum('credit') - $items->sum('debit');
        }

        foreach ($expenseAccounts as $acc) {
            $items = JournalItem::where('account_id', $acc->id)->get();
            $totalExpense += $items->sum('debit') - $items->sum('credit');
        }

        $laba = $totalIncome - $totalExpense;

        // 🔥 masuk ke ekuitas
        $totalEquity += $laba;

        return view('superadmin.pages.akuntansi.neracakeuangan.index', compact(
            'assets','liabilities','equities',
            'totalAsset','totalLiability','totalEquity','laba'
        ));
    }
}
