<x-app-layout>
    <x-slot name="header">
        <h4 class="font-semibold text-xl text-gray-800 leading-tight">
            Transaksi Berhasil
        </h4>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-md rounded-lg p-6">
                <div id="receipt-content">
                    <!-- Transaction success message -->
                    <div class="text-center mb-4">
                        <h2 class="text-2xl font-semibold text-green-600">Transaksi Berhasil!</h2>
                        <p class="text-gray-600">Terima kasih telah berbelanja di K UI Mart</p>
                    </div>

                    <!-- Store information -->
                    <div class="text-center mb-6">
                        <p class="font-bold text-xl">K UI Mart</p>
                        <p class="text-sm">Jl. Example No. 123, Kota, Indonesia</p>
                        <p class="text-sm">Tel: 123-456-789</p>
                    </div>

                    <hr class="my-4 border-t-2 border-gray-300">

                    <!-- Transaction details -->
                    <table class="w-full text-sm">
                        <tbody>
                            <tr>
                                <td class="font-semibold">Kode Transaksi:</td>
                                <td class="text-right">{{ $transaction->kode_transaksi }}</td>
                            </tr>
                            <tr>
                                <td class="font-semibold">Total Harga:</td>
                                <td class="text-right">Rp {{ number_format($transaction->total_harga, 0, ',', '.') }}</td>
                            </tr>
                            <tr>
                                <td class="font-semibold">Bayar:</td>
                                <td class="text-right">Rp {{ number_format($transaction->bayar, 0, ',', '.') }}</td>
                            </tr>
                            <tr>
                                <td class="font-semibold">Kembali:</td>
                                <td class="text-right">Rp {{ number_format($transaction->kembali, 0, ',', '.') }}</td>
                            </tr>
                            <tr>
                                <td class="font-semibold">Tanggal:</td>
                                <td class="text-right">{{ $transaction->created_at->format('d M Y H:i') }}</td>
                            </tr>
                        </tbody>
                    </table>

                    <hr class="my-4 border-t-2 border-gray-300">

                    <!-- Product list (optional) -->
                    @if ($transaction->products)
                        <div class="mb-4">
                            <p class="font-semibold">Rincian Produk:</p>
                            <ul>
                                @foreach ($transaction->products as $product)
                                    <li>{{ $product->nama_barang }} - Qty: {{ $product->pivot->quantity }}, Harga: Rp {{ number_format($product->pivot->price, 0, ',', '.') }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <p class="text-center text-sm">Terima kasih telah berbelanja di K UI Mart!</p>
                </div>

                <!-- Action buttons -->
                <div class="text-center mt-6">
                    <a href="{{ route('transactions.index') }}" class="inline-block bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600">
                        Kembali ke Daftar Transaksi
                    </a>
                    <button onclick="printReceipt()" class="ml-4 inline-block bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600">
                        Cetak Struk
                    </button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function printReceipt() {
                const printWindow = window.open('', '', 'height=600,width=400');
                const kasir = "{{ auth()->user()->name }}";

                printWindow.document.write(`
                    <!DOCTYPE html>
                    <html>
                    <head>
                        <title>Struk Pembelian</title>
                        <style>
                            * { font-family: 'Courier New', monospace; font-size: 12px; }
                            body { width: 80mm; margin: 0 auto; }
                            .center { text-align: center; }
                            .right { text-align: right; }
                            .left { text-align: left; }
                            p { margin: 0; line-height: 1.5; }
                            .bold { font-weight: bold; }
                            table { width: 100%; border-collapse: collapse; }
                            .dashed-line { border-top: 1px dashed #000; height: 1px; margin: 10px 0; }
                            .mb-5 { margin-bottom: 5px; }
                            .mt-5 { margin-top: 5px; }
                        </style>
                    </head>
                    <body>
                        <div class="center bold">
                            <p style="font-size: 14px;">K UI Mart</p>
                            <p>Jl. Example No. 123, Kota, Indonesia</p>
                            <p>Tel: 123-456-789</p>
                        </div>
                        <div class="dashed-line"></div>
                        <table>
                            <tr>
                                <td>Kode Transaksi :</td>
                                <td>{{ $transaction->kode_transaksi }}</td>
                                <td class="right">Kasir : ${kasir}</td>
                            </tr>
                            <tr>
                                <td colspan="3">{{ $transaction->created_at->format('d M, Y H:i') }}</td>
                            </tr>
                        </table>
                        <div class="dashed-line"></div>
                        <table>
                            <tr>
                                <td>Nama Barang</td>
                                <td class="right">Qty</td>
                                <td class="right">Harga</td>
                                <td class="right">Jumlah</td>
                            </tr>
                            ${generateProductRows()}
                        </table>
                        <div class="dashed-line"></div>
                        <table>
                            <tr>
                                <td>Subtotal (Jumlah : {{ $transaction->products->count() }})</td>
                                <td class="right">{{ number_format($transaction->total_harga, 2, ',', '.') }}</td>
                            </tr>
                            <tr>
                                <td>Diskon (0%)</td>
                                <td class="right">0,00</td>
                            </tr>
                            <tr>
                                <td class="bold">Total</td>
                                <td class="right bold">{{ number_format($transaction->total_harga, 2, ',', '.') }}</td>
                            </tr>
                        </table>
                        <div class="dashed-line"></div>
                        <table>
                            <tr>
                                <td>Bayar</td>
                                <td class="right">{{ number_format($transaction->bayar, 2, ',', '.') }}</td>
                            </tr>
                            <tr>
                                <td>Kembali</td>
                                <td class="right">{{ number_format($transaction->kembali, 2, ',', '.') }}</td>
                            </tr>
                        </table>
                        <div class="dashed-line"></div>
                        <p class="center mt-5">Terima Kasih Telah Berkunjung</p>
                    </body>
                    </html>
                `);

                function generateProductRows() {
                    let rows = '';
                    @foreach ($transaction->products as $product)
                        rows += `
                            <tr>
                                <td>{{ $product->nama_barang }}</td>
                                <td class="right">{{ $product->pivot->quantity }}</td>
                                <td class="right">{{ number_format($product->pivot->price, 2, ',', '.') }}</td>
                                <td class="right">{{ number_format($product->pivot->quantity * $product->pivot->price, 2, ',', '.') }}</td>
                            </tr>
                        `;
                    @endforeach
                    return rows;
                }

                printWindow.document.close();
                printWindow.focus();
                setTimeout(function() {
                    printWindow.print();
                    printWindow.close();
                }, 250);
            }
        </script>
    @endpush
</x-app-layout>
