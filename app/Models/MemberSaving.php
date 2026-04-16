<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MemberSaving extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'saving_type_id',
        'balance',
    ];

    protected $casts = [
        'balance' => 'string', // 🔥 bukan decimal
    ];

    // Relasi ke user
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relasi ke saving type
    public function savingType()
    {
        return $this->belongsTo(SavingType::class);
    }

    // Relasi ke saving transactions
    public function savingTransactions()
    {
        return $this->hasMany(SavingTransaction::class);
    }

    // Method untuk menambah saldo
    public function addBalance($amount)
    {
        $this->balance += $amount;
        return $this->save();
    }

    // Method untuk mengurangi saldo
    public function subtractBalance($amount)
    {
        if ($this->balance < $amount) {
            throw new \Exception('Saldo tidak mencukupi');
        }
       $this->balance = bcsub(
            (string) ($this->balance ?? 0),
            (string) $amount,
            2
        );
        return $this->save();
    }
}
