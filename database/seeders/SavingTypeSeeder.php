<?php

namespace Database\Seeders;

use App\Models\SavingType;
use Illuminate\Database\Seeder;

class SavingTypeSeeder extends Seeder
{
    public function run()
    {
        $types = [
            [
                'name' => 'Deposito',
                'minimum_amount' => 1000000, // Minimal Rp 1.000.000
            ],
            [
                'name' => 'Wajib',
                'minimum_amount' => 50000, // Minimal Rp 50.000
            ],
            [
                'name' => 'Sukarela',
                'minimum_amount' => 10000, // Minimal Rp 10.000
            ],
        ];

        foreach ($types as $type) {
            SavingType::updateOrCreate(
                ['name' => $type['name']],
                ['minimum_amount' => $type['minimum_amount']]
            );
        }
    }
}
