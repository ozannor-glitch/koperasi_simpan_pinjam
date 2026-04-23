<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Penarikan extends Model
{
   protected $fillable = [
    'kode_penarikan',
    'user_id',
    'member_saving_id',
    'source_type',
    'source_id',
    'jumlah',
    'jumlah_diterima',
    'bank',
    'no_rekening',
    'nama_rekening',
    'status',
];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function memberSaving()
    {
        return $this->belongsTo(MemberSaving::class);
    }
}
