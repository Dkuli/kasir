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
<!-- filepath: resources/views/dashboard.blade.php -->
<!-- Tambahkan di bawah container chart atau sebelum </body> -->

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Fetch data from API endpoint
        fetch('{{ route("dashboard.weekly-product-data") }}')
            .then(response => response.json())
            .then(data => {
                // Initialize weekly product sales chart
                initWeeklyProductChart(data.weeklySales);

                // Initialize daily revenue chart
                initDailyRevenueChart(data.dailySales);
            })
            .catch(error => console.error('Error fetching chart data:', error));

        // Function to initialize weekly product sales chart
        function initWeeklyProductChart(data) {
            const ctx = document.getElementById('weeklyProductChart').getContext('2d');

            // Limit to top 5 products if there are more
            let labels = data.labels;
            let quantities = data.quantities;
            let revenues = data.revenues;

            if (labels.length > 5) {
                labels = labels.slice(0, 5);
                quantities = quantities.slice(0, 5);
                revenues = revenues.slice(0, 5);
            }

            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [
                        {
                            label: 'Jumlah Terjual',
                            data: quantities,
                            backgroundColor: 'rgba(54, 162, 235, 0.5)',
                            borderColor: 'rgba(54, 162, 235, 1)',
                            borderWidth: 1,
                            yAxisID: 'y'
                        },
                        {
                            label: 'Total Pendapatan (Rp)',
                            data: revenues,
                            backgroundColor: 'rgba(255, 99, 132, 0.5)',
                            borderColor: 'rgba(255, 99, 132, 1)',
                            borderWidth: 1,
                            type: 'line',
                            yAxisID: 'y1'
                        }
                    ]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            type: 'linear',
                            display: true,
                            position: 'left',
                            title: {
                                display: true,
                                text: 'Jumlah Terjual'
                            }
                        },
                        y1: {
                            type: 'linear',
                            display: true,
                            position: 'right',
                            grid: {
                                drawOnChartArea: false,
                            },
                            title: {
                                display: true,
                                text: 'Total Pendapatan (Rp)'
                            },
                            ticks: {
                                callback: function(value) {
                                    return 'Rp ' + value.toLocaleString('id-ID');
                                }
                            }
                        }
                    },
                    plugins: {
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    let label = context.dataset.label || '';
                                    if (label) {
                                        label += ': ';
                                    }
                                    if (context.datasetIndex === 1) {
                                        label += 'Rp ' + context.parsed.y.toLocaleString('id-ID');
                                    } else {
                                        label += context.parsed.y;
                                    }
                                    return label;
                                }
                            }
                        },
                        legend: {
                            position: 'top',
                        },
                        title: {
                            display: true,
                            text: 'Top 5 Produk Terjual Minggu Ini'
                        }
                    }
                }
            });
        }

        // Function to initialize daily revenue chart
        function initDailyRevenueChart(data) {
            const ctx = document.getElementById('dailyRevenueChart').getContext('2d');

            // Format dates to be more readable
            const formattedLabels = data.labels.map(date => {
                const [year, month, day] = date.split('-');
                return `${day}/${month}`;
            });

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: formattedLabels,
                    datasets: [{
                        label: 'Pendapatan Harian',
                        data: data.values,
                        backgroundColor: 'rgba(75, 192, 192, 0.5)',
                        borderColor: 'rgba(75, 192, 192, 1)',
                        borderWidth: 2,
                        tension: 0.3,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Pendapatan (Rp)'
                            },
                            ticks: {
                                callback: function(value) {
                                    return 'Rp ' + value.toLocaleString('id-ID');
                                }
                            }
                        },
                        x: {
                            title: {
                                display: true,
                                text: 'Tanggal'
                            }
                        }
                    },
                    plugins: {
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    let label = context.dataset.label || '';
                                    if (label) {
                                        label += ': ';
                                    }
                                    label += 'Rp ' + context.parsed.y.toLocaleString('id-ID');
                                    return label;
                                }
                            }
                        },
                        legend: {
                            position: 'top',
                        },
                        title: {
                            display: true,
                            text: 'Pendapatan Harian Minggu Ini'
                        }
                    }
                }
            });
        }
    });
    </script>
    @endpush
</x-app-layout>
