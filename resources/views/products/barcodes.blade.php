<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Barcode Produk') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="flex justify-between mb-4">
                        <a href="{{ route('products.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg no-print">
                            <i class="fas fa-arrow-left mr-2"></i> Kembali
                        </a>
                        <x-button onclick="window.print()" class="no-print">
                            <i class="fas fa-print mr-2"></i> Cetak Barcode
                        </x-button>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4 print-grid">
                        @foreach($products as $product)
                            <div class="border rounded-lg p-4 text-center barcode-item">
                                <!-- Barcode container with improved centering -->
                                <div class="barcode-container flex justify-center items-center mb-2" data-product-code="{{ $product->kode_barang ?? $product->id }}"></div>
                                <p class="mt-2 font-semibold">{{ $product->nama_barang }}</p>
                                <p class="text-sm text-gray-500">{{ $product->kode_barang }}</p>
                                <p class="text-sm font-medium">Rp {{ number_format($product->harga, 0, ',', '.') }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('styles')
    <style>
        @media print {
            .no-print {
                display: none;
            }
            .print-grid {
                display: grid;
                grid-template-columns: repeat(3, 1fr);
                gap: 0.5cm;
            }
            .barcode-item {
                break-inside: avoid;
                page-break-inside: avoid;
                margin-bottom: 0.5cm;
            }
        }
        .barcode-container {
            min-height: 80px;
            display: flex !important;
            justify-content: center !important;
            align-items: center !important;
        }
        .barcode-container svg {
            margin: 0 auto;
            max-width: 100%;
        }
    </style>
    @endpush

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const barcodeContainers = document.querySelectorAll('.barcode-container');

            barcodeContainers.forEach(container => {
                const code = container.dataset.productCode;

                // Create SVG element for barcode
                const svg = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
                container.appendChild(svg);

                // Generate barcode
                JsBarcode(svg, code, {
                    format: "CODE128",
                    lineColor: "#000",
                    width: 2,
                    height: 50,
                    displayValue: true,
                    fontSize: 14,
                    margin: 10,
                    textAlign: "center",
                    textPosition: "bottom",
                    textMargin: 8
                });
            });
        });
    </script>
    @endpush
</x-app-layout>
