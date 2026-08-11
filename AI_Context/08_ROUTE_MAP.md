# 08 — ROUTE MAP

> **Source of Truth:** `routes/web.php`, `routes/auth.php`, `routes/settings.php`

---

## Ringkasan Route Groups

| Prefix | Guard / Middleware | Aktor |
|---|---|---|
| `/` | `guest` friendly | Publik & Customer |
| `/customer/*` | `auth:customer` | Customer |
| `/admin/*` | `auth:admin` | Admin & Owner |
| `/auth` (merge) | varies | Semua |

---

## Public Routes

| Method | URI | Controller@Action | Name |
|---|---|---|---|
| GET | `/` | WelcomeController atau closure | `welcome` (tidak dikonfirmasi nama route) |
| GET | `/katalog/{produk}` | `Customer\ProductController@show` | `katalog.show` |

---

## Customer Routes (`auth:customer`)

| Method | URI | Controller@Action | Name |
|---|---|---|---|
| GET | `/customer/dashboard` | `Customer\ProductController@index` | `customer.dashboard` |
| GET | `/customer/products/{produk}` | `Customer\ProductController@show` | `customer.products.show` |
| **Design** | | | |
| GET | `/customer/design/{produk}` | `Customer\DesignController@index` | `customer.design.editor` |
| POST | `/customer/design` | `Customer\DesignController@store` | `customer.design.store` |
| PATCH | `/customer/design/{desain}` | `Customer\DesignController@update` | `customer.design.update` |
| **Cart** | | | |
| GET | `/customer/cart` | `Customer\CartController@index` | `customer.cart.index` |
| POST | `/customer/cart/direct/{produk}` | `Customer\CartController@storeDirect` | `customer.cart.storeDirect` |
| PATCH | `/customer/cart/{cart}/quantity` | `Customer\CartController@updateQuantity` | `customer.cart.updateQuantity` |
| DELETE | `/customer/cart/{cart}` | `Customer\CartController@destroy` | `customer.cart.destroy` |
| **Checkout** | | | |
| GET | `/customer/checkout` | `Customer\CheckoutController@index` | `customer.checkout.index` |
| POST | `/customer/checkout` | `Customer\CheckoutController@store` | `customer.checkout.store` |
| **Orders** | | | |
| GET | `/customer/orders` | `Customer\OrderController@index` | `customer.orders.index` |
| GET | `/customer/orders/{id}` | `Customer\OrderController@show` | `customer.orders.show` |
| POST | `/customer/orders/{id}/payment` | `Customer\OrderController@uploadPayment` | `customer.orders.uploadPayment` |
| **API** | | | |
| GET | `/customer/api/stickers` | (closure) | - |

---

## Admin Routes (`auth:admin`)

### Admin-Only Routes (role:admin)

| Method | URI | Controller@Action | Name |
|---|---|---|---|
| **Products** | | | |
| GET | `/admin/products` | `Admin\ProductController@index` | `admin.products.index` |
| GET | `/admin/products/create` | `Admin\ProductController@create` | `admin.products.create` |
| POST | `/admin/products` | `Admin\ProductController@store` | `admin.products.store` |
| GET | `/admin/products/{product}/edit` | `Admin\ProductController@edit` | `admin.products.edit` |
| PUT/PATCH | `/admin/products/{product}` | `Admin\ProductController@update` | `admin.products.update` |
| DELETE | `/admin/products/{product}` | `Admin\ProductController@destroy` | `admin.products.destroy` |
| **Templates** | | | |
| GET | `/admin/templates` | `Admin\TemplateController@index` | `admin.templates.index` |
| GET | `/admin/templates/create` | `Admin\TemplateController@create` | `admin.templates.create` |
| POST | `/admin/templates` | `Admin\TemplateController@store` | `admin.templates.store` |
| DELETE | `/admin/templates/{template}` | `Admin\TemplateController@destroy` | `admin.templates.destroy` |
| **Orders** | | | |
| GET | `/admin/orders` | `Admin\OrderController@index` | `admin.orders.index` |
| GET | `/admin/orders/{order}` | `Admin\OrderController@show` | `admin.orders.show` |
| PATCH | `/admin/orders/{order}/status` | `Admin\OrderController@updateStatus` | `admin.orders.updateStatus` |
| PATCH | `/admin/orders/{order}/desain/{orderDetail}` | `Admin\OrderController@updateStatusDesain` | `admin.orders.updateStatusDesain` |
| PATCH | `/admin/orders/{order}/verify-payment` | `Admin\OrderController@verifyPayment` | `admin.orders.verifyPayment` |
| **Dashboard** | | | |
| GET | `/admin/dashboard` | `Admin\DashboardController@index` | `admin.dashboard` |

