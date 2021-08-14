<?php

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $categories = [
            [
                'code' => '8540d0b4-a9e6-49f7-9e34-3f2dc1a31cb9',
                'name' => 'Perencanaan',
                'description' => 'Perencanaan'
            ],
            [
                'code' => 'b5ca9ccf-6e68-4e31-bc2b-f2b170298063',
                'name' => 'Penganggaran',
                'description' => 'Penganggaran'
            ],
            [
                'code' => 'ccf26b36-e809-470e-b6a9-370cb4b4c869',
                'name' => 'Data Statistik',
                'description' => 'Data Statistik'
            ],
            [
                'code' => '687c9ee9-6480-4921-b719-e9da37bdb182',
                'name' => 'SPJ',
                'description' => 'SPJ'
            ],
        ];

        foreach ($categories as $key => $role) {
            Category::create($role);
        }
    }
}
