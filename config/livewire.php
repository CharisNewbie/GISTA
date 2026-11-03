<?php

/*
|--------------------------------------------------------------------------
| Livewire Configuration (project overrides)
|--------------------------------------------------------------------------
| Komponen Livewire bisnis GISTA berada di App\Backend\Livewire\Gista,
| bukan di App\Livewire (default Livewire). Tanpa override ini, Livewire
| menghasilkan nama komponen dari FQCN (app.backend.livewire.gista.…)
| tetapi gagal me-resolve balik nama tersebut menjadi class saat request
| update, sehingga setiap aksi (Check PO, Lanjut, dll.) berakhir 419
| "Page Expired". Key lain di-merge dari default package.
*/

return [
    'class_namespace' => 'App\\Backend\\Livewire',
    'class_path' => app_path('Backend/Livewire'),
];
