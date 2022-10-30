<?php

namespace App\Http\Livewire;

use Livewire\Component;

class Checks extends Component
{
    public $rows = [];
    public $checks = [];
    public $totalChecks;

    public function render()
    {
        return view('livewire.checks');
    }

    public function addRow()
    {
        $this->rows[] = '';
    }

    public function removeRow($row)
    {
        unset($this->rows[$row]);
        unset($this->checks[$row]);
        $this->rows = array_values($this->rows);
        $this->checks = array_values($this->checks);

        $this->totalChecks = collect($this->checks)->sum('amount');
        $this->emit('totalChecks', $this->totalChecks);
    }

    public function updated($key, $value)
    {
        $parts = explode('.', $key);

        if ($parts[2] == 'amount') {
            if ($value < 1) {
                unset($this->checks[$parts[1]]['amount']);
            }
        }

        $this->totalChecks = collect($this->checks)->sum('amount');
        $this->emit('totalChecks', $this->totalChecks);
    }
}
