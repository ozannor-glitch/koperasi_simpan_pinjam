<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\LoanType;

class LoanTypeSeeder extends Seeder
{
    public function run(): void
    {
        LoanType::insert([
            [
                'name' => 'Pinjaman Karyawan',
                'max_plafon' => 5000000,
                'interest_rate_percent' => 2.5,
                'max_tenor_months' => 12,
                'tenor' => 12,
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Pinjaman Usaha',
                'max_plafon' => 10000000,
                'interest_rate_percent' => 3.0,
                'max_tenor_months' => 24,
                'tenor' => 24,
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Pinjaman Darurat',
                'max_plafon' => 2000000,
                'interest_rate_percent' => 1.5,
                'max_tenor_months' => 6,
                'tenor' => 6,
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
