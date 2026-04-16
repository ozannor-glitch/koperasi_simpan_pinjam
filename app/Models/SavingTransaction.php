<?php

namespace App\Models;

<<<<<<< HEAD
=======
use Illuminate\Database\Eloquent\Factories\HasFactory;
>>>>>>> d21c2f7 (tambah api saving, data profil user, dan perbaikan register)
use Illuminate\Database\Eloquent\Model;

class SavingTransaction extends Model
{
<<<<<<< HEAD
    protected $table = 'saving_transactions';
=======
    use HasFactory;
>>>>>>> d21c2f7 (tambah api saving, data profil user, dan perbaikan register)

    protected $fillable = [
        'user_id',
        'saving_type_id',
        'transaction_type',
        'amount',
        'status',
    ];

<<<<<<< HEAD
    // 🔗 relasi ke user (anggota)
=======
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
>>>>>>> d21c2f7 (tambah api saving, data profil user, dan perbaikan register)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

<<<<<<< HEAD
    // 🔗 relasi ke jenis simpanan
=======
    // Relasi ke saving type
>>>>>>> d21c2f7 (tambah api saving, data profil user, dan perbaikan register)
    public function savingType()
    {
        return $this->belongsTo(SavingType::class);
    }
<<<<<<< HEAD
=======

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
>>>>>>> d21c2f7 (tambah api saving, data profil user, dan perbaikan register)
}
