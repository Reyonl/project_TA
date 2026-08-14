# 06 — CANVAS EDITOR

> **Source of Truth:** `_editor_scripts.blade.php` (1534 baris), `_canvas_editor.blade.php` (406 baris), `fabric-smart-guides.js`, `editor.blade.php`  
> Ini adalah dokumentasi paling detail dari semua fitur editor.

---

## Overview Arsitektur Canvas Editor

Canvas Editor adalah fitur utama aplikasi, diimplementasikan sebagai:
- **Blade views** terpisah via `@include()`
- **JavaScript inline** dalam `<script>` tag di dalam Blade
- **Fabric.js** (local bundle di `public/js/fabric.min.js`) untuk canvas manipulation
- **Alpine.js** untuk state wizard (step, warna, sisi aktif)
- Satu instance `initFabricEditor()` function yang mencakup semua fitur

---

## File Structure Editor

```
editor.blade.php                 → Wrapper utama (Alpine.js x-data, step wizard)
  @include → _canvas_editor.blade.php   → HTML layout: sidebar, canvas area, properties panel
  @include → _editor_scripts.blade.php  → JavaScript: semua logic Fabric.js
  @include → _editor_styles.blade.php   → CSS khusus editor
  <script> → fabric.min.js             → Fabric.js library (local)
             fabric-smart-guides.js    → Smart guidelines (loaded di dalam _canvas_editor.blade.php)
```

---

## Inisialisasi Canvas

### Fabric.js Canvas Init
```javascript
// Dibuat di initFabricEditor() saat DOMContentLoaded

fabric.devicePixelRatio = devicePixelRatio > 2 ? devicePixelRatio : 3;

// 4 canvas independen (satu per sisi)
const canvasFront = new fabric.Canvas('tshirt-canvas-front', {
    preserveObjectStacking: true,
    selection: true,
    enableRetinaScaling: true,
    imageSmoothingEnabled: true
});
const canvasBack  = new fabric.Canvas('tshirt-canvas-back', {...});
let canvasLeft    = null; // Hanya ada jika ada elemen #tshirt-canvas-left di DOM
let canvasRight   = null;

window.activeCanvas = canvasFront; // Global reference ke canvas aktif
```

### Canvas ID ↔ DOM
```
id="tshirt-canvas-front"  → always rendered
id="tshirt-canvas-back"   → always rendered
id="tshirt-canvas-left"   → hanya jika ada (produk kaos/hoodie)
id="tshirt-canvas-right"  → hanya jika ada (produk kaos/hoodie)
```

### Global Object Styles (Default semua objek Fabric)
```javascript
fabric.Object.prototype.transparentCorners = false;
fabric.Object.prototype.cornerColor = '#ffffff';
fabric.Object.prototype.cornerStrokeColor = '#bae6fd';
fabric.Object.prototype.borderColor = '#0284c7';
fabric.Object.prototype.cornerSize = mobile ? 24 : 14;
fabric.Object.prototype.touchCornerSize = 32;
fabric.Object.prototype.cornerStyle = 'circle';
fabric.Object.prototype.borderDashArray = [4, 4];
fabric.Object.prototype.objectCaching = false;
// Control visibility: hide mid-side handles (mt, mb, ml, mr)
// Only corner handles (tl, tr, bl, br) + rotate (mtr) visible
```

---

## Mockup & Print Area (Safe Area)

### Mockup Container
```html
<div id="mockupContainer" style="width: 480px; height: 600px;">
```
Mockup container selalu berukuran tetap 480×600px (screen pixels).

### Mockup Image (Background)
Ditentukan dari PHP berdasarkan `$produk->jenis_produk` dan nama produk:
```php
$mockupBase = match($produk->jenis_produk) {
    'kaos'    => $isPanjang ? 'kaos_panjang' : 'kaos',
    'hoodie'  => 'hoodie',
    'polo'    => 'polo',
    'seragam' => 'seragam',
    default   => 'kaos'
};
```

File mockup di `public/images/mockups/`:
- `{mockupBase}.png` → depan
- `{mockupBase}_belakang.png` → belakang
- `{mockupBase}_samping_kiri.png` → kiri
- `{mockupBase}_samping_kanan.png` → kanan

