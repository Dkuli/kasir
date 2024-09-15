<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Transaction Reports') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                <!-- Filter Form -->
                <form action="{{ route('reports.index') }}" method="GET" class="mb-6">
                    <div class="flex flex-wrap -mx-3 mb-4">
                        <div class="w-full md:w-1/4 px-3 mb-4 md:mb-0">
                            <label for="start_date" class="block text-gray-700 text-sm font-bold mb-2">Start Date:</label>
                            <input type="date" id="start_date" name="start_date" value="{{ request('start_date') }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                        </div>
                        <div class="w-full md:w-1/4 px-3 mb-4 md:mb-0">
                            <label for="end_date" class="block text-gray-700 text-sm font-bold mb-2">End Date:</label>
                            <input type="date" id="end_date" name="end_date" value="{{ request('end_date') }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                        </div>
                        <div class="w-full md:w-1/4 px-3 mb-4 md:mb-0">
                            <label for="search" class="block text-gray-700 text-sm font-bold mb-2">Search:</label>
                            <input type="text" id="search" name="search" value="{{ request('search') }}" placeholder="Transaction code or user name" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                        </div>
                        <div class="w-full md:w-1/4 px-3 mb-4 md:mb-0 flex items-end">
                            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                                Filter
                            </button>
                        </div>
                    </div>
                </form>

                <!-- Summary Cards -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                    <div class="bg-white overflow-hidden shadow rounded-lg">
                        <div class="px-4 py-5 sm:p-6">
                            <dt class="text-sm font-medium text-gray-500 truncate">
                                Total Revenue
                            </dt>
                            <dd class="mt-1 text-3xl font-semibold text-gray-900">
                                Rp {{ number_format($totalRevenue, 0, ',', '.') }}
                            </dd>
                        </div>
                    </div>
                    <div class="bg-white overflow-hidden shadow rounded-lg">
                        <div class="px-4 py-5 sm:p-6">
                            <dt class="text-sm font-medium text-gray-500 truncate">
                                Total Transactions
                            </dt>
                            <dd class="mt-1 text-3xl font-semibold text-gray-900">
                                {{ $totalTransactions }}
                            </dd>
                        </div>
                    </div>
                    <div class="bg-white overflow-hidden shadow rounded-lg">
                        <div class="px-4 py-5 sm:p-6">
                            <dt class="text-sm font-medium text-gray-500 truncate">
                                Average Transaction Value
                            </dt>
                            <dd class="mt-1 text-3xl font-semibold text-gray-900">
                                Rp {{ number_format($averageTransactionValue, 0, ',', '.') }}
                            </dd>
                        </div>
                    </div>
                </div>

                <!-- Sales Chart -->
                <div class="bg-white overflow-hidden shadow rounded-lg mb-6">
                    <div class="px-4 py-5 sm:p-6">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Daily Sales</h3>
                        <div id="sales-chart" style="height: 300px;"></div>
                    </div>
                </div>

                <!-- Top Products -->
                <div class="bg-white overflow-hidden shadow rounded-lg mb-6">
                    <div class="px-4 py-5 sm:p-6">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Top Selling Products</h3>
                        <ul class="divide-y divide-gray-200">
                            @foreach ($topProducts as $product)
                                <li class="py-3 flex justify-between items-center">
                                    <span class="text-gray-900">{{ $product->nama_barang }}</span>
                                    <span class="text-gray-500">{{ $product->total_sold }} sold</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>

                <!-- Export Buttons -->
                <div class="mb-4 flex justify-end">
                    <a href="{{ route('reports.export.excel') }}?{{ http_build_query(request()->all()) }}" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                        Export Excel
                    </a>
                    <a href="{{ route('reports.export.pdf') }}?{{ http_build_query(request()->all()) }}" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded ml-2">
                        Export PDF
                    </a>
                </div>

                <!-- Transactions Table -->
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Transaction Code
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                User
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Total
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Date
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Products
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach ($transactions as $transaction)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{ $transaction->kode_transaksi }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $transaction->user->name }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    Rp {{ number_format($transaction->total_harga, 0, ',', '.') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $transaction->created_at->format('d M Y H:i') }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">
                                    <ul>
                                        @foreach ($transaction->products as $product)
                                            <li>{{ $product->nama_barang }} ({{ $product->pivot->quantity }})</li>
                                        @endforeach
                                    </ul>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="mt-4">
                    {{ $transactions->links() }}
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script>
        var options = {
            chart: {
                type: 'area',
                height: 300
            },
            series: [{
                name: 'Sales',
                data: {!! json_encode($dailySales->pluck('total_sales')) !!}
            }],
            xaxis: {
                categories: {!! json_encode($dailySales->pluck('date')) !!}
            },
            yaxis: {
                title: {
                    text: 'Sales (Rp)'
                }
            },
            title: {
                text: 'Daily Sales',
                align: 'left'
            }
        }

        var chart = new ApexCharts(document.querySelector("#sales-chart"), options);
        chart.render();
    </script>
    @endpush
</x-app-layout>
