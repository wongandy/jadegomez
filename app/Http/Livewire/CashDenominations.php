<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\CashDenomination;

class CashDenominations extends Component
{
    public $cashDenominations;
    public $cashes = [];
    public $totalCashes;
    
    public function mount()
    {
        $this->cashDenominations = CashDenomination::select('id', 'name', 'number')->get();
    }
    
    public function render()
    {
        return view('livewire.cash-denominations');
    }

    public function updated($key, $value)
    {
        $parts = explode('.', $key);

        if ($value < 1) {
            unset($this->cashes[$parts[1]]);
        }
        else {
            $this->cashes[$parts[1]]['number'] = $this->cashDenominations->firstWhere('id', $parts[1])->number;
            $this->cashes[$parts[1]]['amount'] = $this->cashes[$parts[1]]['number'] * $this->cashes[$parts[1]]['pieces'];
        }

        $this->totalCashes = collect($this->cashes)->sum('amount');
        $this->emit('totalCashes', $this->totalCashes);
    }
}
