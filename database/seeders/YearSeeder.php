<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\YearModel;

class YearSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $currentYear = date('Y');
        $startYear = $currentYear - 5; // Start from 5 years ago
        $endYear = $currentYear + 5; // End at 5 years in the future

        for ($year = $startYear; $year <= $endYear; $year++) {
            YearModel::create([
                'year' => $year,
            ]);
        }
    }
}