### Multiply Blend Compositing (Mockup Warna)
```css
/* CSS di Alpine x-data binding */
background-color: {baseColor}     /* warna baju yang dipilih */
background-image: url({mockupUrl})
background-blend-mode: multiply   /* blend warna dengan mockup */

/* Masking agar hanya area baju yang terlihat */
-webkit-mask-image: url({mockupUrl})
mask-image: url({mockupUrl})
/* mask-size: contain, mask-position: center */
```

**Cara kerja:** Mockup PNG (baju putih dengan background transparan) di-blend dengan warna base menggunakan mode `multiply`. Ketika warna putih → warna baju sesuai `baseColor`. Masking memastikan hanya silhouette baju yang tampil.

### Print Area (Safe Area / Area Cetak)
```php
// Ditentukan server-side dari $getPrintArea($side)
$printAreaDims[$side] = [
    'width'  => ...,  // pixel width canvas
    'height' => ...,  // pixel height canvas  
    'top'    => ...,  // posisi dari atas mockupContainer
    'left'   => ...,  // posisi dari kiri mockupContainer
    'label'  => '...' // label area
];
```

Default print areas:
| Sisi | width | height | top | left | Label |
|---|---|---|---|---|---|
| front/back (default) | 220px | 320px | 120px | 130px | Area Cetak |
| front polo | 90px | 90px | 160px | 265px | Pocket |
| front seragam | 100px | 100px | 180px | 135px | Dada |
| left/right | 140px | 320px | 140px | 170px | Samping |

```javascript
// Tersedia global di window.printAreaDims
window.printAreaDims = {
    front: { width, height, top, left },
    back: { ... },
    left: { ... },
    right: { ... }
};
```

Print area divisualisasikan sebagai `div#printAreaBox` dengan border dashed. Border berubah warna ke merah jika objek menyentuh batas.

### Posisi Fabric Canvas
```html
<!-- Canvas HTML element diposisikan ABSOLUTE dalam mockupContainer -->
<div class="absolute z-20" 
     style="top:{pa.top}px; left:{pa.left}px; width:{pa.width}px; height:{pa.height}px;"
     x-show="activeSide === 'front'">
    <canvas id="tshirt-canvas-front" width="{pa.width}" height="{pa.height}"></canvas>
</div>
```

Artinya: **Fabric canvas coordinate (0,0) = pojok kiri atas Print Area**, bukan pojok kiri atas mockup container.

---

## Object Types

| Type | Fabric Type | customType | Dibuat Oleh |
|---|---|---|---|
| Teks | `i-text` | - | `addTextBtn` click |
| Gambar upload | `image` | `custom-image` | `imageLoader` input |
| Template | `image` | `custom-image` | `.template-item` click |
| Stiker Iconify (SVG) | `group` atau `path` | `custom-svg` | sticker click (addSVGToCanvas) |
| Stiker DiceBear (PNG) | `image` | `custom-image` | sticker click (addImageToCanvas) |

### Custom Properties per Object
- `obj.sablonSize` — ukuran sablon: `'a5'`, `'a4'`, `'a3'` (disimpan ke JSON)
- `obj.customType` — `'custom-image'` atau `'custom-svg'`
- `obj.locked` — boolean flag untuk lock layer
- `obj.isClone` — boolean flag sementara saat paste (tidak disimpan)

---

## Object Management

### Tambah Teks
```javascript
const text = new fabric.IText('Teks Anda', {
    left: canvasWidth/2,
    top: 40,
    originX: 'center',
    originY: 'center',
    textAlign: 'center',
    fontFamily: 'Arial',
    fill: '#000000',
    fontSize: 40,
    fontWeight: 'bold',
    sablonSize: 'a5'   // custom property
});
```

### Upload Gambar
```javascript
// imageLoader input[type=file] → FileReader → fabric.Image
// Validasi: PNG, JPG, SVG (client-side)
// Scale jika lebih lebar dari canvas
img.customType = 'custom-image';
img.sablonSize = 'a4';
```

### Add Template/Stiker SVG
```javascript
fabric.loadSVGFromURL(url, function(objects, options) {
    const group = fabric.util.groupSVGElements(objects, options);
    group.scale(60 / Math.max(group.width, group.height));
    group.customType = 'custom-svg';
    group.sablonSize = 'a5';
});
```

### Auto-resize Saat Objek Ditambahkan
```javascript
// Event: object:added
// Panggil resizeObjectToSablonSize() kecuali saat isHistoryAction atau isLoadingJSON
const size = obj.sablonSize || (isText/isSVG ? 'a5' : 'a4');
window.resizeObjectToSablonSize(obj, size);
```

