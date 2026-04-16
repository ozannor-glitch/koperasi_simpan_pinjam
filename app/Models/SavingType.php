<?php

namespace App\Models;

<<<<<<< HEAD
=======
use Illuminate\Database\Eloquent\Factories\HasFactory;
>>>>>>> d21c2f7 (tambah api saving, data profil user, dan perbaikan register)
use Illuminate\Database\Eloquent\Model;

class SavingType extends Model
{
<<<<<<< HEAD
    protected $table = 'saving_types';
=======
    use HasFactory;
>>>>>>> d21c2f7 (tambah api saving, data profil user, dan perbaikan register)

    protected $fillable = [
        'name',
        'minimum_amount',
    ];

<<<<<<< HEAD
    // 🔗 relasi ke saldo anggota
    public function savings()
=======
    protected $casts = [
        'minimum_amount' => 'decimal:2',
    ];

    // Constanta untuk jenis tabungan
    const TYPE_DEPOSITO = 'Deposito';
    const TYPE_WAJIB = 'Wajib';
    const TYPE_SUKARELA = 'Sukarela';

    // Relasi ke member savings
    public function memberSavings()
>>>>>>> d21c2f7 (tambah api saving, data profil user, dan perbaikan register)
    {
        return $this->hasMany(MemberSaving::class);
    }

<<<<<<< HEAD
    // 🔗 relasi ke transaksi
    public function transactions()
=======
    // Relasi ke saving transactions
    public function savingTransactions()
>>>>>>> d21c2f7 (tambah api saving, data profil user, dan perbaikan register)
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
