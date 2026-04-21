<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;


class Journal extends Model
{
    protected $fillable = ['date','description','source_type','source_id'];

    public function items()
    {
        return $this->hasMany(JournalItem::class);
    }

    public function debitTotal()
    {
        return $this->items->sum('debit');
    }

    public function creditTotal()
    {
        return $this->items->sum('credit');
    }
}