### Resize ke Ukuran Sablon
```javascript
// resizeObjectToSablonSize(obj, size) — size: 'a5', 'a4', 'a3'
// Kalkulasi pixel per cm berdasarkan pa_width / 28 (asumsi 28cm untuk kaos)
// (polo/seragam: pa_width / 10)
// A5 (logo) = 10×10 cm, A4 = 20×25 cm, A3 = 25×35 cm
// Scale proporsional, clamped ke batas print area
```

### Constraint Object ke Print Area
```javascript
// Event: object:moving, object:scaling → constrainObjectBounds()
// Cek boundingRect vs print area dimensions
// Jika keluar batas: koreksi posisi (snap back)
// SweetAlert toast "Objek menyentuh batas area cetak"
// Flash printAreaBox border merah
```

---

## Delete, Duplicate, Layer

### Delete
- Button `#deleteObjBtn` → `activeCanvas.remove(activeObj)`
- Keyboard `Delete` / `Backspace` (bukan saat editing text) → delete active object

### Duplicate
- Button `#duplicateObjBtn` → `activeObj.clone()` dengan offset (+10, +10)
- `Ctrl+C` / `Ctrl+V` → copy-paste dengan clipboard `window._clipboard`

### Layer Management
- `#layersListContainer` → dirender ulang tiap ada perubahan canvas
- `bringForwardBtn` → `canvas.bringForward(obj)`
- `sendBackwardBtn` → `canvas.sendBackwards(obj)`
- Toggle lock: `obj.locked`, set `selectable/evented/lockMovement*` semua ke false saat locked

---

## Text Properties (Properties Panel)

Tersedia ketika objek `i-text` dipilih:
- `fontFamily` — pilihan dari Google Fonts: Arial, Roboto, Montserrat, Bebas Neue, Impact, Oswald, Anton, Pacifico, Lobster, Dancing Script, Playfair Display
- `textAlign` — left, center, right
- `lineHeight` — range 0.5 - 3.0
- `charSpacing` — range -50 sampai 300
- `fill` (warna) — color picker
- `stroke` (outline) + `strokeWidth` — color picker + range 0-10
- `shadow` — toggle on/off (blur:4, offset:2,2)

---

## Image Properties (Properties Panel)

Tersedia ketika objek `image` (custom-image) dipilih:
- **Hapus Latar** — `fabric.Image.filters.RemoveColor` dengan target warna + toleransi 0-50%
- **Auto-detect warna bg** — baca pixel [0,0] dari image element
- **Undo filter** — hapus semua filter RemoveColor
- **Opacity** — range 0-100%
- **Flip Horizontal / Vertikal**

---

## SVG Properties (Properties Panel)

Tersedia ketika objek `custom-svg` dipilih:
- **Warna Vektor** — terapkan rekursif ke semua child objects (fill + stroke)

---

## Zoom & Pan

### Zoom
- **Mouse wheel** (desktop only): Dipasang pada `#canvasScalerWrapper` dengan kompensasi posisi mouse `P_new = P_old - (k - 1) * (M - P_old)`, range 0.4x - 3.5x.
- **Zoom In/Out buttons** (`#zoomInBtn`, `#zoomOutBtn`): faktor 1.2x.
- **Reset button** (`#zoomResetBtn`): kembali ke 1.0x, reset pan (0,0).
- Zoom diterapkan ke `mockupContainer.style.transform` (seluruh visual mockup kaos + area cetak + canvas).

### Pan (VIEWPORT PAN — BUKAN OBJECT MOVEMENT)
```
Space key held down → Pan Mode aktif (_panIsActive = true)
```

**Implementasi Pan:**
1. `Space keydown` → `_panActivate()`:
   - Tambahkan class `is-panning` ke `document.body` (semua elemen menampilkan cursor `grab !important`).
   - Set `canvas.selection = false` untuk semua canvas.
   - Set `canvas.upperCanvasEl.style.pointerEvents = 'none'` (Fabric.js tidak menerima event klik objek).
