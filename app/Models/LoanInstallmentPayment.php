<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoanInstallmentPayment extends Model
{

    protected $fillable = [
        'loan_installment_id',
        'amount_paid',
        'penalty',
        'paid_at',
    ];

    protected $casts = [
        'amount_paid' => 'decimal:2',
        'penalty'     => 'decimal:2',
        'paid_at'     => 'datetime',
    ];

    // 🔗 relasi ke installment
    public function installment()
    {
        return $this->belongsTo(LoanInstallment::class, 'loan_installment_id');
    }
}
