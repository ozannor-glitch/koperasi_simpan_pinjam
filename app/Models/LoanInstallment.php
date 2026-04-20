<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoanInstallment extends Model
{
    protected $fillable = [
    'loan_id',
    'installment_number',
    'due_date',
    'amount_due',
    'principal',
    'interest',
    'remaining_balance',
    'status'
];
    public function loan()
{
    return $this->belongsTo(Loan::class);
}
public function payments()
{
    return $this->hasMany(LoanInstallmentPayment::class);
}
}
