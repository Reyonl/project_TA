# 11 — FRONTEND COMPONENTS

> **Source of Truth:** Blade views, Alpine.js state, CSS di `_editor_styles.blade.php`  
> Komponen ini adalah komponen Blade + Alpine.js, BUKAN Livewire.

---

## Layout System

### `layouts/app.blade.php`
Layout utama yang digunakan oleh hampir semua halaman.  
- Memuat Vite assets (CSS + JS)
- Header component
- Konten `{{ $slot }}`
- Flash message handling

---

## Design Editor — Alpine.js State

Editor di `editor.blade.php` menggunakan Alpine.js `x-data`:

```javascript
Alpine.data('editorState', () => ({
    // Step wizard
    currentStep: 1,
    totalSteps: 4 atau 6,  // dari PHP $totalSteps
    
    // Canvas side
    activeSide: 'front',   // 'front', 'back', 'left', 'right'
    
    // Color picker
    baseColor: '#ffffff',  // atau dari $initialColor
    
    // Step names (dinamis berdasarkan totalSteps)
    stepNames: ['Pilih Warna', 'Desain Depan', 'Desain Belakang', ...],
    
    // Methods
    goToStep(n)     → validasi step saat ini, switch canvas side
    nextStep()      → goToStep(currentStep + 1)
    prevStep()      → goToStep(currentStep - 1)
    canGoNext()     → validasi: step 2 harus ada minimal 1 objek di canvasFront
}))
```

### Step Structure (6-step = kaos/hoodie)
| Step | Konten | activeSide |
|---|---|---|
| 1 | Pilih Warna Baju | - |
| 2 | Canvas Depan | `front` |
| 3 | Canvas Belakang | `back` |
| 4 | Canvas Kiri | `left` |
| 5 | Canvas Kanan | `right` |
| 6 | Review & Simpan | - |

### Step Structure (4-step = polo/default)
| Step | Konten | activeSide |
|---|---|---|
| 1 | Pilih Warna Baju | - |
| 2 | Canvas Depan | `front` |
| 3 | Canvas Belakang | `back` |
| 4 | Review & Simpan | - |

---

## Design Editor — UI Sections

### Left Toolbar (Tools Sidebar)
Hanya tampil di desktop. Di mobile, ada floating button dan bottom bar.

**Sections:**
1. **Tools Tab** — Upload gambar, Tambah teks, Undo/Redo
2. **Template Tab** — Grid template dari DB (data-url → addImageToCanvas)
3. **Stiker Tab** — Search input + grid hasil Iconify/DiceBear API
4. **Teks Tab** — Quick-add teks (shortcut ke addTextBtn)
5. **Layers Tab** — `#layersListContainer` (diupdate oleh JS)

### Canvas Area (Center)
- `#mockupContainer` (480×600px) → mockup image + canvas overlay
- `#canvasScalerWrapper` → wrapper untuk zoom/pan transform
- Zoom controls: `#zoomInBtn`, `#zoomOutBtn`, `#zoomResetBtn`

### Right Sidebar (Properties Panel)
- `#editorControls` → hidden sampai ada objek aktif
- `#objectSablonSizeControl` → hidden sementara
- `#textControls` → muncul untuk objek `i-text`
- `#imageControls` → muncul untuk objek `image`
- `#svgControls` → muncul untuk objek `custom-svg`
- Layer management: `#bringForwardBtn`, `#sendBackwardBtn`
- Alignment: `#alignCenterHBtn`, `#alignCenterVBtn`
- Group/Ungroup: `#groupBtn`, `#ungroupBtn` (kondisional)
- Actions: `#duplicateObjBtn`, `#deleteObjBtn`

---

## Color Picker (Warna Baju)

**Implementasi:** Grid button di Step 1 editor

**Color Map** (hardcoded di Blade):
```php
$colorMap = [
    'Maroon' => '#800000', 'Green'  => '#228B22',
    'Grey'   => '#808080', 'Army'   => '#4B5320',
    'Yellow' => '#FFD700', 'White'  => '#FFFFFF',
    'Navy'   => '#001F5B', 'Orange' => '#FF8C00',
    'Black'  => '#000000', 'Red'    => '#DC143C',
    'Blue'   => '#1E90FF', 'Mint'   => '#98FF98',
];
```

