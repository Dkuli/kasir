<x-app-layout>
    <x-slot name="header">
        <h4 class="font-semibold text-xl text-gray-800 leading-tight">
            tes Generate Sales Report
        </h4>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-md rounded-lg p-6">
                <form action="{{ route('reports.sales') }}" method="GET">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="start_date" class="block text-sm font-medium text-gray-700">Start Date</label>
                            <input type="date" id="start_date" name="start_date" required class="mt-1 block w-full">
                        </div>
                        <div>
                            <label for="end_date" class="block text-sm font-medium text-gray-700">End Date</label>
                            <input type="date" id="end_date" name="end_date" required class="mt-1 block w-full">
                        </div>
                        <div>
                            <label for="product_name" class="block text-sm font-medium text-gray-700">Product Name</label>
                            <input type="text" id="product_name" name="product_name" class="mt-1 block w-full">
                        </div>
                        <div>
                            <label for="transaction_code" class="block text-sm font-medium text-gray-700">Transaction Code</label>
                            <input type="text" id="transaction_code" name="transaction_code" class="mt-1 block w-full">
                        </div>
                    </div>
                    <div class="mt-4">
                        <button type="submit" class="bg-blue-500 text-white hover:bg-blue-600 px-4 py-2 rounded-md">Generate Report</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