### Owner+Admin Routes (role:owner,admin)

| Method | URI | Controller@Action | Name |
|---|---|---|---|
| GET | `/admin/reports` | `Owner\ReportController@index` | `admin.reports.index` |
| GET | `/admin/reports` (export_csv=1) | `Owner\ReportController@exportCsv` | `admin.reports.export` |

---

## Auth Routes (`routes/auth.php`)

| Method | URI | Controller@Action | Middleware |
|---|---|---|---|
| GET | `/register` | `Auth\RegisteredUserController@create` | `guest` |
| POST | `/register` | `Auth\RegisteredUserController@store` | `guest` |
| GET | `/login` | `Auth\AuthenticatedSessionController@create` | `guest` |
| POST | `/login` | `Auth\AuthenticatedSessionController@store` | `guest` |
| GET | `/forgot-password` | `Auth\PasswordResetLinkController@create` | `guest` |
| POST | `/forgot-password` | `Auth\PasswordResetLinkController@store` | `guest` |
| GET | `/reset-password/{token}` | `Auth\NewPasswordController@create` | `guest` |
| POST | `/reset-password` | `Auth\NewPasswordController@store` | `guest` |
| GET | `/verify-email` | `Auth\EmailVerificationPromptController` | `auth` |
| GET | `/verify-email/{id}/{hash}` | `Auth\VerifyEmailController` | `auth`, `signed`, `throttle:6,1` |
| POST | `/email/verification-notification` | `Auth\EmailVerificationNotificationController@store` | `auth`, `throttle:6,1` |
| GET | `/confirm-password` | `Auth\ConfirmablePasswordController@show` | `auth` |
| POST | `/confirm-password` | `Auth\ConfirmablePasswordController@store` | `auth` |
| PUT | `/password` | `Auth\PasswordController@update` | `auth` |
| POST | `/logout` | `Auth\AuthenticatedSessionController@destroy` | (none — akses dari semua guard) |

---

## Settings Routes (`routes/settings.php`)

| Method | URI | Controller | Middleware |
|---|---|---|---|
| GET | `/settings/profile` | `ProfileController@edit` | `auth` |
| PATCH | `/settings/profile` | `ProfileController@update` | `auth` |
| DELETE | `/settings/profile` | `ProfileController@destroy` | `auth` |

> ⚠️ Settings routes menggunakan guard `auth` (web default), bukan `auth:customer` atau `auth:admin`. Ini kemungkinan adalah legacy dari Breeze starter kit. Customer tidak menggunakan ini.

---

## Utility / Debug Routes

| Method | URI | Fungsi |
|---|---|---|
| GET | `/storage/{path}` | Serve file dari storage untuk shared hosting (fallback tanpa symlink) |
| GET | `/debug-storage` | Debug route aktif — harus DIHAPUS sebelum production |

---

## Catatan Route

- Semua route customer menggunakan **named routes** dengan prefix `customer.*`
- Semua route admin menggunakan **named routes** dengan prefix `admin.*`
- Logout tidak memiliki middleware agar bisa diakses dari semua guard session
- Sticker API route adalah closure langsung di web.php, bukan controller method
- Route model binding:
  - `{produk}` → `Produk` model (by primary key `id_produk`)
  - `{desain}` → `Desain` model
  - `{order}` → `Order` model
  - `{orderDetail}` → `OrderDetail` model
  - `{product}` → `Produk` model (di admin, Laravel convention camelCase)
  - `{cart}` → `Cart` model
  - `{template}` → `Template` model
