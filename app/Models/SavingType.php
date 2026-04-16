<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SavingType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'minimum_amount',
    ];

    protected $casts = [
        'minimum_amount' => 'decimal:2',
    ];

    // Constanta untuk jenis tabungan
    const TYPE_DEPOSITO = 'Deposito';
    const TYPE_WAJIB = 'Wajib';
    const TYPE_SUKARELA = 'Sukarela';

    // Relasi ke member savings
    public function memberSavings()
    {
        return $this->hasMany(MemberSaving::class);
    }

    // Relasi ke saving transactions
    public function savingTransactions()
    {
        return $this->hasMany(SavingTransaction::class);
    }

    // Cek apakah jenis tabungan adalah Deposito
    public function isDeposito()
    {
        return $this->name === self::TYPE_DEPOSITO;
    }

    // Cek apakah jenis tabungan adalah Wajib
    public function isWajib()
    {
        return $this->name === self::TYPE_WAJIB;
    }

    // Cek apakah jenis tabungan adalah Sukarela
    public function isSukarela()
    {
        return $this->name === self::TYPE_SUKARELA;
    }
}
