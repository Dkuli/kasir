<x-app-layout>
    <x-slot name="header">
        <h4 class="font-semibold text-xl text-gray-800 leading-tight">
            Tambah Diskon
        </h4>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-md rounded-lg p-6">
                <form action="{{ route('discounts.store') }}" method="POST">
                    @csrf

                    <div class="mb-6">
                        <label for="name" class="block mb-2 text-sm font-medium text-gray-700">Nama Diskon <span class="text-red-500">*</span></label>
                        <input type="text" name="name" id="name" class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" value="{{ old('name') }}" required>
                        @error('name')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-6">
                        <label for="code" class="block mb-2 text-sm font-medium text-gray-700">Kode Diskon</label>
                        <input type="text" name="code" id="code" class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" value="{{ old('code') }}">
                        <small class="text-gray-500">Biarkan kosong jika tidak perlu kode diskon</small>
                        @error('code')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-6">
                        <label for="type" class="block mb-2 text-sm font-medium text-gray-700">Tipe Diskon <span class="text-red-500">*</span></label>
                        <select name="type" id="type" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm" required>
                            <option value="percentage" {{ old('type') == 'percentage' ? 'selected' : '' }}>Persentase (%)</option>
                            <option value="fixed" {{ old('type') == 'fixed' ? 'selected' : '' }}>Nominal Tetap (Rp)</option>
                            <option value="buy_x_get_y" {{ old('type') == 'buy_x_get_y' ? 'selected' : '' }}>Beli X Gratis Y</option>
                        </select>
                        @error('type')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-6">
                        <label for="value" class="block mb-2 text-sm font-medium text-gray-700">Nilai Diskon <span class="text-red-500">*</span></label>
                        <input type="number" step="0.01" name="value" id="value" class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" value="{{ old('value') }}" required>
                        <small id="valueHelp" class="text-gray-500">Untuk persentase, masukkan nilai dalam % (mis: 10 untuk 10%)</small>
                        @error('value')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-6">
                        <label for="min_purchase" class="block mb-2 text-sm font-medium text-gray-700">Minimal Pembelian (Rp)</label>
                        <input type="number" name="min_purchase" id="min_purchase" class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" value="{{ old('min_purchase') }}">
                        <small class="text-gray-500">Biarkan kosong jika tidak ada minimum</small>
                        @error('min_purchase')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-6">
                        <label for="max_discount" class="block mb-2 text-sm font-medium text-gray-700">Maksimal Diskon (Rp)</label>
                        <input type="number" name="max_discount" id="max_discount" class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" value="{{ old('max_discount') }}">
                        <small class="text-gray-500">Biarkan kosong jika tidak ada batas maksimal</small>
                        @error('max_discount')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-6 grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="start_date" class="block mb-2 text-sm font-medium text-gray-700">Tanggal Mulai</label>
                            <input type="date" name="start_date" id="start_date" class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" value="{{ old('start_date') }}">
                            <small class="text-gray-500">Biarkan kosong jika tidak ada batas waktu mulai</small>
                            @error('start_date')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="end_date" class="block mb-2 text-sm font-medium text-gray-700">Tanggal Berakhir</label>
                            <input type="date" name="end_date" id="end_date" class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" value="{{ old('end_date') }}">
                            <small class="text-gray-500">Biarkan kosong jika tidak ada batas waktu berakhir</small>
                            @error('end_date')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-6">
                        <label for="applies_to" class="block mb-2 text-sm font-medium text-gray-700">Berlaku Untuk <span class="text-red-500">*</span></label>
                        <select name="applies_to" id="applies_to" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm" required>
                            <option value="all" {{ old('applies_to') == 'all' ? 'selected' : '' }}>Semua Produk</option>
                            <option value="category" {{ old('applies_to') == 'category' ? 'selected' : '' }}>Kategori Tertentu</option>
                            <option value="product" {{ old('applies_to') == 'product' ? 'selected' : '' }}>Produk Tertentu</option>
                        </select>
                        @error('applies_to')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div id="category_selection" class="mb-6 {{ old('applies_to') == 'category' ? '' : 'hidden' }}">
                        <label for="category_id" class="block mb-2 text-sm font-medium text-gray-700">Pilih Kategori</label>
                        <select name="category_id" id="category_id" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            <option value="">Pilih Kategori</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                            @endforeach
                        </select>
                        @error('category_id')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div id="product_selection" class="mb-6 {{ old('applies_to') == 'product' ? '' : 'hidden' }}">
                        <label for="product_id" class="block mb-2 text-sm font-medium text-gray-700">Pilih Produk</label>
                        <select name="product_id" id="product_id" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            <option value="">Pilih Produk</option>
                            @foreach($products as $product)
                                <option value="{{ $product->id }}" {{ old('product_id') == $product->id ? 'selected' : '' }}>{{ $product->nama_barang }} - {{ $product->kode_barang }}</option>
                            @endforeach
                        </select>
                        @error('product_id')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-6">
                        <div class="flex items-center">
                            <input type="checkbox" name="is_active" id="is_active" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded" value="1" {{ old('is_active', 1) ? 'checked' : '' }}>
                            <label for="is_active" class="ml-2 block text-sm text-gray-700">Diskon Aktif</label>
                        </div>
                    </div>

                    <div class="flex justify-end space-x-2">
                        <a href="{{ route('discounts.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded inline-flex items-center">Batal</a>
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded inline-flex items-center">
                            <x-heroicon-o-save class="w-5 h-5 mr-2" />
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const typeSelect = document.getElementById('type');
            const valueHelp = document.getElementById('valueHelp');
            const appliesTo = document.getElementById('applies_to');
            const categorySelection = document.getElementById('category_selection');
            const productSelection = document.getElementById('product_selection');

            // Update help text based on discount type
            function updateValueHelp() {
                const selectedType = typeSelect.value;
                switch(selectedType) {
                    case 'percentage':
                        valueHelp.textContent = 'Untuk persentase, masukkan nilai dalam % (mis: 10 untuk 10%)';
                        break;
                    case 'fixed':
                        valueHelp.textContent = 'Masukkan jumlah diskon dalam Rupiah';
                        break;
                    case 'buy_x_get_y':
                        valueHelp.textContent = 'Masukkan jumlah item yang perlu dibeli untuk mendapatkan item gratis';
                        break;
                }
            }

            // Show/hide relevant fields based on what the discount applies to
            function updateAppliesTo() {
                const selected = appliesTo.value;

                if (selected === 'category') {
                    categorySelection.classList.remove('hidden');
                    productSelection.classList.add('hidden');
                } else if (selected === 'product') {
                    productSelection.classList.remove('hidden');
                    categorySelection.classList.add('hidden');
                } else {
                    categorySelection.classList.add('hidden');
                    productSelection.classList.add('hidden');
                }
            }

            // Set up event listeners
            typeSelect.addEventListener('change', updateValueHelp);
            appliesTo.addEventListener('change', updateAppliesTo);

            // Initialize
            updateValueHelp();
            updateAppliesTo();
        });
    </script>
    @endpush
</x-app-layout>
