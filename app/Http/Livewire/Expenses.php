<?php

namespace App\Http\Livewire;

use Livewire\Component;

class Expenses extends Component
{
    public $rows = [];
    public $expenses = [];
    public $totalExpenses;

    public function render()
    {
        return view('livewire.expenses');
    }

    public function addRow()
    {
        $this->rows[] = '';
    }

    public function removeRow($row)
    {
        unset($this->rows[$row]);
        unset($this->expenses[$row]);
        $this->rows = array_values($this->rows);
        $this->expenses = array_values($this->expenses);

        $this->totalExpenses = collect($this->expenses)->sum('amount');
        $this->emit('totalExpenses', $this->totalExpenses);
    }

    public function updated($key, $value)
    {
        $parts = explode('.', $key);
        
        if ($parts[2] == 'amount') {
            if ($value < 1) {
                unset($this->expenses[$parts[1]]['amount']);
            }
        }

        $this->totalExpenses = collect($this->expenses)->sum('amount');
        $this->emit('totalExpenses', $this->totalExpenses);
    }
}
