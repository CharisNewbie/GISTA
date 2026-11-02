<?php

namespace App\Livewire\Gista;

use App\Models\PaymentDocument;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.gista')]
class BuktiBayar extends Component
{
    public PaymentDocument $document;

    public function mount(PaymentDocument $document): void
    {
        $this->document = $document;
    }

    public static function terbilang(float $number): string
    {
        // ponytail: recursive stdlib integer words up to trillions; upgrade if non-IDR currencies require multi-locale numfmt.
        $words = ['', 'Satu', 'Dua', 'Tiga', 'Empat', 'Lima', 'Enam', 'Tujuh', 'Delapan', 'Sembilan', 'Sepuluh', 'Sebelas'];
        $n = (int) floor($number);

        if ($n < 12) {
            $res = $words[$n];
        } elseif ($n < 20) {
            $res = self::terbilang($n - 10) . ' Belas';
        } elseif ($n < 100) {
            $res = self::terbilang((int)($n / 10)) . ' Puluh ' . self::terbilang($n % 10);
        } elseif ($n < 200) {
            $res = 'Seratus ' . self::terbilang($n - 100);
        } elseif ($n < 1000) {
            $res = self::terbilang((int)($n / 100)) . ' Ratus ' . self::terbilang($n % 100);
        } elseif ($n < 2000) {
            $res = 'Seribu ' . self::terbilang($n - 1000);
        } elseif ($n < 1000000) {
            $res = self::terbilang((int)($n / 1000)) . ' Ribu ' . self::terbilang($n % 1000);
        } elseif ($n < 1000000000) {
            $res = self::terbilang((int)($n / 1000000)) . ' Juta ' . self::terbilang($n % 1000000);
        } elseif ($n < 1000000000000) {
            $res = self::terbilang((int)($n / 1000000000)) . ' Miliar ' . self::terbilang($n % 1000000000);
        } else {
            $res = self::terbilang((int)($n / 1000000000000)) . ' Triliun ' . self::terbilang($n % 1000000000000);
        }

        return trim(preg_replace('/\s+/', ' ', $res));
    }

    public function render()
    {
        $terbilangText = '#' . ucwords(strtolower(self::terbilang($this->document->total_payment))) . ' Rupiah#';

        return view('livewire.gista.bukti-bayar', [
            'terbilang' => $terbilangText,
        ]);
    }
}
