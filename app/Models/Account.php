<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    public function journalItems()
{
    return $this->hasMany(\App\Models\JournalItem::class);
}
}
