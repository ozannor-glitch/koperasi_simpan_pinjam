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
}