2. Mouse down (ketika pan aktif) → `_panIsDragging = true`, tambah class `is-panning-dragging` (`cursor: grabbing !important`, `user-select: none`).
3. Mouse move → update `window._mockupPanX/Y += (dx, dy)` → update `mockupContainer.style.transform`.
4. Mouse up → `_panIsDragging = false`, lepas class `is-panning-dragging`, cursor kembali `grab`.
5. `Space keyup` / `window blur` → `_panDeactivate()`:
   - Lepas class `is-panning` dan `is-panning-dragging` dari `document.body`.
   - Restore `canvas.selection = true`.
   - Restore `pointerEvents = ''`.
   - Cursor kembali normal.

**PENTING — Isolation Pan dari Object Movement:**
Setting `upperCanvasEl.style.pointerEvents = 'none'` memastikan Fabric.js TIDAK menerima mouse events selama pan mode. Pan memindahkan viewport container (`mockupContainer`), bukan objek di atas canvas.

Middle mouse button (button=1) juga mengaktifkan drag pan.

### Mobile Scaling
```javascript
// setupMobileCanvasScaler()
// Jika layar < 768px: scale mockupContainer agar muat di layar
// scale = min(availableWidth/480, availableHeight/600, 1.0)
// Tidak ada pan di mobile
```

---

## Smart Guidelines (Snapping)

**File:** `public/js/fabric-smart-guides.js`  
**Inisialisasi:** `initAligningGuidelines(canvas)` dipanggil untuk setiap canvas instance.

**Cara Kerja:**
- Pada event `object:moving`, dibandingkan posisi objek dengan:
  1. Center canvas (horizontal dan vertikal)
  2. Tepi dan center semua objek lain di canvas
- Jika dalam range `aligningLineOffset = 3px`, objek di-snap (snap ke position)
- Garis panduan digambar di selection context canvas dengan warna magenta `rgb(255,0,255)`
- Garis dashed (4px dash, 4px gap)
- Pada `after:render`, garis dihapus

**Penting:** Smart guidelines adalah **visual helper saja** — tidak disimpan ke DB.  
Garis ada HANYA selama objek digerakkan, dihapus setelah.

---

## Alignment Tools

- **Tengah Horizontal** (`alignCenterHBtn`): `obj.set({left: printArea.width/2, originX: 'center'})`
- **Tengah Vertikal** (`alignCenterVBtn`): `obj.set({top: printArea.height/2, originY: 'center'})`
- Alignment relatif terhadap dimensi **print area**, bukan canvas/viewport

---

## Undo / Redo

```javascript
// History stored per-side (front/back/left/right)
window.historyStates = {
    front: { undo: [], redo: [] },
    back: { undo: [], redo: [] },
    left: { undo: [], redo: [] },
    right: { undo: [], redo: [] }
};

// window.saveHistory() → push JSON snapshot ke undo stack, clear redo
// window.undoHistory() → pop dari undo, push ke redo, load previous state
// window.redoHistory() → pop dari redo, push ke undo, load next state
```

Keyboard: `Ctrl+Z` (undo), `Ctrl+Y` / `Ctrl+Shift+Z` (redo)  
Buttons: `#btnUndoAction`, `#btnRedoAction` (disabled jika stack kosong)

History disimpan sebagai JSON string dari `canvas.toJSON(['customType', 'sablonSize'])`.

---

## Save Canvas (Submit ke Server)

**Trigger:** Button `#saveDesignBtn` di Step Review

**Proses:**
1. Discard active object di semua canvas
2. Validasi: harus ada minimal 1 objek di semua canvas gabungan
3. Export setiap canvas yang ada objeknya ke dataURL PNG (`multiplier: 4` untuk resolusi tinggi)
4. Export canvas JSON (`toJSON(['customType', 'sablonSize'])`) untuk setiap sisi
5. Kumpulkan raw asset URLs dari semua objek `custom-image`
6. Kirim via `fetch()` sebagai JSON

**Payload:**
```javascript
{
    _token: csrf_token,
    id_produk: id_produk,
    file_desain: base64DataURL,           // wajib (depan)
    file_desain_belakang: base64 | '',    // kosong string jika tidak ada objek
    file_desain_kiri: base64 | '',
    file_desain_kanan: base64 | '',
    canvas_front: JSON.stringify(fabricJSON),  // untuk resume edit
    canvas_back: JSON | null,
    canvas_left: JSON | null,
    canvas_right: JSON | null,
    warna_baju: '#hexcolor',
    raw_assets: [url1, url2, ...],
    harga_desain: 0,                      // selalu 0 saat ini
    detail_sablon: 'Depan: Teks... '      // deskripsi objek per sisi
}
```

