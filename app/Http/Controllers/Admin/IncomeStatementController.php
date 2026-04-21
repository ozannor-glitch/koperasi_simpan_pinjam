<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\JournalItem;

class IncomeStatementController extends Controller
{
    public function index()
{
        // ambil akun income & expense
        $accounts = Account::whereIn('type', ['income','expense'])
            ->orderBy('code')
            ->get();

        $data = [];
        $totalIncome = 0;
        $totalExpense = 0;

        foreach ($accounts as $account) {

            $items = JournalItem::where('account_id', $account->id)->get();

            $debit = $items->sum('debit');
            $credit = $items->sum('credit');

            if ($account->type == 'income') {
                $amount = $credit - $debit;
                $totalIncome += $amount;
            } else {
                $amount = $debit - $credit;
                $totalExpense += $amount;
            }

            $data[] = [
                'account' => $account,
                'amount' => $amount
            ];
        }

        $netIncome = $totalIncome - $totalExpense;

        return view('superadmin.pages.akuntansi.laba_rugi.index', compact(
            'data',
            'totalIncome',
            'totalExpense',
            'netIncome'
        ));
    }
}
