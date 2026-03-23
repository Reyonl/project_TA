<x-app-layout>
    <!-- Muat Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 leading-tight">
            {{ __('Laporan Penjualan & Analitik (Owner)') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <!-- Summary KPI Cards -->
            <div class="bg-white overflow-hidden shadow-xl shadow-indigo-100/50 sm:rounded-2xl border border-slate-100 p-8">
                <h3 class="text-xl font-bold text-slate-800 mb-6 flex items-center gap-2">
                    <span>📊</span> Ringkasan Aktivitas Sistem SablonQu
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="bg-indigo-50 p-6 rounded-2xl border border-indigo-100 hover:shadow-md transition">
                        <p class="text-indigo-600 font-bold mb-2 uppercase text-xs tracking-wider">Total Pesanan</p>
                        <h4 class="text-4xl font-black text-indigo-900">{{ number_format($totalOrders) }} <span class="text-sm font-medium text-indigo-500">Order</span></h4>
                    </div>
                    
                    <div class="bg-emerald-50 p-6 rounded-2xl border border-emerald-100 hover:shadow-md transition">
                        <p class="text-emerald-600 font-bold mb-2 uppercase text-xs tracking-wider">Pendapatan Bersih (Selesai)</p>
                        <h4 class="text-4xl font-black text-emerald-900">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</h4>
                    </div>

                    <div class="bg-violet-50 p-6 rounded-2xl border border-violet-100 hover:shadow-md transition">
                        <p class="text-violet-600 font-bold mb-2 uppercase text-xs tracking-wider">Total Desain Dibuat</p>
                        <h4 class="text-4xl font-black text-violet-900">{{ number_format($totalDesains) }} <span class="text-sm font-medium text-violet-500">Desain</span></h4>
                    </div>
                </div>
            </div>

            <!-- Charts Section -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Bar Chart (Pendapatan Bulanan) -->
                <div class="md:col-span-2 bg-white shadow-xl shadow-indigo-100/50 sm:rounded-2xl border border-slate-100 p-6 flex flex-col items-center">
                    <h4 class="text-sm font-bold text-slate-500 uppercase tracking-widest mb-4 w-full text-left">📈 Grafik Pendapatan 2026</h4>
                    <div class="relative w-full h-[300px]">
                        <canvas id="revenueChart"></canvas>
                    </div>
                </div>

                <!-- Doughnut Chart (Status Pesanan) -->
                <div class="bg-white shadow-xl shadow-indigo-100/50 sm:rounded-2xl border border-slate-100 p-6 flex flex-col items-center">
                    <h4 class="text-sm font-bold text-slate-500 uppercase tracking-widest mb-4 w-full text-left">🛒 Proporsi Status Pesanan</h4>
                    <div class="relative w-full h-[250px] flex justify-center mt-4">
                        <canvas id="statusChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Statistik Aktivitas Desain -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Data Produk Terlaris -->
                <div class="bg-white shadow-xl shadow-slate-100/50 sm:rounded-2xl border border-slate-100 p-6">
                    <h4 class="text-sm font-bold text-slate-500 uppercase tracking-widest mb-4 border-b pb-2 flex items-center gap-2">
                        <span>📦</span> Top 5 Kategori Produk Terlaris
                    </h4>
                    <ul class="space-y-3">
                        @forelse($produkPopuler as $item)
                            <li class="flex items-center justify-between p-3 rounded-lg bg-slate-50 border border-slate-100 hover:bg-slate-100 transition duration-150">
                                <span class="font-bold text-slate-700">{{ $item->nama_produk }}</span>
                                <span class="text-sm font-black bg-indigo-100 text-indigo-700 px-3 py-1 rounded-lg shadow-sm border border-indigo-200">{{ $item->total_terjual }} Terjual</span>
                            </li>
                        @empty
                            <p class="text-slate-500 text-sm italic text-center py-4">Belum ada data penjualan.</p>
                        @endforelse
                    </ul>
                </div>

                <!-- Data Warna Baju Favorit -->
                <div class="bg-white shadow-xl shadow-slate-100/50 sm:rounded-2xl border border-slate-100 p-6">
                    <h4 class="text-sm font-bold text-slate-500 uppercase tracking-widest mb-4 border-b pb-2 flex items-center gap-2">
                        <span>🎨</span> Top 5 Warna Sablon Paling Diminati
                    </h4>
                    <ul class="space-y-3">
                        @forelse($warnaPopuler as $item)
                            <li class="flex items-center justify-between p-3 rounded-lg bg-slate-50 border border-slate-100 hover:bg-slate-100 transition duration-150">
                                <div class="flex items-center gap-3">
                                    <span class="w-8 h-8 rounded-full border border-slate-300 shadow-sm block" style="background-color: {{ $item->warna_baju }}"></span>
                                    <span class="font-black text-slate-700 uppercase tracking-wider text-sm">{{ $item->warna_baju }}</span>
                                </div>
                                <span class="text-sm font-black bg-emerald-100 text-emerald-700 px-3 py-1 rounded-lg shadow-sm border border-emerald-200">{{ $item->total }} Desain</span>
                            </li>
                        @empty
                            <p class="text-slate-500 text-sm italic text-center py-4">Belum ada data warna baju.</p>
                        @endforelse
                    </ul>
                </div>
            </div>

        </div>
    </div>

    <!-- Script Init Chart data dari PHP Controller -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Data Pendapatan
            const rawMonthlyData = @json($formattedMonthly);
            const monthLabels = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des'];
            const revenueValues = Object.values(rawMonthlyData);

            // Data Status
            const rawStatusData = @json($statusCounts);
            // Default mapping
            const statusMapping = {
                'keranjang': { label: 'Keranjang', color: '#94a3b8' },
                'menunggu_konfirmasi': { label: 'Menunggu', color: '#f59e0b' },
                'diproses': { label: 'Diproses', color: '#3b82f6' },
                'dikirim': { label: 'Dikirim', color: '#8b5cf6' },
                'selesai': { label: 'Selesai', color: '#10b981' },
                'dibatalkan': { label: 'Batal', color: '#ef4444' }
            };

            const statusLabels = [];
            const statusValues = [];
            const statusColors = [];

            for (const [key, value] of Object.entries(rawStatusData)) {
                if (statusMapping[key]) {
                    statusLabels.push(statusMapping[key].label);
                    statusColors.push(statusMapping[key].color);
                } else {
                    statusLabels.push(key);
                    statusColors.push('#cbd5e1');
                }
                statusValues.push(value);
            }

            // --- INIT REVENUE BAR CHART ---
            const ctxRevenue = document.getElementById('revenueChart').getContext('2d');
            
            // Gradient Setup
            let gradientStr = ctxRevenue.createLinearGradient(0, 0, 0, 400);
            gradientStr.addColorStop(0, 'rgba(79, 70, 229, 0.8)'); // Indigo-600
            gradientStr.addColorStop(1, 'rgba(79, 70, 229, 0.1)');
            
            new Chart(ctxRevenue, {
                type: 'bar',
                data: {
                    labels: monthLabels,
                    datasets: [{
                        label: 'Pendapatan (Rp)',
                        data: revenueValues,
                        backgroundColor: gradientStr,
                        borderColor: '#4f46e5',
                        borderWidth: 1,
                        borderRadius: 6,
                        barThickness: 'flex',
                        maxBarThickness: 45
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    let label = context.dataset.label || '';
                                    if (label) { label += ': '; }
                                    if (context.parsed.y !== null) {
                                        label += new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(context.parsed.y);
                                    }
                                    return label;
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: { color: '#f1f5f9', drawBorder: false },
                            ticks: { display: false } // Hide label sumbu Y panjang
                        },
                        x: {
                            grid: { display: false, drawBorder: false },
                            ticks: { font: { family: "'Inter', sans-serif" } }
                        }
                    }
                }
            });

            // --- INIT STATUS DOUGHNUT CHART ---
            const ctxStatus = document.getElementById('statusChart').getContext('2d');
            new Chart(ctxStatus, {
                type: 'doughnut',
                data: {
                    labels: statusLabels,
                    datasets: [{
                        data: statusValues,
                        backgroundColor: statusColors,
                        borderWidth: 0,
                        hoverOffset: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '70%',
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                padding: 20,
                                usePointStyle: true,
                                pointStyle: 'circle',
                                font: { family: "'Inter', sans-serif", size: 12 }
                            }
                        }
                    }
                }
            });
        });
    </script>
</x-app-layout>
