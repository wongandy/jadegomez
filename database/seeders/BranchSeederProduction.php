<?php

namespace Database\Seeders;

use App\Models\Branch;
use Illuminate\Database\Seeder;

class BranchSeederProduction extends Seeder
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
                'contact_number' => '2632489'
            ]
        ];

        foreach ($datas as $data) {
            Branch::create($data);
        }
    }
}
