# AI_Context — Index

> Dibuat berdasarkan audit source code aktual per **2026-08-11**  
> Untuk digunakan oleh AI coding agent sebagai sumber konteks project.  
> **Jangan percaya dokumentasi lama secara langsung — selalu validasi dengan source code.**

---

## Cara Menggunakan Folder Ini

Folder `AI_Context/` berisi dokumentasi project yang dihasilkan dari audit source code aktual.  
Sebelum mengerjakan task apapun, AI agent disarankan membaca file yang relevan dari daftar di bawah.

### Quick Reference

| Butuh tahu tentang... | Baca file ini |
|---|---|
| Gambaran umum project | `00_PROJECT_OVERVIEW.md` |
| Stack teknologi & versi | `01_TECH_STACK.md` |
| Arsitektur & pattern | `02_ARCHITECTURE.md` |
| Alur bisnis lengkap | `03_BUSINESS_FLOW.md` |
| Aturan bisnis & validasi | `04_BUSINESS_RULES.md` |
| Skema database & tabel | `05_DATABASE.md` |
| Canvas editor (Fabric.js) | `06_CANVAS_EDITOR.md` |
| File mana yang mengerjakan apa | `07_FILE_MAP.md` |
| Semua URL endpoint | `08_ROUTE_MAP.md` |
| Model Eloquent & relasi | `09_MODELS.md` |
| Logic per controller | `10_CONTROLLERS.md` |
| UI components & Alpine.js | `11_FRONTEND_COMPONENTS.md` |
| Bug & inkonsistensi known | `12_DISCREPANCIES.md` |

---

## Files

| File | Deskripsi Singkat |
|---|---|
| `00_PROJECT_OVERVIEW.md` | Tujuan, scope, aktor, fitur utama |
| `01_TECH_STACK.md` | Laravel 12, Livewire, Fabric.js, Vite, semua versi |
| `02_ARCHITECTURE.md` | MVC pattern, directory structure, middleware |
| `03_BUSINESS_FLOW.md` | Step-by-step alur customer, admin, dan owner |
| `04_BUSINESS_RULES.md` | Semua business rules dengan sumber code |
| `05_DATABASE.md` | Schema tabel, tipe data, kolom, FK, history migration |
| `06_CANVAS_EDITOR.md` | Dokumentasi lengkap Fabric.js editor (1500+ baris JS) |
| `07_FILE_MAP.md` | Fitur → file mapping |
| `08_ROUTE_MAP.md` | Semua HTTP routes, method, middleware |
| `09_MODELS.md` | Eloquent models, $fillable, cast, relationships |
| `10_CONTROLLERS.md` | Logic per method controller, FormRequests |
| `11_FRONTEND_COMPONENTS.md` | Alpine.js state, UI sections, CSS classes |
| `12_DISCREPANCIES.md` | Bug known, inkonsistensi, prioritas fix |

---

## Diagrams

| File | Tipe | Deskripsi |
|---|---|---|
| `diagrams/01_usecase_diagram.puml` | Use Case | Aktor dan semua use case sistem |
| `diagrams/02_erd_diagram.puml` | ERD | Entity relationship semua tabel aktual |
| `diagrams/03_sequence_design_order.puml` | Sequence | Flow desain → cart → checkout |
| `diagrams/04_sequence_admin_review.puml` | Sequence | Flow admin review desain & pembayaran |
| `diagrams/05_state_order_status.puml` | State | State machine status order |

---

## Ringkasan Project

**Nama:** DailyCo  
**Tipe:** Aplikasi web pemesanan sablon pakaian custom  
**Stack:** Laravel 12 + Livewire + Fabric.js  
**Auth:** Multi-guard (Customer + Admin/Owner)  
**Database:** SQLite (dev) / MySQL (prod estimasi)  

### Fitur Utama
- Canvas editor multi-sisi (depan/belakang/kiri/kanan) dengan Fabric.js
- Multi-step wizard Alpine.js
- Revisi desain dengan versioning (parent_id)
- Checkout + upload bukti pembayaran manual
- Admin review desain + auto-transition status
- Owner laporan + export CSV

### Bug Prioritas Tinggi (dari 12_DISCREPANCIES.md)
1. Revenue report: `'selesai'` harus `'completed'` (ReportController)
2. Ready-made status: `'disetujui'` harus `'approved'` (CheckoutController)

---

## Catatan Konteks

- Project ini adalah **skripsi/tugas akhir** (berdasarkan nama folder)
- Livewire diinstall tapi **belum digunakan** untuk fitur bisnis
- Pricing desain custom **dinonaktifkan** (selalu Rp 0)
- Canvas editor adalah **single JavaScript file 1500+ baris** dalam Blade
- Storage menggunakan **PHP route fallback** (shared hosting pattern)
- **Tidak ada notifikasi email** atau real-time push
