<?php

namespace App\Livewire\Gista;

use App\Models\PaymentDocument;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.gista')]
class Inbox extends Component
{
    public string $search = '';

    public function render()
    {
        $query = PaymentDocument::whereIn('status', [
            'Waiting for Approval 1',
            'Waiting for Approval 2',
            'Waiting for Checker',
        ])->latest();

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('optg_number', 'like', "%{$this->search}%")
                  ->orWhere('description', 'like', "%{$this->search}%")
                  ->orWhere('vendor_name', 'like', "%{$this->search}%");
            });
        }

        $items = $query->get();

        return view('livewire.gista.inbox', [
            'items' => $items,
        ]);
    }
}
