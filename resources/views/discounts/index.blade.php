<x-app-layout>
    <x-slot name="header">
        <h4 class="font-semibold text-xl text-gray-800 leading-tight">
            Daftar Diskon
        </h4>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-md rounded-lg p-6">
                <div class="flex justify-between items-center mb-6">
                    <div class="flex items-center gap-4">
                        <a href="{{ route('discounts.create') }}" class="bg-green-500 text-white hover:bg-green-600 px-4 py-2 rounded-md flex items-center">
                            <x-heroicon-o-plus class="w-5 h-5 mr-1" />
                            Tambah Diskon
                        </a>
                    </div>
                </div>

                <!-- Tabel Diskon -->
                <div class="overflow-x-auto">
                    <table class="min-w-full bg-white divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kode</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipe</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nilai</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Berlaku Untuk</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Periode</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach ($discounts as $discount)
                                <tr class="hover:bg-gray-100">
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $discount->name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $discount->code ?? '-' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($discount->type == 'percentage')
                                            <span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800">Persentase</span>
                                        @elseif($discount->type == 'fixed')
                                            <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">Nominal Tetap</span>
                                        @else
                                            <span class="px-2 py-1 text-xs rounded-full bg-purple-100 text-purple-800">Beli X Gratis Y</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($discount->type == 'percentage')
                                            {{ $discount->value }}%
                                        @elseif($discount->type == 'fixed')
                                            Rp {{ number_format($discount->value, 0, ',', '.') }}
                                        @else
                                            {{ $discount->value }}
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($discount->applies_to == 'all')
                                            Semua Produk
                                        @elseif($discount->applies_to == 'category')
                                            Kategori: {{ $discount->category->name ?? 'Tidak ada' }}
                                        @else
                                            Produk: {{ $discount->product->nama_barang ?? 'Tidak ada' }}
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($discount->start_date && $discount->end_date)
                                            {{ $discount->start_date->format('d/m/Y') }} - {{ $discount->end_date->format('d/m/Y') }}
                                        @elseif($discount->start_date)
                                            Dari {{ $discount->start_date->format('d/m/Y') }}
                                        @elseif($discount->end_date)
                                            Sampai {{ $discount->end_date->format('d/m/Y') }}
                                        @else
                                            Tidak terbatas
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($discount->is_active)
                                            <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">Aktif</span>
                                        @else
                                            <span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-800">Non-aktif</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <a href="{{ route('discounts.edit', $discount->id) }}" class="text-indigo-600 hover:text-indigo-900">
                                            <x-heroicon-o-pencil class="w-5 h-5 inline-block mr-1" />
                                            Edit
                                        </a>
                                        <button onclick="confirmDelete('{{ $discount->name }}', '{{ route('discounts.destroy', $discount->id) }}')" class="text-red-600 hover:text-red-900">
                                            <x-heroicon-o-trash class="w-5 h-5 inline-block mr-1" />
                                            Hapus
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
        function confirmDelete(name, deleteUrl) {
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Diskon '" + name + "' akan dihapus.",
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
                            'Diskon telah dihapus.',
                            'success'
                        ).then(() => {
                            location.reload();
                        });
                    })
                    .catch(error => {
                        Swal.fire(
                            'Gagal!',
                            'Diskon tidak dapat dihapus.',
                            'error'
                        );
                    });
                }
            });
        }
    </script>
</x-app-layout>
