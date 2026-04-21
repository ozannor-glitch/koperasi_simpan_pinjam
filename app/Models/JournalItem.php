<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Account;


class JournalItem extends Model
{
    protected $fillable = ['journal_id','account_id','debit','credit'];

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    public function journal()
    {
        return $this->belongsTo(Journal::class);
    }

}
