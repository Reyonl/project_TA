<x-app-layout>
    <!-- Muat Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <x-slot name="header">
        <h2 class="font-black text-2xl text-slate-800 leading-tight font-outfit uppercase tracking-tight">
            {{ __('Analytics & Performance') }} <span class="text-sky-600">(Owner)</span>
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <!-- Summary KPI Cards -->
            <div class="bg-white overflow-hidden shadow-[0_20px_50px_rgba(8,_112,_184,_0.05)] sm:rounded-[2.5rem] border border-slate-100 p-10 relative overflow-hidden group">
                <div class="absolute -top-10 -right-10 w-40 h-40 bg-sky-50 rounded-full blur-3xl opacity-50 group-hover:scale-150 transition-transform duration-1000"></div>
                
                <h3 class="text-xl font-black text-slate-800 mb-8 flex items-center gap-3 font-outfit uppercase tracking-tight relative z-10">
                    <span class="w-10 h-10 bg-sky-600 rounded-xl flex items-center justify-center text-white shadow-lg shadow-sky-100">📊</span>
                    Ringkasan Aktivitas <span class="text-sky-600">DAILY.CO</span>
                </h3>
 
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8 relative z-10">
                    <div class="bg-sky-50 p-8 rounded-3xl border border-sky-100 hover:shadow-2xl hover:shadow-sky-100 transition-all duration-300 group/kpi">
                        <p class="text-sky-600 font-black mb-3 uppercase text-[10px] tracking-[0.2em]">Total Pesanan</p>
                        <h4 class="text-4xl font-black text-slate-900 font-outfit">{{ number_format($totalOrders) }} <span class="text-xs font-bold text-sky-400 italic">Order</span></h4>
                    </div>
                    
                    <div class="bg-emerald-50 p-8 rounded-3xl border border-emerald-100 hover:shadow-2xl hover:shadow-emerald-100 transition-all duration-300 group/kpi">
                        <p class="text-emerald-600 font-black mb-3 uppercase text-[10px] tracking-[0.2em]">Pendapatan Bersih</p>
                        <h4 class="text-4xl font-black text-slate-900 font-outfit italic tracking-tighter">Rp {{ number_format($totalRevenue/1000, 0, ',', '.') }}<span class="text-sm font-bold text-emerald-400 uppercase">k</span></h4>
                    </div>
 
                    <div class="bg-violet-50 p-8 rounded-3xl border border-violet-100 hover:shadow-2xl hover:shadow-violet-100 transition-all duration-300 group/kpi">
                        <p class="text-violet-600 font-black mb-3 uppercase text-[10px] tracking-[0.2em]">Kreativitas Desain</p>
                        <h4 class="text-4xl font-black text-slate-900 font-outfit">{{ number_format($totalDesains) }} <span class="text-xs font-bold text-violet-400 italic">Mockup</span></h4>
                    </div>
                </div>
            </div>

            <!-- Charts Section -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Bar Chart (Pendapatan Bulanan) -->
                <div class="md:col-span-2 bg-white shadow-sm sm:rounded-[2rem] border border-slate-100 p-8 flex flex-col">
                    <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-6">📈 Growth Matrix 2026</h4>
                    <div class="relative w-full h-[350px]">
                        <canvas id="revenueChart"></canvas>
                    </div>
                </div>
 
                <!-- Doughnut Chart (Status Pesanan) -->
                <div class="bg-white shadow-sm sm:rounded-[2rem] border border-slate-100 p-8 flex flex-col">
                    <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-6">🛒 Order Lifecycle</h4>
                    <div class="relative w-full h-[350px] flex flex-col justify-center">
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
            gradientStr.addColorStop(0, 'rgba(2, 132, 199, 0.9)'); // Sky-600
            gradientStr.addColorStop(1, 'rgba(2, 132, 199, 0.05)');
            
            new Chart(ctxRevenue, {
                type: 'bar',
                data: {
                    labels: monthLabels,
                    datasets: [{
                        label: 'Revenue',
                        data: revenueValues,
                        backgroundColor: gradientStr,
                        borderColor: '#0284c7',
                        borderWidth: 2,
                        borderRadius: 12,
                        barThickness: 'flex',
                        maxBarThickness: 40
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
