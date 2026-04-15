<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SavingTransaction extends Model
{
    protected $table = 'saving_transactions';

    protected $fillable = [
        'user_id',
        'saving_type_id',
        'transaction_type',
        'amount',
        'status',
    ];

    // 🔗 relasi ke user (anggota)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // 🔗 relasi ke jenis simpanan
    public function savingType()
    {
        return $this->belongsTo(SavingType::class);
    }
}
