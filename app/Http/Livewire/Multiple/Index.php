<?php

namespace App\Http\Livewire\Multiple;

use Livewire\Component;
use App\Traits\HasCalculationTrait;

class Index extends Component
{
    // use HasCalculationTrait;

    public $tab = 'tab1';

    protected $listeners = [
        'refreshComponent' => '$refresh',
    ];

    public function toggleTabMultiple($tab)
    {
        $this->tab = $tab;

        if ($tab == 'tab1') {
            $this->emitTo('multiple.system', 'mount');
        }

        if ($tab == 'tab2') {
            $this->emitTo('multiple.result', 'mount');
        }
    }

    public function render()
    {
        return view('livewire.multiple.index');
    }
}
