<?php

namespace Database\Seeders;

use App\Models\SavingType;
<<<<<<< HEAD
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
=======
>>>>>>> d21c2f7 (tambah api saving, data profil user, dan perbaikan register)
use Illuminate\Database\Seeder;

class SavingTypeSeeder extends Seeder
{
<<<<<<< HEAD
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
          SavingType::create([
        'name' => 'Wajib',
        'minimum_amount' => 10000
    ]);

    SavingType::create([
        'name' => 'Sukarela',
        'minimum_amount' => 0
    ]);

     SavingType::create([
        'name' => 'Deposito',
        'minimum_amount' => 1000000
    ]);
=======
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
>>>>>>> d21c2f7 (tambah api saving, data profil user, dan perbaikan register)
    }
}
