<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MemberSaving extends Model
{
    protected $table = 'member_savings';

    protected $fillable = [
        'user_id',
        'saving_type_id',
        'balance',
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

    public function penarikans()
    {
        return $this->hasMany(Penarikan::class);
    }
}
