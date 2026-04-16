<?php

namespace App\Models;

<<<<<<< HEAD
=======
use Illuminate\Database\Eloquent\Factories\HasFactory;
>>>>>>> d21c2f7 (tambah api saving, data profil user, dan perbaikan register)
use Illuminate\Database\Eloquent\Model;

class MemberSaving extends Model
{
<<<<<<< HEAD
    protected $table = 'member_savings';
=======
    use HasFactory;
>>>>>>> d21c2f7 (tambah api saving, data profil user, dan perbaikan register)

    protected $fillable = [
        'user_id',
        'saving_type_id',
        'balance',
    ];

<<<<<<< HEAD
    // 🔗 relasi ke user (anggota)
=======
    protected $casts = [
        'balance' => 'decimal:2',
    ];

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
        $this->balance -= $amount;
        return $this->save();
    }
>>>>>>> d21c2f7 (tambah api saving, data profil user, dan perbaikan register)
}
