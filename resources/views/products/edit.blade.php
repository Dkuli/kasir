<x-app-layout>
    <x-slot name="header">
        <h4 class="font-semibold text-xl text-gray-800 leading-tight">
            Edit Produk
        </h4>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-md rounded-lg p-6">
                <form action="{{ route('products.update', $product->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="mb-4">
                            <label for="kode_barang" class="block text-sm font-medium text-gray-700">Kode Barang</label>
                            <input type="text" name="kode_barang" id="kode_barang" value="{{ $product->kode_barang }}" class="mt-1 p-2 block w-full border border-gray-300 rounded-md shadow-sm bg-gray-100" readonly>
                        </div>

                        <div class="mb-4">
                            <label for="category_id" class="block text-sm font-medium text-gray-700">Kategori</label>
                            <select name="category_id" id="category_id" class="mt-1 p-2 block w-full border border-gray-300 rounded-md shadow-sm" required>
                                <option value="">Pilih Kategori</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ $product->category_id == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>


                        <div class="mb-4">
                            <label for="nama_barang" class="block text-sm font-medium text-gray-700">Nama Barang</label>
                            <input type="text" name="nama_barang" id="nama_barang" value="{{ old('nama_barang', $product->nama_barang) }}" class="mt-1 p-2 block w-full border border-gray-300 rounded-md shadow-sm" required>
                            @error('nama_barang')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="merek" class="block text-sm font-medium text-gray-700">Merek</label>
                            <input type="text" name="merek" id="merek" value="{{ old('merek', $product->merek) }}" class="mt-1 p-2 block w-full border border-gray-300 rounded-md shadow-sm">
                            @error('merek')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="stok" class="block text-sm font-medium text-gray-700">Stok</label>
                            <input type="number" name="stok" id="stok" value="{{ old('stok', $product->stok) }}" class="mt-1 p-2 block w-full border border-gray-300 rounded-md shadow-sm" required>
                            @error('stok')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="harga" class="block text-sm font-medium text-gray-700">Harga</label>
                            <input type="number" name="harga" id="harga" value="{{ old('harga', $product->harga) }}" class="mt-1 p-2 block w-full border border-gray-300 rounded-md shadow-sm" required>
                            @error('harga')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="keterangan" class="block text-sm font-medium text-gray-700">Keterangan</label>
                            <select name="keterangan" id="keterangan" class="mt-1 p-2 block w-full border border-gray-300 rounded-md shadow-sm" {{ $product->stok == 0 ? 'disabled' : '' }}>
                                <option value="Tersedia" {{ $product->keterangan == 'Tersedia' ? 'selected' : '' }}>Tersedia</option>
                                <option value="Terbatas" {{ $product->keterangan == 'Terbatas' ? 'selected' : '' }}>Terbatas</option>
                                <option value="Pre-order" {{ $product->keterangan == 'Pre-order' ? 'selected' : '' }}>Pre-order</option>
                                <option value="Habis" {{ $product->keterangan == 'Habis' ? 'selected' : '' }}>Habis</option>
                            </select>
                            @error('keterangan')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="image" class="block text-sm font-medium text-gray-700">Gambar Produk</label>
                            <input type="file" name="image" id="image" class="mt-1 p-2 block w-full border border-gray-300 rounded-md shadow-sm">
                            <div class="mt-2">
                                <img src="{{ asset('storage/' . $product->image) }}" alt="Gambar Produk" class="w-32 h-32 object-cover rounded-md">
                            </div>
                            @error('image')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="flex justify-end mt-4">
                        <button type="submit" class="bg-blue-500 text-white hover:bg-blue-600 px-4 py-2 rounded-md">
                            <x-heroicon-o-check class="w-5 h-5 inline-block mr-1" />
                            Update
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
    document.getElementById('stok').addEventListener('change', function() {
        var stok = parseInt(this.value);
        var keterangan = document.getElementById('keterangan');
        if (stok === 0) {
            keterangan.value = 'Habis';
            keterangan.disabled = true;
        } else {
            keterangan.disabled = false;
        }
    });
    </script>
</x-app-layout>
