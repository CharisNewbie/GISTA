<?php

namespace App\Livewire\Gista;

use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.gista')]
class SelectPaymentType extends Component
{
    public function render()
    {
        return view('livewire.gista.select-payment-type');
    }
}
