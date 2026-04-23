<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Loan extends Model
{
    protected $fillable = [
    'user_id',
    'loan_type_id',
    'total_amount',
    'tenor',
    'status',
    'pdf',
    'recipient_name',
    'bank_name',
    'account_number',
    'collateral_name',
    'collateral_value',
    'collateral_photo',
    'akad',
    'rejection_reason'
];
    public function index()
{
    $loans = \App\Models\Loan::with(['user','loanType'])->latest()->get();
    return view('loans.index', compact('loans'));
}
    public function user()
{
    return $this->belongsTo(User::class);
}

public function loanType()
{
    return $this->belongsTo(LoanType::class);
}

public function installments()
{
    return $this->hasMany(LoanInstallment::class);
}
public function approvals()
{
    return $this->hasMany(LoanApproval::class);
}
public function generateInstallments()
{
    $type = $this->loanType;

    $P = $this->total_amount;
    $n = $this->tenor;
    $r = $type->interest_rate_percent / 100;

    $monthlyPrincipal = $P / $n;

    for ($i = 1; $i <= $n; $i++) {

        $interest = $P * $r;
        $total = $monthlyPrincipal + $interest;

        LoanInstallment::create([
            'loan_id' => $this->id,
            'installment_number' => $i,
            'due_date' => now()->addMonths($i),
            'amount_due' => $total,
            'status' => 'unpaid'
        ]);
    }
}

}
