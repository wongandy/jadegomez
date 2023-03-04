<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Branch;
use App\Models\Purchase;
use App\Models\Supplier;
use Illuminate\Database\Eloquent\Factories\Factory;

class PurchaseFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Purchase::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'supplier_id' => Supplier::inRandomOrder()->first(),
            'branch_id' => Branch::inRandomOrder()->first(),
            'user_id' => User::inRandomOrder()->first(),
            'number' => 1,
            'purchase_number' => 'PO-00000001',
            'status' => 'available',
        ];
    }
}
