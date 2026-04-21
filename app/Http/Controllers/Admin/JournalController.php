<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\Journal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class JournalController extends Controller
{
 public function index()
    {
        $journals = Journal::with('items.account')->latest()->get();
        return view('superadmin.pages.akuntansi.jurnal.index', compact('journals'));
    }

    public function create()
    {
        $accounts = Account::all();
        return view('superadmin.pages.akuntansi.jurnal.create', compact('accounts'));
    }

    public function store(Request $request)
{
    $request->validate([
        'date' => 'required',
        'description' => 'required',
        'accounts' => 'required|array'
    ]);

    DB::transaction(function () use ($request) {

        $journal = Journal::create([
            'date' => $request->date,
            'description' => $request->description
        ]);

        $totalDebit = 0;
        $totalCredit = 0;

        foreach ($request->accounts as $i => $accountId) {

            $debit = $request->debit[$i] ?? 0;
            $credit = $request->credit[$i] ?? 0;

            $totalDebit += $debit;
            $totalCredit += $credit;

            $journal->items()->create([
                'account_id' => $accountId,
                'debit' => $debit,
                'credit' => $credit
            ]);
        }

        if ($totalDebit != $totalCredit) {
            throw new \Exception('Debit & Credit harus sama!');
        }
    });

    return redirect()->route('superadmin.jurnal.index')
        ->with('success', 'Jurnal berhasil dibuat');
}

    public function edit($id)
    {
        $journal = Journal::with('items')->findOrFail($id);
        $accounts = Account::all();

        return view('superadmin.pages.akuntansi.jurnal.edit', compact('journal','accounts'));
    }

  public function update(Request $request, $id)
    {
        $journal = Journal::findOrFail($id);

        DB::transaction(function () use ($request, $journal) {

            $journal->update([
                'date' => $request->date,
                'description' => $request->description
            ]);

            // 🔥 HAPUS DETAIL LAMA
            $journal->items()->delete();

            $totalDebit = 0;
            $totalCredit = 0;

            foreach ($request->accounts as $i => $accountId) {

                $debit = $request->debit[$i] ?? 0;
                $credit = $request->credit[$i] ?? 0;

                $totalDebit += $debit;
                $totalCredit += $credit;

                $journal->items()->create([
                    'account_id' => $accountId,
                    'debit' => $debit,
                    'credit' => $credit
                ]);
            }

            if ($totalDebit != $totalCredit) {
                throw new \Exception('Debit dan Credit harus sama!');
            }
        });

        return redirect()->route('superadmin.jurnal.index')
            ->with('success', 'Jurnal berhasil diupdate');
    }

    public function destroy($id)
    {
        $journal = Journal::findOrFail($id);
        $journal->delete();

        return back()->with('success', 'Jurnal dihapus');
    }
}
