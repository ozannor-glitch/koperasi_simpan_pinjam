<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SavingTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'saving_type_id',
        'transaction_type',
        'amount',
        'status',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    // Constanta untuk tipe transaksi
    const TYPE_SETOR = 'setor';      // Setor/Deposit
    const TYPE_TARIK = 'tarik';      // Tarik tunai/Withdrawal

    // Constanta untuk status
    const STATUS_PENDING = 'pending';
    const STATUS_SUCCESS = 'success';
    const STATUS_FAILED = 'failed';

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

    // Scope untuk transaksi sukses
    public function scopeSuccess($query)
    {
        return $query->where('status', self::STATUS_SUCCESS);
    }

    // Scope untuk setor
    public function scopeSetor($query)
    {
        return $query->where('transaction_type', self::TYPE_SETOR);
    }

    // Scope untuk tarik
    public function scopeTarik($query)
    {
        return $query->where('transaction_type', self::TYPE_TARIK);
    }
}
