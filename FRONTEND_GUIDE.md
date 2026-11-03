# Panduan Frontend Developer (PAYDUE App)

Area kerja frontend berada di folder **`resources/`** dan konfigurasi asset. Temanmu tidak perlu menyentuh file logic PHP/backend (`app/Backend`, `database/`, dsb).

---

## 📁 Wilayah Kerja Frontend

| Folder / File | Fungsi |
| :--- | :--- |
| `resources/views/frontend/gista/` | **Halaman UI Utama**: Form, tabel, inbox, bukti bayar |
| `resources/views/frontend/layouts/` | **Master Layout**: Header, sidebar, footer |
| `resources/views/components/` | **Komponen Blade**: Modal, button, input form, dropdown |
| `resources/css/` & `resources/js/` | **Styling & Script**: CSS Tailwind & JavaScript |
| `tailwind.config.js` | Konfigurasi tema, font, dan warna Tailwind |

---

## 🚀 Cara Menjalankan untuk Frontend Dev

1. **Install dependensi frontend**:
   ```bash
   npm install
   ```
2. **Jalankan Vite (Hot Reload UI & CSS)**:
   ```bash
   npm run dev
   ```
   *(Server backend dijalankan terpisah via `php artisan serve`)*

3. **Build hasil akhir untuk deployment**:
   ```bash
   npm run build
   ```
   *(Output otomatis masuk ke folder `public/build/`)*

---

## ⚠️ Aturan Penting Saat Merapikan UI

1. **Jaga Tag Livewire**: Jangan hapus atribut `wire:model`, `wire:click`, atau `@entangle` yang ada di dalam elemen HTML/Blade karena itu jembatan komunikasi ke data backend.
2. **Styling Utama**: Gunakan utilitas Tailwind CSS yang sudah terpasang.
3. **Logic Backend**: Logika database/validasi berada di `app/Backend/`, jadi frontend murni fokus pada markup, styling, dan interaksi UI.
