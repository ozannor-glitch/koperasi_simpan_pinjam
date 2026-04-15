<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SavingType extends Model
{
    protected $table = 'saving_types';

    protected $fillable = [
        'name',
        'minimum_amount',
    ];

    // 🔗 relasi ke saldo anggota
    public function savings()
    {
        return $this->hasMany(MemberSaving::class);
    }

    // 🔗 relasi ke transaksi
    public function transactions()
    {
        return $this->hasMany(SavingTransaction::class);
    }
}
