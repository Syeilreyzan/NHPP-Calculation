<?php

namespace App\Http\Livewire\Single;

use App\Traits\HasCalculationTrait;
use Livewire\Component;

class Index extends Component
{
    use HasCalculationTrait;

    public $tab = 'tab1';

    protected $listeners = [
        'refreshComponent' => '$refresh',
    ];

    public function toggleTab($tab)
    {
        $this->tab = $tab;

        if ($tab == 'tab1') {
            $this->emitTo('index', 'mount');
        }

        if ($tab == 'tab2') {
            $this->emitTo('single.result', 'mount');
        }
    }

    public function render()
    {
        return view('livewire.single.index');
    }
}
