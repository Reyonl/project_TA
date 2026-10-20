<?php

namespace Tests\Feature\Customer;

use App\Models\Customer;
use App\Models\Desain;
use App\Models\Produk;
use Database\Seeders\AdminSeeder;
use Database\Seeders\ProdukSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CanvasEditorIndustrialToolsTest extends TestCase
{
    use RefreshDatabase;

    protected Customer $customer;

    protected Produk $produk;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([
            AdminSeeder::class,
            ProdukSeeder::class,
        ]);

        $this->customer = Customer::create([
            'nama_customer' => 'Customer Sablon',
            'email' => 'sablon@test.com',
            'password' => bcrypt('password'),
            'no_hp' => '081234567890',
            'alamat' => 'Jl. Sablon No. 10',
        ]);

        $this->produk = Produk::where('jenis_produk', 'kaos')->firstOrFail();
    }

    public function test_authenticated_customer_can_render_editor_with_industrial_sablon_tools(): void
    {
        $response = $this->actingAs($this->customer, 'customer')
            ->get(route('customer.designs.editor', $this->produk->id_produk));

        $response->assertStatus(200);

        // Verify Tab Analisis Sablon exists
        $response->assertSee("activeTab = 'analysis'", false);
        $response->assertSee('Pemeriksaan kualitas & warna sablon', false);
        $response->assertSee('Warna Sablon Terdeteksi', false);
        $response->assertSee('Rekomendasi Teknik:', false);
        $response->assertSee('Dimensi Cetak Aktual', false);

        // Verify Safety Bleed Margin & Guide Box exists
        $response->assertSee('id="safetyBleedBox"', false);
        $response->assertSee('Batas Aman Jahitan', false);

        // Verify Real-time Floating Dimension Badge exists
        $response->assertSee('id="activeObjectDimensionBadge"', false);
        $response->assertSee('id="dimensionBadgeText"', false);

        // Verify Real-world Physical Dimension Card in Properties Panel exists
        $response->assertSee('id="objectRealDimensionsCard"', false);
        $response->assertSee('id="objectDpiStatusBadge"', false);
        $response->assertSee('id="objPropWidthCm"', false);
        $response->assertSee('id="objPropHeightCm"', false);

        // Verify Industrial JS calculation functions are injected
        $response->assertSee('window.getPhysicalScaleCmPerPx', false);
        $response->assertSee('window.updateLiveObjectDimensions', false);
        $response->assertSee('window.hideLiveObjectDimensions', false);
        $response->assertSee('window.runPrintAnalysis', false);
    }

    public function test_guest_is_redirected_when_accessing_canvas_editor(): void
    {
        $response = $this->get(route('customer.designs.editor', $this->produk->id_produk));

        $response->assertRedirect('/login');
    }

    public function test_customer_cannot_access_revision_editor_of_another_customer(): void
    {
        $otherCustomer = Customer::create([
            'nama_customer' => 'Customer Lain',
            'email' => 'lain@test.com',
            'password' => bcrypt('password'),
            'no_hp' => '08987654321',
            'alamat' => 'Jl. Lain No. 9',
        ]);

        $otherDesain = Desain::create([
            'id_customer' => $otherCustomer->id_customer,
            'file_desain' => 'designs/sample.png',
            'warna_baju' => '#ffffff',
            'harga_desain' => 0,
        ]);

        $response = $this->actingAs($this->customer, 'customer')
            ->get(route('customer.designs.editor', [
                'produk' => $this->produk->id_produk,
                'revisi' => $otherDesain->id_desain,
            ]));

        $response->assertStatus(403);
    }
}
