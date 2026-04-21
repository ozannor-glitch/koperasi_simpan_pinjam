<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Account;
use Illuminate\Http\Request;

class GeneralLedgerController extends Controller
{
    public function index(Request $request)
    {
        $accountId = $request->get('account_id');

        $accounts = Account::orderBy('code')->get();

        $account = null;
        $items = collect();
        $running = 0;

        if ($accountId) {

            $account = Account::findOrFail($accountId);

            $items = \App\Models\JournalItem::with('journal')
                ->where('account_id', $accountId)
                ->orderBy('created_at')
                ->get();

            // 🔥 hitung saldo berjalan
            foreach ($items as $item) {

                if (in_array($account->type, ['asset','expense'])) {
                    $running += ($item->debit - $item->credit);
                } else {
                    $running += ($item->credit - $item->debit);
                }

                $item->running_balance = $running;
            }
        }

        return view('superadmin.pages.akuntansi.buku_besar.index', compact('accounts','account','items'));
    }

 public function show($id)
{
    $account = Account::with('journalItems.journal')->findOrFail($id);

    $items = $account->journalItems
        ->sortBy(fn($i) => $i->journal->date)
        ->values();

    $runningBalance = 0;

    foreach ($items as $item) {

        if (in_array($account->type, ['asset','expense'])) {
            $runningBalance += ($item->debit - $item->credit);
        } else {
            $runningBalance += ($item->credit - $item->debit);
        }

        $item->running_balance = $runningBalance;
    }

    return view('superadmin.pages.akuntansi.buku_besar.show', compact('account','items'));
}
}
