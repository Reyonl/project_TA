<?php

namespace Tests\Feature\Admin;

use App\Models\Admin;
use App\Models\Customer;
use App\Models\Produk;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * RBAC of the CURRENT architecture: the role middleware (CheckRole) only
 * recognizes the admin guard and matches Admin->role against the route's
 * allowed roles; everything else gets 403.
 */
class ProductTemplateRbacTest extends TestCase
{
    use RefreshDatabase;

    private function adminWithRole(string $role): Admin
    {
        return Admin::create([
            'nama_admin' => ucfirst($role),
            'email' => "$role@test.com",
            'password' => Hash::make('password'),
            'role' => $role,
        ]);
    }

    public function test_admin_role_can_manage_products(): void
    {
        $this->actingAs($this->adminWithRole('admin'), 'admin')
            ->get(route('admin.products.index'))
            ->assertOk();
    }

    public function test_admin_role_can_create_products(): void
    {
        $this->actingAs($this->adminWithRole('admin'), 'admin')
            ->post(route('admin.products.store'), [
                'nama_produk' => 'Hoodie Baru',
                'jenis_produk' => 'hoodie',
                'tipe_produk' => 'kustom',
                'harga_dasar' => 120000,
            ]);

        $this->assertDatabaseHas('produks', ['nama_produk' => 'Hoodie Baru']);
    }

    public function test_product_store_validation_rejects_unknown_jenis(): void
    {
        $this->actingAs($this->adminWithRole('admin'), 'admin')
            ->post(route('admin.products.store'), [
                'nama_produk' => 'Bad',
                'jenis_produk' => 'topi', // no longer a valid jenis (kaos,hoodie,polo)
                'tipe_produk' => 'kustom',
                'harga_dasar' => 1,
            ])
            ->assertSessionHasErrors('jenis_produk');

        $this->assertDatabaseMissing('produks', ['nama_produk' => 'Bad']);
    }

    public function test_owner_role_cannot_manage_products(): void
    {
        // role:admin group excludes owner.
        $this->actingAs($this->adminWithRole('owner'), 'admin')
            ->get(route('admin.products.index'))
            ->assertForbidden();
    }

    public function test_reports_are_visible_to_owner_and_admin(): void
    {
        $this->actingAs($this->adminWithRole('owner'), 'admin')
            ->get(route('admin.report.index'))
            ->assertOk();

        $this->actingAs($this->adminWithRole('admin'), 'admin')
            ->get(route('admin.report.index'))
            ->assertOk();
    }

    public function test_customer_guard_never_passes_admin_rbac(): void
    {
        $customer = Customer::create([
            'nama_customer' => 'Intruder',
            'email' => 'intruder@test.com',
            'password' => Hash::make('password'),
            'no_hp' => '08123456789',
            'alamat' => 'Nowhere',
        ]);

        // auth:admin redirects customer sessions to login (no admin guard identity).
        $this->actingAs($customer, 'customer')
            ->get(route('admin.products.index'))
            ->assertRedirect(route('login'));
    }
}
