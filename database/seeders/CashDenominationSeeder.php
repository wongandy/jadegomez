<?php

namespace Database\Seeders;


use Illuminate\Database\Seeder;
use App\Models\CashDenomination;

class CashDenominationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        CashDenomination::insert([
            [
                'name' => 'one thousand',
                'number' => '1000'
            ],
            [
                'name' => 'five hundred',
                'number' => '500'
            ],
            [
                'name' => 'two hundred',
                'number' => '200'
            ],
            [
                'name' => 'one hundred',
                'number' => '100'
            ],
            [
                'name' => 'fifty',
                'number' => '50'
            ],
            [
                'name' => 'twenty',
                'number' => '20'
            ],
            [
                'name' => 'ten',
                'number' => '10'
            ],
            [
                'name' => 'five',
                'number' => '5'
            ],
            [
                'name' => 'one',
                'number' => '1'
            ],
            [
                'name' => 'twenty-five cents',
                'number' => '0.25'
            ],
            [
                'name' => 'ten cents',
                'number' => '0.10'
            ],
        ]);
    }
}
