<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Penarikan extends Model
{
    protected $fillable = [
        'user_id',
        'member_saving_id',
        'kode_penarikan',
        'jumlah',
        'biaya_admin',
        'jumlah_diterima',
        'status'
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
