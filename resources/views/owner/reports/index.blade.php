<x-app-layout>
    <!-- Muat Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <!-- Muat jQuery & DataTables CDN untuk Tabel Profesional -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>

    <x-slot name="header">
        <div class="flex flex-col md:flex-row justify-between items-center gap-4">
            <h2 class="font-black text-2xl text-slate-800 leading-tight font-outfit uppercase tracking-tight">
                Analytics & Performance <span class="text-red-600">(Owner)</span>
            </h2>

            <!-- Fitur Export & Print -->
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.report.index', array_merge(request()->query(), ['export_csv' => 1])) }}" class="print:hidden bg-emerald-600 text-white px-5 py-2.5 rounded-xl font-bold shadow-lg shadow-emerald-200 hover:bg-emerald-700 hover:-translate-y-1 transition flex items-center gap-2 text-sm uppercase tracking-wider">
                    📊 Download Excel
                </a>
                <button onclick="window.print()" class="print:hidden bg-slate-900 text-white px-5 py-2.5 rounded-xl font-bold shadow-lg shadow-slate-300 hover:bg-slate-800 hover:-translate-y-1 transition flex items-center gap-2 text-sm uppercase tracking-wider">
                    🖨️ Surat Cetak
                </button>
            </div>
        </div>
    </x-slot>

    <!-- CSS khusus Cetak -->
    <style>
        @media print {
            body { background: white !important; font-size: 12px; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .print\:hidden { display: none !important; }
            .shadow-sm, .shadow-xl { box-shadow: none !important; border: 1px solid #e2e8f0; }
            canvas { max-width: 100% !important; height: auto !important; }
            nav, header { display: none !important; }
            .py-12 { padding: 0 !important; }
            #print-header { display: block !important; margin-bottom: 30px; border-bottom: 3px solid #1e293b; padding-bottom: 15px; }
            table { font-size: 10px; width: 100% !important; }
            .dataTables_wrapper .dataTables_paginate, .dataTables_wrapper .dataTables_info, .dataTables_wrapper .dataTables_filter, .dataTables_wrapper .dataTables_length { display: none !important; }
        }
        #print-header { display: none; }
        
        /* Modifikasi DataTables UI biar pasang kuku modern */
        .dataTables_wrapper .dataTables_filter input { border: 1px solid #cbd5e1; border-radius: 8px; padding: 4px 10px; margin-left: 8px; outline: none; }
        .dataTables_wrapper .dataTables_filter input:focus { border-color: #dc2626; box-shadow: 0 0 0 3px rgba(220,38,38,0.1); }
    </style>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Surat Resmi Kop untuk Cetak PDF -->
            <div id="print-header">
                <h1 style="font-size: 24px; font-weight: 900; font-family: 'Outfit', sans-serif; text-transform: uppercase;">DAILY.CO Official Report</h1>
                <p style="font-weight: 600; font-size: 14px; margin-top: 5px;">Laporan Ringkasan Penjualan Transaksional</p>
                <p style="font-size: 12px; font-style: italic; color: #475569;">Periode Cetak: {{ $startDate->format('d M Y') }} - {{ $endDate->format('d M Y') }}</p>
            </div>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Filter Tanggal -->
            <div class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm print:hidden">
                <form method="GET" action="{{ route('admin.report.index') }}" class="flex flex-col md:flex-row items-end gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Mulai Tanggal</label>
                        <input type="date" name="start_date" value="{{ $startDate->format('Y-m-d') }}" class="border-slate-200 rounded-xl px-4 py-2 text-sm focus:ring-red-500 focus:border-red-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Sampai Tanggal</label>
                        <input type="date" name="end_date" value="{{ $endDate->format('Y-m-d') }}" class="border-slate-200 rounded-xl px-4 py-2 text-sm focus:ring-red-500 focus:border-red-500">
                    </div>
                    <button type="submit" class="bg-red-600 text-white px-6 py-2 rounded-xl font-bold shadow-md shadow-red-200 hover:bg-red-700 transition">Filter Data</button>
                    
                    @if(request()->has('start_date'))
                        <a href="{{ route('admin.report.index') }}" class="text-sm font-semibold text-slate-500 hover:text-slate-800 underline">Reset</a>
                    @endif
                </form>
                <p class="text-xs text-slate-400 mt-3 font-semibold">Menampilkan data periode: <span class="text-red-600">{{ $startDate->format('d M Y') }}</span> s/d <span class="text-red-600">{{ $endDate->format('d M Y') }}</span></p>
            </div>

            <!-- Summary KPI Cards -->
            <div class="bg-white overflow-hidden shadow-[0_20px_50px_rgba(220,_38,_38,_0.05)] sm:rounded-[2.5rem] border border-slate-100 p-10 relative overflow-hidden group">
                <div class="absolute -top-10 -right-10 w-40 h-40 bg-red-50 rounded-full blur-3xl opacity-50 group-hover:scale-150 transition-transform duration-1000"></div>
                
                <h3 class="text-xl font-black text-slate-800 mb-8 flex items-center gap-3 font-outfit uppercase tracking-tight relative z-10">
                    <span class="w-10 h-10 bg-red-600 rounded-xl flex items-center justify-center text-white shadow-lg shadow-red-100">📊</span>
                    Ringkasan Aktivitas <span class="text-red-600">DAILY.CO</span>
                </h3>
 
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8 relative z-10 print:grid-cols-3 print:gap-4 border-b border-dashed border-red-100 pb-10">
                    <div class="bg-red-50 p-8 rounded-3xl border border-red-100 hover:shadow-2xl hover:shadow-red-100 transition-all duration-300 group/kpi">
                        <p class="text-red-600 font-black mb-3 uppercase text-[10px] tracking-[0.2em] flex items-center gap-2 border-b border-red-100 pb-2">🏷️ Total Pesanan</p>
                        <h4 class="text-4xl font-black text-slate-900 font-outfit">{{ number_format($totalOrders) }} <span class="text-xs font-bold text-red-500 italic uppercase">Order</span></h4>
                        <div class="mt-4 text-[10px] font-bold">
                            @if($growthOrders >= 0)
                                <span class="text-emerald-700 bg-emerald-100 px-2 py-1 rounded shadow-sm border border-emerald-200">🚀 +{{ $growthOrders }}%</span>
                            @else
                                <span class="text-red-700 bg-red-100 px-2 py-1 rounded shadow-sm border border-red-200">🔻 {{ $growthOrders }}%</span>
                            @endif
                            <span class="text-slate-500 italic ml-1">vs rentang waktu sblmnya</span>
                        </div>
                    </div>
                    
                    <div class="bg-emerald-50 p-8 rounded-3xl border border-emerald-100 hover:shadow-2xl hover:shadow-emerald-100 transition-all duration-300 group/kpi">
                        <p class="text-emerald-600 font-black mb-3 uppercase text-[10px] tracking-[0.2em] flex items-center gap-2 border-b border-emerald-100 pb-2">💎 Pendapatan Bersih</p>
                        <h4 class="text-3xl font-black text-slate-900 font-outfit italic tracking-tighter">Rp {{ number_format($totalRevenue/1000, 0, ',', '.') }}<span class="text-[10px] font-bold text-emerald-500 uppercase not-italic"> Ribu</span></h4>
                        <div class="mt-4 text-[10px] font-bold">
                            @if($growthRevenue >= 0)
                                <span class="text-emerald-700 bg-emerald-100 px-2 py-1 rounded shadow-sm border border-emerald-200">🚀 +{{ $growthRevenue }}%</span>
                            @else
                                <span class="text-red-700 bg-red-100 px-2 py-1 rounded shadow-sm border border-red-200">🔻 {{ $growthRevenue }}%</span>
                            @endif
                            <span class="text-slate-500 italic ml-1">dari perputaran omzet</span>
                        </div>
                    </div>
 
                    <div class="bg-violet-50 p-8 rounded-3xl border border-violet-100 hover:shadow-2xl hover:shadow-violet-100 transition-all duration-300 group/kpi">
                        <p class="text-violet-600 font-black mb-3 uppercase text-[10px] tracking-[0.2em] flex items-center gap-2 border-b border-violet-100 pb-2">🎨 Kreativitas Desain</p>
                        <h4 class="text-4xl font-black text-slate-900 font-outfit">{{ number_format($totalDesains) }} <span class="text-xs font-bold text-violet-500 italic uppercase">Mockup</span></h4>
                        <div class="mt-4 text-[10px] font-bold">
                            @if($growthDesains >= 0)
                                <span class="text-emerald-700 bg-emerald-100 px-2 py-1 rounded shadow-sm border border-emerald-200">🚀 +{{ $growthDesains }}%</span>
                            @else
                                <span class="text-red-700 bg-red-100 px-2 py-1 rounded shadow-sm border border-red-200">🔻 {{ $growthDesains }}%</span>
                            @endif
                            <span class="text-slate-500 italic ml-1">kustomsiasi pelanggan</span>
                        </div>
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

            <!-- Tabel Transaksi Terbaru dengan DataTables -->
            <div class="bg-white shadow-xl shadow-slate-100/50 sm:rounded-[2rem] border border-slate-100 p-8 relative" style="page-break-before: always;">
                <h4 class="text-sm font-black text-slate-800 uppercase tracking-widest mb-6 border-b border-red-100 pb-3 flex items-center gap-3">
                    <span class="text-xl">📑</span> Ledger Laporan Transaksional
                </h4>
                <div class="overflow-x-auto print:overflow-visible">
                    <table id="ordersTable" class="w-full text-left border-collapse border border-slate-100">
                        <thead class="bg-slate-50">
                            <tr class="text-slate-600 text-[10px] font-black uppercase tracking-widest border-y border-slate-200">
                                <th class="py-4 px-4 whitespace-nowrap border-r border-slate-100">Inv No.</th>
                                <th class="py-4 px-4 whitespace-nowrap border-r border-slate-100">Tanggal Transaksi</th>
                                <th class="py-4 px-4 border-r border-slate-100">Informasi Customer</th>
                                <th class="py-4 px-4 border-r border-slate-100">Rincian Pembelian</th>
                                <th class="py-4 px-4 whitespace-nowrap border-r border-slate-100 text-right">Total (Rp)</th>
                                <th class="py-4 px-4 whitespace-nowrap text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody class="text-sm divide-y divide-slate-100">
                            @forelse($allOrders as $order)
                                <tr class="hover:bg-slate-50/50 transition">
                                    <td class="py-3 px-4 font-mono text-xs font-bold text-slate-500 border-r border-slate-50">INV-{{ str_pad($order->id_order, 5, '0', STR_PAD_LEFT) }}</td>
                                    <td class="py-3 px-4 font-medium text-slate-600 text-xs border-r border-slate-50">{{ \Carbon\Carbon::parse($order->tanggal_order)->format('Y-m-d') }}</td>
                                    <td class="py-3 px-4 font-bold text-slate-800 text-xs border-r border-slate-50">{{ $order->customer->nama_customer ?? 'Umum / Guest' }}</td>
                                    <td class="py-3 px-4 text-xs font-medium text-slate-500 border-r border-slate-50">
                                        <ul class="list-disc pl-4 space-y-1">
                                            @foreach($order->orderDetails as $od)
                                                <li>{{ $od->produk->nama_produk ?? 'Custom' }} <span class="font-bold text-red-500">(x{{ $od->quantity }})</span></li>
                                            @endforeach
                                        </ul>
                                    </td>
                                    <td class="py-3 px-4 font-black text-slate-900 text-right border-r border-slate-50">{{ number_format($order->total_harga, 0, ',', '.') }}</td>
                                    <td class="py-3 px-4 text-center">
                                        @php
                                            $badgeColor = match($order->status_order) {
                                                'menunggu_konfirmasi' => 'bg-amber-100 text-amber-700 border-amber-200',
                                                'diproses' => 'bg-blue-100 text-blue-700 border-blue-200',
                                                'dikirim' => 'bg-indigo-100 text-indigo-700 border-indigo-200',
                                                'selesai' => 'bg-emerald-100 text-emerald-700 border-emerald-200',
                                                'dibatalkan' => 'bg-red-100 text-red-700 border-red-200',
                                                default => 'bg-slate-100 text-slate-700 border-slate-200'
                                            };
                                        @endphp
                                        <span class="inline-block px-3 py-1 text-[10px] font-black uppercase tracking-wider rounded border {{ $badgeColor }}">
                                            {{ str_replace('_', ' ', $order->status_order) }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                            @endforelse
                        </tbody>
                    </table>
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
            gradientStr.addColorStop(0, 'rgba(220, 38, 38, 0.9)'); // Red-600
            gradientStr.addColorStop(1, 'rgba(220, 38, 38, 0.05)');
            
            new Chart(ctxRevenue, {
                type: 'bar',
                data: {
                    labels: monthLabels,
                    datasets: [{
                        label: 'Revenue',
                        data: revenueValues,
                        backgroundColor: gradientStr,
                        borderColor: '#dc2626',
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
            // --- INIT DATATABLES ---
            $('#ordersTable').DataTable({
                "pageLength": 10,
                "ordering": true,
                "order": [[1, "desc"]], // Sort by date descending
                "language": {
                    "search": "Cari Transaksi:",
                    "lengthMenu": "Tampilkan _MENU_ data",
                    "info": "Menampilkan _START_ s/d _END_ dari total _TOTAL_ transaksi",
                    "emptyTable": "Tidak ada data transaksi di rentang waktu ini."
                }
            });
        });
    </script>
</x-app-layout>
