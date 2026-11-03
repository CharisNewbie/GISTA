<?php

namespace App\Backend\Livewire\Gista;

use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('frontend.layouts.gista')]
class SelectPaymentType extends Component
{
    public function render()
    {
        return view('frontend.gista.select-payment-type');
    }
}
