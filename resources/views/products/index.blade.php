<x-app-layout>
    <x-slot name="header">
        <h4 class="font-semibold text-xl text-gray-800 leading-tight">
            Daftar Produk
        </h4>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-md rounded-lg p-6">
                <div class="flex justify-between items-center mb-6">
                    <div class="flex items-center gap-4">
                        <a href="{{ route('products.create') }}" class="bg-green-500 text-white hover:bg-green-600 px-4 py-2 rounded-md flex items-center">
                            <x-heroicon-o-plus class="w-5 h-5 mr-1" />
                            Tambah Produk
                        </a>
                        <a href="{{ route('products.import') }}" class="bg-yellow-500 text-white hover:bg-yellow-600 px-4 py-2 rounded-md flex items-center">
                            <x-heroicon-o-upload class="w-5 h-5 mr-1" />
                            Import Produk
                        </a>
                    </div>
                    <div>
                        <a href="{{ route('products.barcodes') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md flex items-center">
                            <i class="fas fa-barcode mr-2"></i> Generate Barcodes
                        </a>
                    </div>
                </div>

                <!-- Pencarian -->
                <form method="GET" action="{{ route('products.index') }}" class="mb-6">
                    <div class="relative">
                        <input type="text" name="search" value="{{ request()->get('search') }}" placeholder="Cari produk..." class="p-3 w-full border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 shadow-sm">
                        <button type="submit" class="absolute right-2 top-2 bg-blue-600 text-white hover:bg-blue-700 px-4 py-2 rounded-lg font-semibold transition ease-in-out duration-150">
                            <x-heroicon-o-search class="w-5 h-5" />
                        </button>
                    </div>
                </form>

                <!-- Tabel Produk -->
                <div class="overflow-x-auto">
                    <table class="min-w-full bg-white divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kode Barang</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kategori</th> <!-- Update -->
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Barang</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Merek</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stok</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Harga</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Gambar</th> <!-- Tambahkan Gambar -->
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Keterangan</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach ($products as $product)
                                <tr class="hover:bg-gray-100">
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $product->kode_barang }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $product->category->name ?? '-' }}</td> <!-- Update -->
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $product->nama_barang }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $product->merek ?? '-' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $product->stok }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">Rp {{ number_format($product->harga, 0, ',', '.') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($product->image)
                                            <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->nama_barang }}" class="w-16 h-16 object-cover rounded-md">
                                        @else
                                            Tidak ada gambar
                                        @endif
                                    </td> <!-- Tambahkan tampilan gambar -->
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $product->keterangan }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <a href="{{ route('products.edit', $product->id) }}" class="text-indigo-600 hover:text-indigo-900">
                                            <x-heroicon-o-pencil class="w-5 h-5 inline-block mr-1" />
                                            Edit
                                        </a>
                                        <button onclick="confirmDelete('{{ $product->kode_barang }}', '{{ route('products.destroy', $product->id) }}')" class="text-red-600 hover:text-red-900">
                                            <x-heroicon-o-trash class="w-5 h-5 inline-block mr-1" />
                                            Hapus
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>

                    </table>
                </div>

                <!-- Pagination -->
                <div class="mt-4">
                    {{ $products->links('pagination::tailwind') }}
                </div>
            </div>
        </div>
    </div>



    <script>
        function confirmDelete(itemCode, deleteUrl) {
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Barang dengan kode " + itemCode + " akan dihapus.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    axios.post(deleteUrl, {
                        _method: 'DELETE',
                        _token: '{{ csrf_token() }}'
                    })
                    .then(response => {
                        Swal.fire(
                            'Terhapus!',
                            'Barang telah dihapus.',
                            'success'
                        ).then(() => {
                            location.reload();
                        });
                    })
                    .catch(error => {
                        Swal.fire(
                            'Gagal!',
                            'Barang tidak dapat dihapus.',
                            'error'
                        );
                    });
                }
            });
        }
    </script>
</x-app-layout>
