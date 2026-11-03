<?php

namespace App\Backend\Livewire\Gista;

use App\Backend\Models\PaymentDocument;
use Carbon\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('frontend.layouts.gista')]
class DashboardPayment extends Component
{
    public function render()
    {
        $today = Carbon::today();
        $h1 = Carbon::today()->addDay();
        $h2 = Carbon::today()->addDays(2);

        $query = PaymentDocument::query()->visibleTo(auth()->user());

        $todayCount = (clone $query)->whereDate('due_date', $today)->count();
        $h1Count = (clone $query)->whereDate('due_date', $h1)->count();
        $h2Count = (clone $query)->whereDate('due_date', $h2)->count();

        $outstanding = (clone $query)->where('status', 'Waiting for Approval 2')
            ->latest()
            ->get();

        return view('frontend.gista.dashboard-payment', [
            'todayCount' => $todayCount,
            'h1Count' => $h1Count,
            'h2Count' => $h2Count,
            'outstanding' => $outstanding,
        ]);
    }
}
