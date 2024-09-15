<x-app-layout>
    <x-slot name="header">
        <h4 class="font-semibold text-xl text-gray-800 leading-tight">
            Riwayat Transaksi
        </h4>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-md rounded-lg p-6">
                <!-- Table -->
                <div class="overflow-x-auto">
                    <table class="min-w-full bg-white divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kode Transaksi</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Harga</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bayar</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kembali</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Detail</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($transactions as $transaction)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $transaction->kode_transaksi }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">Rp {{ number_format($transaction->total_harga, 0, ',', '.') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">Rp {{ number_format($transaction->bayar, 0, ',', '.') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">Rp {{ number_format($transaction->kembali, 0, ',', '.') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $transaction->created_at->format('d M Y H:i') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <button onclick="showTransactionDetails({{ $transaction->id }})" class="bg-blue-500 text-white hover:bg-blue-600 px-4 py-2 rounded-md">
                                            Lihat Detail
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-4 whitespace-nowrap text-center">Tidak ada transaksi ditemukan.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="mt-4">
                    {{ $transactions->links('pagination::tailwind') }}
                </div>
            </div>
        </div>
    </div>

    <!-- Modal for Transaction Details -->
    <div id="transaction-details-modal" class="fixed z-10 inset-0 overflow-y-auto hidden">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
            </div>

            <!-- Modal Content -->
            <div class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="block bg-white px-4 pt-5 pb-4">
                    <div class="text-lg font-bold mb-4">Detail Transaksi</div>
                    <div id="transaction-details-content"></div>
                </div>
                <div class="mt-5 sm:mt-6">
                    <button onclick="closeModal()" class="bg-red-600 text-white px-4 py-2 rounded-md">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function showTransactionDetails(transactionId) {
            axios.get('/transactions/' + transactionId)
                .then(response => {
                    const transaction = response.data.transaction;
                    let content = `<p>Kode Transaksi: ${transaction.kode_transaksi}</p>`;
                    content += `<p>Total Harga: Rp ${new Intl.NumberFormat('id-ID').format(transaction.total_harga)}</p>`;
                    content += `<p>Bayar: Rp ${new Intl.NumberFormat('id-ID').format(transaction.bayar)}</p>`;
                    content += `<p>Kembali: Rp ${new Intl.NumberFormat('id-ID').format(transaction.kembali)}</p>`;
                    content += `<p>Tanggal: ${transaction.created_at}</p>`;
                    content += '<hr><p>Produk:</p><ul>';

                    transaction.products.forEach(product => {
                        content += `<li>${product.nama_barang} - Qty: ${product.pivot.quantity}, Harga: Rp ${new Intl.NumberFormat('id-ID').format(product.pivot.price)}</li>`;
                    });
                    content += '</ul>';

                    document.getElementById('transaction-details-content').innerHTML = content;
                    document.getElementById('transaction-details-modal').classList.remove('hidden');
                })
                .catch(error => {
                    alert('Gagal mengambil detail transaksi.');
                });
        }

        function closeModal() {
            document.getElementById('transaction-details-modal').classList.add('hidden');
        }
    </script>
</x-app-layout>

