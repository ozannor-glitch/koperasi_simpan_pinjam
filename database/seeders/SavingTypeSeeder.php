<?php

namespace Database\Seeders;

use App\Models\SavingType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SavingTypeSeeder extends Seeder
{
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
    }
}
