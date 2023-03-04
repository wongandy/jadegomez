<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\CashDenomination;

class LiquidationForm extends Component
{
    public $totalChecks;
    public $totalExpenses;
    public $totalCashes;
    public $totalSales;

    protected $listeners = ['totalChecks', 'totalExpenses', 'totalCashes'];

    public function render()
    {
        return view('livewire.liquidation-form');
    }

    public function totalChecks($totalChecks)
    {
        $this->totalChecks = $totalChecks;
        $this->calculateTotalSales();
    }

    public function totalExpenses($totalExpenses)
    {
        $this->totalExpenses = $totalExpenses;
        $this->calculateTotalSales();
    }

    public function totalCashes($totalCashes)
    {
        $this->totalCashes = $totalCashes;
        $this->calculateTotalSales();
    }

    public function calculateTotalSales()
    {
        $this->totalSales = $this->totalChecks + $this->totalExpenses + $this->totalCashes;
    }
}
