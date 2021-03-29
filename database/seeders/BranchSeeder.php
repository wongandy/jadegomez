<?php

namespace Database\Seeders;

use App\Models\Branch;
use Illuminate\Database\Seeder;

class BranchSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $datas = [
            [
                'name' => 'Jade Gomez Computer Trading',
                'address' => 'Basak Pardo',
                'contact_number' => 1234
            ],
            [
                'name' => 'Jade Gomez Computer Trading',
                'address' => 'Mandaue City',
                'contact_number' => 2345
            ],
            [
                'name' => 'Ragomez Computer Trading',
                'address' => 'Carcar City',
                'contact_number' => 6789
            ]
        ];

        foreach ($datas as $data) {
            Branch::create($data);
        }
    }
}
