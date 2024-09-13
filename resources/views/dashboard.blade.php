<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <h2 class="text-xl font-semibold leading-tight">
                {{ __('Dashboard') }}
            </h2>
            <x-button target="_blank" href="https://github.com/kamona-wd/kui-laravel-breeze" variant="black" class="justify-center max-w-xs gap-2">
                <x-icons.github class="w-6 h-6" aria-hidden="true" />
                <span>{{ __('Star on Github') }}</span>
            </x-button>
        </div>
    </x-slot>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mb-6">
                <div class="bg-gradient-to-r from-blue-500 to-blue-600 text-white p-6 rounded-lg shadow-lg">
                    <div class="flex items-center">
                        <div class="text-3xl font-bold">Rp</div>
                        <div class="ml-4">
                            <p class="text-sm font-semibold">{{ __('Pemasukan Harian') }}</p>
                            <h5 class="text-2xl mt-2" id="dailyIncome">Rp {{$dailyIncome}}</h5>
                        </div>
                    </div>
                </div>

                <div class="bg-white text-gray-700 shadow-lg p-6 rounded-lg dark:bg-gray-800 dark:text-gray-300">
                    <div class="flex items-center">
                        <div class="text-2xl text-blue-500">
                            <x-heroicon-o-user-group class="w-8 h-8" aria-hidden="true" />
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-semibold">{{ __('Pelanggan Harian') }}</p>
                            <h5 class="text-2xl mt-2" id="dailyCustomers">{{$dailyCustomers}} Orang</h5>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white text-gray-700 shadow-lg p-6 rounded-lg dark:bg-gray-800 dark:text-gray-300">
                <div class="flex justify-between items-center mb-4">
                    <h5 class="font-semibold">{{ __('Analisis Produk Mingguan') }}</h5>
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button class="btn bg-gray-100 text-gray-700 px-3 py-2 rounded-lg hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-200">
                                {{ __('Filter') }}
                            </button>
                        </x-slot>
                        <x-slot name="content">
                            <x-dropdown-link href="#" class="chart-filter" data-filter="produk1">
                                {{ __('Produk 1') }}
                            </x-dropdown-link>
                            <x-dropdown-link href="#" class="chart-filter" data-filter="produk2">
                                {{ __('Produk 2') }}
                            </x-dropdown-link>
                            <x-dropdown-link href="#" class="chart-filter" data-filter="produk3">
                                {{ __('Produk 3') }}
                            </x-dropdown-link>
                        </x-slot>
                    </x-dropdown>
                </div>
                <div>
                    <canvas id="weeklyProductChart" class="w-full h-64"></canvas>
                </div>
            </div>
        </div>

        <div class="bg-white text-gray-700 shadow-lg p-6 rounded-lg dark:bg-gray-800 dark:text-gray-300">
            <div class="text-center mb-6">
                <p class="text-sm font-semibold">{{ __('Total Pemasukan') }}</p>
                <h2 class="text-3xl font-bold mt-2" id="totalIncome">Rp.{{$totalIncome}} </h2>
                <p class="text-gray-500 dark:text-gray-400" id="dateRange">{{ __('Tanggal - Tanggal') }}</p>
            </div>
            <hr class="border-gray-200 dark:border-gray-700">
            <div class="flex justify-between items-center mt-6">
                <h5 class="font-semibold">{{ __('Riwayat Transaksi') }}</h5>
                <x-button class="bg-gray-100 text-gray-700 px-3 py-2 rounded-lg hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-200" onclick="loadAllTransactions()">
                    {{ __('Semua') }}
                </x-button>
            </div>
            <div class="mt-6" id="transactionHistory">
                <div class="overflow-auto max-h-96">
                    <ul class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($transactions as $transaction)
                        <li class="py-4 flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="bg-blue-100 p-2 rounded-lg">
                                    <x-heroicon-o-switch-vertical class="w-6 h-6 text-blue-500" aria-hidden="true" />
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900 dark:text-gray-300">
                                        {{ $transaction->kode_transaksi }}
                                    </div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400 mt-1 flex items-center">
                                        Rp {{ number_format($transaction->total_harga, 0, ',', '.') }}
                                        <span class="mx-2">&bull;</span>
                                        Kasir: Admin
                                    </div>
                                </div>
                            </div>
                            <div class="text-xs text-gray-400 dark:text-gray-500">
                                <td class="px-6 py-4 whitespace-nowrap">{{ $transaction->created_at->format('d M Y H:i') }}</td>
                            </div>
                        </li>
                        <li class="py-4 flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="bg-blue-100 p-2 rounded-lg">
                                    <x-heroicon-o-switch-vertical class="w-6 h-6 text-blue-500" aria-hidden="true" />
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900 dark:text-gray-300">
                                        Transaksi #124
                                    </div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400 mt-1 flex items-center">
                                        Rp 200.000,00
                                        <span class="mx-2">&bull;</span>
                                        Kasir: Admin
                                    </div>
                                </div>
                            </div>
                            <div class="text-xs text-gray-400 dark:text-gray-500">
                                20 jam yang lalu
                            </div>
                        </li>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-4 whitespace-nowrap text-center">Tidak ada transaksi ditemukan.</td>
                        </tr>
                    @endforelse
                        <!-- Tambahkan item daftar lainnya sesuai kebutuhan -->
                    </ul>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var ctx = document.getElementById('weeklyProductChart').getContext('2d');
            var myChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: [], // Label akan diperbarui secara dinamis
                    datasets: [{
                        label: 'Analisis Produk Mingguan',
                        data: [], // Data akan diperbarui secara dinamis
                        backgroundColor: '#4A90E2', // Warna biru lembut
                        borderColor: '#0033A0', // Warna biru lebih gelap untuk border
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'top',
                        },
                        tooltip: {
                            callbacks: {
                                label: function (tooltipItem) {
                                    return tooltipItem.label + ': Rp ' + tooltipItem.raw.toLocaleString();
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            beginAtZero: true
                        },
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });

            var fakeData = {
                produk1: {
                    labels: ['Produk A', 'Produk B', 'Produk C'],
                    values: [120000, 150000, 90000]
                },
                produk2: {
                    labels: ['Produk X', 'Produk Y', 'Produk Z'],
                    values: [100000, 200000, 80000]
                },
                produk3: {
                    labels: ['Produk M', 'Produk N', 'Produk O'],
                    values: [180000, 110000, 130000]
                }
            };

            document.querySelectorAll('.chart-filter').forEach(filter => {
                filter.addEventListener('click', function (e) {
                    e.preventDefault();
                    var dataFilter = this.getAttribute('data-filter');
                    updateChart(dataFilter);
                });
            });

            function updateChart(filter) {
                if (fakeData[filter]) {
                    myChart.data.labels = fakeData[filter].labels;
                    myChart.data.datasets[0].data = fakeData[filter].values;
                    myChart.update();
                }
            }

            // Memuat data chart default
            updateChart('produk1');

        });

        function loadAllTransactions() {
            window.location.href = "{{ url('/transactions/history') }}";
        }
    </script>
    @endpush
</x-app-layout>
