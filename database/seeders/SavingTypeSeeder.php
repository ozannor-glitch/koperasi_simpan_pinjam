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
        // Cek apakah sudah ada data, jika belum maka insert
        if (SavingType::count() == 0) {
            $types = [
                ['name' => 'Tabungan Reguler', 'minimum_amount' => 50000],
                ['name' => 'Tabungan Pendidikan', 'minimum_amount' => 100000],
                ['name' => 'Tabungan Hari Raya', 'minimum_amount' => 20000],
                ['name' => 'Tabungan Investasi', 'minimum_amount' => 500000],
                ['name' => 'Tabungan Darurat', 'minimum_amount' => 100000],
            ];

            foreach ($types as $type) {
                SavingType::create($type);
            }

            $this->command->info('Saving types seeded successfully!');
        } else {
            $this->command->info('Saving types already exists, skipping...');
        }
>>>>>>> d21c2f7 (tambah api saving, data profil user, dan perbaikan register)
    }
}