**Mode Revisi:** Jika `$desainRevisi` ada, URL: `PATCH /customer/design/{id}` (method override `_method: 'PATCH'`)  
**Mode Baru:** `POST /customer/design`

---

## Load Canvas (Resume Edit / Revisi)

```javascript
const savedData = {
    front: {!! $desainRevisi->canvas_front !!},   // JSON string
    back:  {!! $desainRevisi->canvas_back  !!},
    left:  {!! $desainRevisi->canvas_left  !!},
    right: {!! $desainRevisi->canvas_right !!}
};

// safeLoadCanvas(canvas, jsonStr):
//   Parse JSON → canvas.loadFromJSON(obj, callback)
//   Set isLoadingJSON=true selama proses
//   Setelah load: renderAll, renderLayersList, saveHistory
```

---

## Preview Generation (Step Review)

```javascript
// compositePreview(targetImgId, fabricCanvas, mockupUrl, hasDesign)
// Menggunakan offscreen canvas (300×375px):
// 1. Fill background abu-abu
// 2. Load mockup image
// 3. Buat tint canvas: fill baseColor → multiply blend mockup → destination-in masking
// 4. Overlay design (toDataURL dari fabricCanvas, scale 45%, posisi tengah)
// Hasilnya: data URL PNG → set ke img.src

window.generatePreviews = function() {
    compositePreview('preview-front', canvasFront, mockupFrontUrl, true);
    compositePreview('preview-back', canvasBack, mockupBackUrl, hasObjects);
    // left & right jika ada
};
```

Preview di-generate saat masuk Step Review (via Alpine.js effect watcher).

---

## Keyboard Shortcuts

| Shortcut | Aksi |
|---|---|
| `Space` (held) | Aktifkan Pan Mode |
| `Delete` / `Backspace` | Hapus objek aktif |
| `Ctrl+Z` | Undo |
| `Ctrl+Y` / `Ctrl+Shift+Z` | Redo |
| `Ctrl+C` | Copy objek aktif ke clipboard |
| `Ctrl+V` | Paste dari clipboard (+10 offset) |

---

## Sticker Search (Iconify + DiceBear)

```javascript
// loadStickers(query)
// fetch /customer/api/stickers?q={query}
// Server: proxy ke Iconify API (limit 36 ikon) + 2 DiceBear styles
// Render ke #stickersContainer
// Iconify SVG → addSVGToCanvas()
// DiceBear PNG → addImageToCanvas()
// Fallback (jika fetch gagal): 6 ikon hardcoded dari Iconify CDN
```

---

## Background Removal (Hapus Latar Gambar)

Menggunakan built-in Fabric.js filter `RemoveColor`:
```javascript
o.filters.push(new fabric.Image.filters.RemoveColor({
    color: targetHexColor,
    distance: toleransi / 100    // 0.0 - 0.5
}));
o.applyFilters();
```

Auto-detect: baca pixel [0,0] gambar untuk menebak warna background.

---

## Mobile Responsiveness

- Toolbar (Tools, Upload, Stiker, Teks, Layers) dipindahkan ke bottom fixed bar di mobile
- Sidebar panel muncul dari bawah (slide up animation)
- Properties panel: hanya muncul saat tap ikon Edit (button `#mobileEditBtn`)
- Mobile canvas: scale-fit ke layar, tanpa zoom/pan controls
- Touch corner size lebih besar: 32px

---

## Dependency Antar Fitur

```
initFabricEditor()
├── requires: window.printAreaDims (dari Blade PHP)
├── requires: fabric.js loaded (sync script)
├── requires: fabric-smart-guides.js loaded (sync, sebelum init)
│
├── window.activeCanvas → switching sisi
├── window.saveHistory → dipanggil oleh: object:added, object:removed, object:modified
├── window.recalculateTotalPrice → dipanggil oleh: object:added/removed, undo/redo
├── window.renderLayersList → dipanggil oleh: selection events, object events
├── window.generatePreviews → dipanggil oleh: goToStep (last step)
├── window.setupMobileCanvasScaler → dipanggil oleh: resize, orientationchange
└── Alpine.js `activeSide`, `currentStep` → dikonsumsi oleh fungsi-fungsi yang perlu tahu sisi aktif
```