Warna dipilih → `Alpine.$data.baseColor` diupdate → CSS `background-color` + `background-blend-mode: multiply` pada mockup berubah secara reaktif.

---

## Landing Page / Katalog

**File:** `resources/views/welcome.blade.php`

**Sections:**
- Hero section
- Katalog produk (max 3 tampil, filter `jenis_produk != 'topi'`)
- "Cara Kerja" atau CTA section

---

## Customer Pages

### Dashboard (`customer.dashboard`)
- Grid produk yang bisa didesain
- Link ke "Mulai Desain" per produk

### Riwayat Pesanan (`customer.orders.index`)
- List order dengan status badge (warna berdasarkan status_order)
- Status labels dalam Bahasa Indonesia:
  - `reviewing` → "Review Desain" (amber)
  - `pending_payment` → "Menunggu Pembayaran" (orange)
  - `processing` → "Sedang Diproses" (sky)
  - `completed` → "Selesai" (emerald)
  - `cancelled` → "Dibatalkan" (rose)
- Total harga dalam Rupiah

### Detail Pesanan (`customer.orders.show`)
- Informasi order
- Per order item: nama produk, gambar desain, status desain
- Upload bukti pembayaran (form muncul jika `payment_status = 'awaiting_payment'`)
- Tombol "Edit Desain" jika `status_desain = 'revision_required'`

---

## Admin Pages

### Admin Orders (`admin.orders.index`)
- List semua order dari semua customer
- Status badge

### Admin Order Detail (`admin.orders.show`)
- Info order lengkap
- Gambar desain tiap item
- Form review desain (approve/revision_required + catatan)
- Form update status order manual
- Gambar bukti pembayaran + form verifikasi

---

## Owner Pages

### Laporan Penjualan (`admin.reports.index`)
- Date range picker (`start_date`, `end_date`)
- Kartu statistik: Total Order, Total Revenue, Total Desain, Growth %
- Grafik Chart.js:
  - Doughnut: distribusi status order
  - Bar: revenue per bulan
- DataTables: tabel semua transaksi
- Tombol Export CSV

---

## CSS Kelas Khusus Editor

Dari `_editor_styles.blade.php`:

| Class | Fungsi |
|---|---|
| `.custom-scrollbar` | Scrollbar tipis dengan accent merah |
| `.mobile-backdrop` | Overlay backdrop gelap untuk mobile properties panel |
| `.sheet-handle` | Handle drag indicator di atas properties panel mobile |
| `.editor-properties` | Styling khusus properties panel (slide dari bawah di mobile) |

---

## Google Fonts yang Diload

Dimuat via CDN di editor:
- Bebas Neue
- Dancing Script
- Lobster
- Montserrat
- Pacifico
- Playfair Display
- Roboto
- Oswald
- Anton
- (Inter untuk UI general)

---

## Responsiveness Breakpoints (TailwindCSS)

- `md:` prefix = breakpoint 768px
  - Di bawah 768px: mobile layout (bottom toolbar, slide-up properties)
  - Di atas 768px: desktop layout (sidebar kiri, panel kanan, zoom controls)
- Editor khusus: `window.innerWidth < 768` dicek di JS untuk mobile-only behavior

---

## Third-Party UI Libraries

| Library | Versi | Source | Digunakan Di |
|---|---|---|---|
| SweetAlert2 | v11 | CDN | Editor (konfirmasi, toast, error) |
| Chart.js | Tidak dikonfirmasi versi | CDN atau npm | Owner laporan (grafik) |
| DataTables | Tidak dikonfirmasi versi | CDN atau npm | Owner laporan (tabel) |

---

## Mobile-Specific Editor Behavior

- Canvas di-scale otomatis untuk fit di layar (`setupMobileCanvasScaler()`)
- Touch corners lebih besar (32px vs 14px desktop)
- Properties panel muncul dari bawah sebagai sheet
- Button mobile edit `#mobileEditBtn` → toggle properties panel
- Backdrop `#editorControlsBackdrop` → dismiss properties panel
- NO zoom/pan controls di mobile (`zoomInBtn/OutBtn disabled if < 768px`)
