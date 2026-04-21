<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoanType extends Model
{
    protected $fillable = [
    'name',
    'max_plafon',
    'interest_rate_percent',
    'max_tenor_months',
    'collateral_ratio (misal 70%)'
];
    public function loans()
    {
        return $this->hasMany(Loan::class);
    }
}
