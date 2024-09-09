<x-app-layout>
    <x-slot name="header">
        <h4 class="font-semibold text-xl text-gray-800 leading-tight">
            Tambah Produk Baru
        </h4>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-md rounded-lg p-6">
                <form id="productForm" action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Kategori -->
                        <div class="mb-4">
                            <label for="category_id" class="block text-sm font-medium text-gray-700">Kategori</label>
                            <select name="category_id" id="category_id" class="mt-1 p-2 block w-full border border-gray-300 rounded-md shadow-sm" required>
                                <option value="">Pilih Kategori</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Nama Barang -->
                        <div class="mb-4">
                            <label for="nama_barang" class="block text-sm font-medium text-gray-700">Nama Barang</label>
                            <input type="text" name="nama_barang" id="nama_barang" value="{{ old('nama_barang') }}" placeholder="Masukkan nama barang" class="mt-1 p-2 block w-full border border-gray-300 rounded-md shadow-sm" required>
                            @error('nama_barang')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Merek -->
                        <div class="mb-4">
                            <label for="merek" class="block text-sm font-medium text-gray-700">Merek</label>
                            <input type="text" name="merek" id="merek" value="{{ old('merek') }}" placeholder="Masukkan merek" class="mt-1 p-2 block w-full border border-gray-300 rounded-md shadow-sm">
                            @error('merek')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Stok -->
                        <div class="mb-4">
                            <label for="stok" class="block text-sm font-medium text-gray-700">Stok</label>
                            <input type="number" name="stok" id="stok" value="{{ old('stok', 1) }}" placeholder="Masukkan jumlah stok" class="mt-1 p-2 block w-full border border-gray-300 rounded-md shadow-sm" required>
                            @error('stok')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Harga -->
                        <div class="mb-4">
                            <label for="harga" class="block text-sm font-medium text-gray-700">Harga</label>
                            <input type="number" name="harga" id="harga" value="{{ old('harga') }}" placeholder="Masukkan harga" class="mt-1 p-2 block w-full border border-gray-300 rounded-md shadow-sm" required>
                            @error('harga')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Keterangan -->
                        <div class="mb-4">
                            <label for="keterangan" class="block text-sm font-medium text-gray-700">Keterangan</label>
                            <select name="keterangan" id="keterangan" class="mt-1 p-2 block w-full border border-gray-300 rounded-md shadow-sm">
                                <option value="Tersedia" {{ old('keterangan') == 'Tersedia' ? 'selected' : '' }}>Tersedia</option>
                                <option value="Terbatas" {{ old('keterangan') == 'Terbatas' ? 'selected' : '' }}>Terbatas</option>
                                <option value="Pre-order" {{ old('keterangan') == 'Pre-order' ? 'selected' : '' }}>Pre-order</option>
                            </select>
                            @error('keterangan')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Input Gambar -->
                        <div class="mb-4">
                            <label for="image" class="block text-sm font-medium text-gray-700">Gambar Produk</label>
                            <input type="file" name="image" id="image" class="mt-1 p-2 block w-full border border-gray-300 rounded-md shadow-sm">
                            @error('image')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <!-- Button Submit -->
                    <div class="flex justify-end mt-4">
                        <button type="button" id="submitButton" class="bg-blue-500 text-white hover:bg-blue-600 px-4 py-2 rounded-md">
                            <x-heroicon-o-check class="w-5 h-5 inline-block mr-1" />
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Menampilkan konfirmasi sebelum menyimpan produk
        document.getElementById('submitButton').addEventListener('click', function(event) {
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Anda akan menyimpan produk baru ini.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, simpan!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('productForm').submit();
                }
            });
        });
    </script>
</x-app-layout>
