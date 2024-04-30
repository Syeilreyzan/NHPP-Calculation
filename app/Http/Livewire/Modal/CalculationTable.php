<?php

namespace App\Http\Livewire\Modal;

use App\Traits\HasCalculationTrait;
use Illuminate\Testing\Fluent\Concerns\Has;
use LivewireUI\Modal\ModalComponent;

class CalculationTable extends ModalComponent
{
    public $slope;
    public $lambda;
    public $time;
    public $increment;
    public $tableRows;
    public $instantenousMtbfs = [];
    public $cumulativeMtbfs = [];
    public $predictedNumberFailures = [];
    public $times = [];

    public function render()
    {
        return view('livewire.modal.calculation-table');
    }

    /**
    * Supported: 'sm', 'md', 'lg', 'xl', '2xl', '3xl', '4xl', '5xl', '6xl', '7xl'
    */
    public static function modalMaxWidth(): string
    {
        return '7xl';
    }

    public function closingModal()
    {
        $this->emit('closeModal');
    }
}
