<?php

namespace App\Backend\Livewire\Gista;

use App\Backend\Models\PaymentDocument;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('frontend.layouts.gista')]
class Inbox extends Component
{
    public string $search = '';

    public function render()
    {
        $user = auth()->user();

        $query = PaymentDocument::query()->visibleTo($user)->latest();

        // Inbox hanya menampilkan dokumen yang menunggu tindakan user tersebut.
        if ($user->isChecker()) {
            $query->where('status', 'Waiting for Checker')
                  ->where('checker_id', $user->id);
        } else {
            $query->where('status', 'Waiting for Approval 1')
                  ->where('spv1_id', $user->id);
        }

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('optg_number', 'like', "%{$this->search}%")
                  ->orWhere('description', 'like', "%{$this->search}%")
                  ->orWhere('vendor_name', 'like', "%{$this->search}%");
            });
        }

        $items = $query->get();

        return view('frontend.gista.inbox', [
            'items' => $items,
        ]);
    }
}
