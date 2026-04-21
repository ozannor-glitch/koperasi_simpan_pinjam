<?php

namespace App\Services;

use App\Models\Journal;
use App\Models\JournalItem;
use Illuminate\Support\Facades\DB;

class JournalService
{
    public static function create($date, $description, $entries, $sourceType = null, $sourceId = null)
    {
        return DB::transaction(function () use ($date, $description, $entries, $sourceType, $sourceId) {

            $journal = Journal::create([
                'date' => $date,
                'description' => $description,
                'source_type' => $sourceType,
                'source_id' => $sourceId,
            ]);

            $totalDebit = 0;
            $totalCredit = 0;

            foreach ($entries as $entry) {

                $totalDebit += $entry['debit'];
                $totalCredit += $entry['credit'];

                JournalItem::create([
                    'journal_id' => $journal->id,
                    'account_id' => $entry['account_id'],
                    'debit' => $entry['debit'],
                    'credit' => $entry['credit'],
                ]);
            }

            // 🔥 VALIDASI WAJIB (DOUBLE ENTRY)
            if ($totalDebit != $totalCredit) {
                throw new \Exception('Jurnal tidak balance!');
            }

            return $journal;
        });
    }
}
