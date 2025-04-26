<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-gray-900 leading-tight">
            {{ __('Transaksi') }}
        </h2>
    </x-slot>

    <div class="container mx-auto p-6">
        <form id="transaction_form" action="{{ route('transactions.store') }}" method="POST">
            @csrf
            <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">
                <!-- Left Side - Categories and Products -->
                <div class="lg:col-span-3">
                    <!-- Transaction Header -->
                    <div class="bg-white rounded-lg shadow-md p-4 mb-4">
                        <div class="flex justify-between items-center mb-2">
                            <div class="flex items-center space-x-4">
                                <div class="bg-blue-100 p-3 rounded-full">
                                    <x-heroicon-s-shopping-cart class="w-6 h-6 text-blue-700" />
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Kode Transaksi</p>
                                    <p id="kode_transaksi" class="text-xl font-semibold">{{ $transactionCode }}</p>
                                    <input type="hidden" name="kode_transaksi" value="{{ $transactionCode }}">
                                </div>
                            </div>
                            <div class="flex space-x-2">
                                <button type="button"
                                    class="flex items-center bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition"
                                    onclick="openScanModal()">
                                    <x-heroicon-s-qrcode class="w-5 h-5 mr-2" />
                                    Scan Barcode
                                </button>
                                <button id="btn_cancel_order" type="button"
                                    class="flex items-center bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg transition">
                                    <x-heroicon-s-trash class="w-5 h-5 mr-2" />
                                    Batal
                                </button>
                            </div>
                        </div>
                        <div class="flex items-center mt-2">
                            <div class="relative flex-1">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <x-heroicon-s-search class="h-5 w-5 text-gray-400" />
                                </div>
                                <input id="productSearchInput"
                                    class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                    placeholder="Cari produk..." type="search">
                            </div>
                        </div>
                    </div>

                    <!-- Category Filter Tabs -->
                    <div class="bg-white rounded-lg shadow-md p-4 mb-4">
                        <div class="mb-3 font-medium text-gray-700">Kategori</div>
                        <div class="flex overflow-x-auto space-x-2 pb-2 categories-container">
                            <button type="button"
                                class="category-btn active px-4 py-2 rounded-full bg-blue-500 text-white whitespace-nowrap"
                                data-category="all">
                                Semua
                            </button>
                            <!-- Categories will be loaded dynamically here -->
                        </div>
                    </div>

                    <!-- Products Grid -->
                    <div class="bg-white rounded-lg shadow-md p-4">
                        <div class="mb-3 font-medium text-gray-700">Produk</div>
                        <div id="products-grid" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                            <!-- Products will be loaded dynamically here -->
                        </div>
                        <div id="no-products" class="hidden text-center py-8 text-gray-500">
                            Tidak ada produk yang ditemukan
                        </div>
                        <div id="loading-products" class="text-center py-8">
                            <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-gray-900">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Side - Order Summary and Payment -->
                <div class="lg:col-span-2">
                    <!-- Order List -->
                    <div class="bg-white rounded-lg shadow-md p-4 mb-4">
                        <div class="flex items-center mb-4">
                            <x-heroicon-s-clipboard-list class="w-6 h-6 text-gray-500 mr-2" />
                            <span class="font-medium text-gray-700">Daftar Pesanan</span>
                        </div>
                        <div class="h-64 overflow-y-auto mb-4">
                            <table id="order_list" class="min-w-full">
                                <thead class="bg-gray-50 sticky top-0">
                                    <tr>
                                        <th
                                            class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Item</th>
                                        <th
                                            class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Harga</th>
                                        <th
                                            class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Qty</th>
                                        <th
                                            class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Total</th>
                                        <th
                                            class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <!-- Order rows will be dynamically added here -->
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="bg-white rounded-lg shadow-md p-4 mb-4">
                        <div class="flex items-center mb-4">
                            <x-icons.percent-badge class="w-6 h-6 text-gray-500 mr-2" />
                            <span class="font-medium text-gray-700">Diskon</span>
                        </div>
                        <div class="space-y-3">
                            <div class="flex space-x-2">
                                <div class="relative flex-1">
                                    <input type="text" id="discount_code" placeholder="Masukkan kode diskon"
                                        class="block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                </div>
                                <button type="button" id="btn_apply_discount" class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-2 rounded-lg text-sm">
                                    Terapkan
                                </button>
                            </div>
                            <div id="discount_info" class="hidden">
                                <div class="flex justify-between items-center text-sm">
                                    <span id="discount_name" class="text-gray-700">Nama Diskon</span>
                                    <span class="text-red-600">- Rp <span id="discount_amount">0</span></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Info -->
                    <div class="bg-white rounded-lg shadow-md p-4">
                        <div class="mb-4">
                            <div class="grid grid-cols-1 gap-4">
                                <div>
                                    <div class="text-sm text-gray-600">Detail Transaksi</div>
                                    <table class="w-full text-sm mt-2">
                                        <tr>
                                            <td class="py-1 text-gray-500">Tanggal</td>
                                            <td class="py-1 text-right">{{ date('d M, Y') }}</td>
                                        </tr>
                                        <tr>
                                            <td class="py-1 text-gray-500">Waktu</td>
                                            <td class="py-1 text-right">{{ date('H:i') }}</td>
                                        </tr>
                                        <tr>
                                            <td class="py-1 text-gray-500">Kasir</td>
                                            <td class="py-1 text-right" id="kasir_name">{{ auth()->user()->name }}</td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="border-t pt-4">
                                    <div class="flex justify-between items-center">
                                        <span class="font-medium">Total</span>
                                        <span class="text-xl font-bold">Rp <span id="total_bayar">0.00</span></span>
                                    </div>
                                </div>
                                <div>
                                    <label for="input_bayar" class="block text-sm font-medium text-gray-700">Jumlah
                                        Bayar</label>
                                    <div class="mt-1 relative rounded-md shadow-sm">
                                        <div
                                            class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <span class="text-gray-500 sm:text-sm">Rp</span>
                                        </div>
                                        <input type="number" id="input_bayar" name="bayar"
                                            class="focus:ring-blue-500 focus:border-blue-500 block w-full pl-12 pr-12 sm:text-sm border-gray-300 rounded-md"
                                            placeholder="0">
                                    </div>
                                </div>
                                <div>
                                    <div class="flex justify-between items-center">
                                        <span class="text-sm font-medium text-gray-700">Kembali</span>
                                        <span class="text-lg font-bold text-green-600">Rp <span
                                                id="total_kembali">0.00</span></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="flex space-x-3">
                            <button type="button" id="btn_quick_cash"
                                class="flex-1 bg-gray-200 hover:bg-gray-300 py-2 px-4 rounded-lg text-gray-700 font-medium">
                                Uang Pas
                            </button>
                            <button type="submit" id="btn_transaction"
                                class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg disabled:opacity-50 disabled:cursor-not-allowed"
                                disabled>
                                Bayar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        <!-- Barcode Scan Modal -->
        <div id="scanModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 hidden">
            <div class="bg-white rounded-lg shadow-md max-w-md w-full p-6">
                <div class="flex justify-between items-center mb-4">
                    <h5 class="text-lg font-semibold">Scan Barcode</h5>
                    <button type="button" class="text-gray-600 hover:text-gray-900" onclick="closeScanModal()">
                        <span class="text-2xl">&times;</span>
                    </button>
                </div>
                <div id="scanner-container" class="mb-4 h-64 bg-gray-200"></div>
                <input type="text" id="barcode-result" class="w-full p-2 border rounded mb-4"
                    placeholder="Hasil scan barcode" readonly>
                <div class="flex justify-end space-x-4">
                    <button type="button"
                        class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition"
                        onclick="addScannedProduct()">Tambahkan</button>
                    <button type="button" class="bg-gray-200 hover:bg-gray-300 px-4 py-2 rounded-lg transition"
                        onclick="resetScanner()">Ulangi</button>
                </div>
            </div>
        </div>

        <!-- Notification Container -->
        <div id="notificationContainer" class="fixed bottom-4 right-4 z-50"></div>
    </div>

    <!-- JavaScript -->
    @push('scripts')
        <script src="https://unpkg.com/quagga@0.12.1/dist/quagga.min.js"></script>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            $(document).ready(function() {
                const $discount_code = $('#discount_code');
const $discount_info = $('#discount_info');
const $discount_name = $('#discount_name');
const $discount_amount = $('#discount_amount');
const $btn_apply_discount = $('#btn_apply_discount');
let currentDiscount = null;
                let orders = [];
                let allProducts = [];
                let categories = [];
                const $total_bayar = $('#total_bayar');
                const $input_bayar = $('#input_bayar');
                const $total_kembali = $('#total_kembali');
                const $btn_transaction = $('#btn_transaction');
                const $productSearchInput = $('#productSearchInput');
                const $scanModal = $('#scanModal');
                const $kode_transaksi_display = $('#kode_transaksi');
                const $kode_transaksi_input = $('input[name="kode_transaksi"]');
                const $productsGrid = $('#products-grid');
                const $noProducts = $('#no-products');
                const $loadingProducts = $('#loading-products');

                // Load categories on page load
                loadCategories();

                // Load all products on page load
                loadProducts('all');

                // Quick cash button
                $('#btn_quick_cash').on('click', function() {
                    const total = parseFloat($total_bayar.text().replace(/\./g, '').replace(',', '.'));
                    $input_bayar.val(total);
                    calculateKembali();
                });

                // Standard error handling function
                function handleTransactionError(error, defaultMessage = 'Terjadi kesalahan pada server') {
                    console.error('Transaction error:', error);
                    Swal.close(); // Close any open loading dialogs

                    let errorMessage = defaultMessage;
                    if (error.responseJSON && error.responseJSON.message) {
                        errorMessage = error.responseJSON.message;
                    } else if (typeof error === 'string') {
                        errorMessage = error;
                    }

                    showNotification(errorMessage, 'error');
                }

                function loadCategories() {
                    $.ajax({
                        url: '{{ route('categories.list') }}',
                        method: 'GET',
                        success: function(response) {
                            categories = response;
                            const $categoriesContainer = $('.categories-container');

                            // Add categories after "All" button
                            categories.forEach(category => {
                                const $categoryBtn = $(`<button type="button"
                    class="category-btn px-4 py-2 rounded-full bg-gray-200 hover:bg-gray-300 text-gray-700 whitespace-nowrap"
                    data-category="${category.id}">
                    ${category.name}
                </button>`);
                                $categoriesContainer.append($categoryBtn);
                            });

                            // Category button click handler
                            $('.category-btn').on('click', function() {
                                $('.category-btn').removeClass('active bg-blue-500 text-white')
                                    .addClass('bg-gray-200 text-gray-700');
                                $(this).addClass('active bg-blue-500 text-white').removeClass(
                                    'bg-gray-200 text-gray-700');
                                const categoryId = $(this).data('category');
                                loadProducts(categoryId);
                            });
                        },
                        error: function(xhr) {
                            console.error('Error loading categories:', xhr.responseText);
                        }
                    });
                }

                function formatRupiah(amount) {
                    return parseFloat(amount).toLocaleString('id-ID');
                }

                function loadProducts(categoryId) {
                    $productsGrid.empty();
                    $noProducts.addClass('hidden');
                    $loadingProducts.removeClass('hidden');

                    $.ajax({
                        url: '{{ route('products.by.category') }}',
                        method: 'GET',
                        data: {
                            category_id: categoryId
                        },
                        success: function(response) {
                            allProducts = response;
                            displayProducts(allProducts);
                        },
                        error: function(xhr) {
                            console.error('Error loading products:', xhr.responseText);
                            showNotification('Gagal memuat produk', 'error');
                            $loadingProducts.addClass('hidden');
                        }
                    });
                }

                function displayProducts(products) {
                    $productsGrid.empty();
                    $loadingProducts.addClass('hidden');

                    if (products.length === 0) {
                        $noProducts.removeClass('hidden');
                        return;
                    }

                    $noProducts.addClass('hidden');

                    products.forEach(product => {
                        const stockStatus = product.stok > 0 ?
                            `<span class="text-green-500">${product.stok} tersedia</span>` :
                            '<span class="text-red-500">Stok habis</span>';

                        const productCard = $(`
            <div class="product-card bg-white border rounded-lg overflow-hidden shadow-sm hover:shadow-md transition" data-id="${product.id}">
                <div class="p-3">
                    <div class="flex justify-between items-start">
                        <h3 class="font-medium text-gray-800 truncate" title="${product.nama_barang}">${product.nama_barang}</h3>
                        <span class="text-xs bg-blue-100 text-blue-800 px-1.5 py-0.5 rounded">${product.kode_barang}</span>
                    </div>
                    <div class="mt-1">
                        <div class="text-lg font-bold text-gray-900">Rp ${formatRupiah(product.harga)}</div>
                        <div class="text-xs mt-1">${stockStatus}</div>
                    </div>
                </div>
                <div class="bg-gray-50 px-3 py-2">
                    <button
                        type="button"
                        class="w-full bg-blue-500 hover:bg-blue-600 text-white text-sm py-1 px-2 rounded ${product.stok <= 0 ? 'opacity-50 cursor-not-allowed' : ''}"
                        onclick="addProductById(${product.id})"
                        ${product.stok <= 0 ? 'disabled' : ''}>
                        Tambah ke Keranjang
                    </button>
                </div>
            </div>
        `);

                        $productsGrid.append(productCard);
                    });
                }

                function addToOrder(id, nama, harga, stok) {
                    const existingOrder = orders.find(o => o.id == id);
                    if (existingOrder) {
                        if (existingOrder.jumlah < stok) {
                            existingOrder.jumlah += 1;
                        } else {
                            showNotification('Stok tidak mencukupi', 'error');
                            return;
                        }
                    } else {
                        const order = {
                            id,
                            nama,
                            harga: parseFloat(harga),
                            jumlah: 1,
                            stok
                        };
                        orders.push(order);
                    }
                    updateOrderList();
                    calculateTotal();
                    showNotification(`${nama} ditambahkan ke pesanan`, 'success');
                }

                function updateOrderList() {
                    const $orderList = $('#order_list tbody');
                    $orderList.empty();

                    if (orders.length === 0) {
                        $orderList.html(
                            '<tr><td colspan="5" class="text-center py-4 text-gray-500">Belum ada item</td></tr>');
                        return;
                    }

                    orders.forEach((order, index) => {
                        const $row = $('<tr>').html(`
                        <td class="px-4 py-2">
                            <div class="font-medium text-gray-800">${order.nama}</div>
                        </td>
                        <td class="px-4 py-2 text-right">Rp ${parseFloat(order.harga).toLocaleString('id-ID')}</td>
                        <td class="px-4 py-2 text-center">
                            <div class="flex items-center justify-center">
                                <button type="button" class="decrement-qty text-gray-500 hover:text-gray-700 p-1" data-index="${index}">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
                                    </svg>
                                </button>
                                <input type="number" min="1" max="${order.stok}" value="${order.jumlah}" class="w-12 text-center bg-gray-100 border-none focus:ring-0 px-0" data-index="${index}">
                                <button type="button" class="increment-qty text-gray-500 hover:text-gray-700 p-1" data-index="${index}">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                    </svg>
                                </button>
                            </div>
                        </td>
<td class="px-4 py-2 text-right">Rp ${parseFloat(order.harga * order.jumlah).toLocaleString('id-ID')}</td>
                        <td class="px-4 py-2 text-center">
                            <button type="button" class="text-red-500 hover:text-red-700 cancel-order-item" data-index="${index}">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        </td>
                    `);
                        $orderList.append($row);
                    });
                }

                function updateJumlah(index, jumlah) {
                    const qty = parseInt(jumlah);
                    const order = orders[index];
                    if (isNaN(qty) || qty < 1) {
                        order.jumlah = 1;
                    } else if (qty > order.stok) {
                        order.jumlah = order.stok;
                        showNotification('Jumlah melebihi stok yang tersedia', 'error');
                    } else {
                        order.jumlah = qty;
                    }
                    updateOrderList();
                    calculateTotal();
                }

                function incrementQuantity(index) {
                    const order = orders[index];
                    if (order.jumlah < order.stok) {
                        order.jumlah += 1;
                        updateOrderList();
                        calculateTotal();
                    } else {
                        showNotification('Jumlah melebihi stok yang tersedia', 'error');
                    }
                }

                function decrementQuantity(index) {
                    const order = orders[index];
                    if (order.jumlah > 1) {
                        order.jumlah -= 1;
                        updateOrderList();
                        calculateTotal();
                    }
                }

                function removeOrder(index) {
                    const removedOrder = orders.splice(index, 1)[0];
                    updateOrderList();
                    calculateTotal();
                    showNotification(`${removedOrder.nama} dihapus dari pesanan`, 'info');
                }

                function calculateTotal() {
    let subtotal = orders.reduce((sum, order) => sum + (order.harga * order.jumlah), 0);
    let discountValue = currentDiscount ? parseFloat(currentDiscount.amount) : 0;

    // Make sure discount doesn't exceed subtotal
    discountValue = Math.min(discountValue, subtotal);

    let total = subtotal - discountValue;

    // Update UI
    $total_bayar.text(total.toLocaleString('id-ID'));
    calculateKembali();
}

                function calculateKembali() {
                    const bayar = parseFloat($input_bayar.val()) || 0;
                    const total = parseFloat($total_bayar.text().replace(/\./g, '').replace(',', '.'));
                    const kembali = bayar - total;
                    $total_kembali.text(kembali.toLocaleString('id-ID'));
                    $btn_transaction.prop('disabled', kembali < 0 || total === 0);
                }

                $input_bayar.on('input', calculateKembali);

                // Product search functionality
                $productSearchInput.on('input', function() {
                    const query = $(this).val().trim().toLowerCase();
                    if (query.length === 0) {
                        displayProducts(allProducts);
                        return;
                    }

                    const filteredProducts = allProducts.filter(product =>
                        product.nama_barang.toLowerCase().includes(query) ||
                        product.kode_barang.toLowerCase().includes(query)
                    );

                    displayProducts(filteredProducts);
                });

                // Handle quantity inputs
                $(document).on('change', '#order_list input[type="number"]', function() {
                    const index = $(this).data('index');
                    const jumlah = parseInt($(this).val());
                    updateJumlah(index, jumlah);
                });

                // Handle increment/decrement buttons
                $(document).on('click', '.increment-qty', function() {
                    const index = $(this).data('index');
                    incrementQuantity(index);
                });

                $(document).on('click', '.decrement-qty', function() {
                    const index = $(this).data('index');
                    decrementQuantity(index);
                });

                // Handle remove button
                $(document).on('click', '.cancel-order-item', function() {
                    const index = $(this).data('index');
                    cancelOrderItem(index);
                });

                function cancelOrderItem(index) {
                    Swal.fire({
                        title: 'Batalkan Produk?',
                        text: "Apakah Anda yakin ingin menghapus produk ini dari pesanan?",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Ya, Hapus!',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            removeOrder(index);
                        }
                    });
                }

                // Add scanned product functionality
                function addScannedProduct() {
                    const barcode = $('#barcode-result').val().trim();
                    if (barcode.length === 0) {
                        showNotification('Barcode kosong', 'error');
                        return;
                    }
                    $.ajax({
                        url: `{{ url('transactions/get-product') }}/${barcode}`,
                        method: 'GET',
                        success: function(response) {
                            if (response.success) {
                                const product = response.product;
                                if (product.stok <= 0) {
                                    showNotification('Stok produk habis', 'error');
                                    return;
                                }
                                addToOrder(product.id, product.nama_barang, product.harga, product.stok);
                            } else {
                                showNotification('Produk tidak ditemukan', 'error');
                            }
                        },
                        error: function(xhr) {
                            console.error('Error getting product:', xhr.responseText);
                            showNotification('Terjadi kesalahan saat mengambil data produk', 'error');
                        }
                    });
                }

                // Add product by ID (for click on product card)
                function addProductById(productId) {
                    const product = allProducts.find(p => p.id === productId);
                    if (product) {
                        addToOrder(product.id, product.nama_barang, product.harga, product.stok);
                    } else {
                        showNotification('Produk tidak ditemukan', 'error');
                    }
                }
                window.addProductById = addProductById;

                function showNotification(message, type = 'info') {
                    const icon = type === 'error' ? 'error' : (type === 'success' ? 'success' : 'info');
                    Swal.fire({
                        icon: icon,
                        title: type.charAt(0).toUpperCase() + type.slice(1),
                        text: message,
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true,
                        didOpen: (toast) => {
                            toast.addEventListener('mouseenter', Swal.stopTimer)
                            toast.addEventListener('mouseleave', Swal.resumeTimer)
                        }
                    });
                }

                $btn_apply_discount.on('click', function() {
    applyDiscount();
});

function applyDiscount() {
    const code = $discount_code.val().trim();

    if (!code) {
        showNotification('Masukkan kode diskon', 'warning');
        return;
    }

    if (orders.length === 0) {
        showNotification('Tambahkan produk terlebih dahulu', 'warning');
        $discount_code.val('');
        return;
    }

    // Show loading
    $btn_apply_discount.prop('disabled', true).html('<span class="inline-block animate-spin h-4 w-4 border-2 border-white rounded-full border-t-transparent"></span>');

    $.ajax({
        url: '{{ route('discounts.check') }}',
        method: 'POST',
        data: {
            _token: '{{ csrf_token() }}',
            code: code,
            orders: orders,
            total_amount: parseFloat($total_bayar.text().replace(/\./g, '').replace(',', '.'))
        },
        success: function(response) {
            $btn_apply_discount.prop('disabled', false).text('Terapkan');

            if (response.valid) {
                currentDiscount = {
                    code: code,
                    name: response.discount_name,
                    amount: response.discount_amount
                };

                // Show discount info
                $discount_name.text(response.discount_name);
                $discount_amount.text(parseFloat(response.discount_amount).toLocaleString('id-ID'));
                $discount_info.removeClass('hidden');

                // Update total after discount
                calculateTotal();

                showNotification('Diskon berhasil diterapkan', 'success');
            } else {
                showNotification(response.message || 'Kode diskon tidak valid', 'error');
                resetDiscount();
            }
        },
        error: function(xhr) {
            $btn_apply_discount.prop('disabled', false).text('Terapkan');
            console.error('Error checking discount:', xhr.responseText);
            showNotification('Gagal memeriksa kode diskon', 'error');
        }
    });
}

// Function to reset discount
function resetDiscount() {
    currentDiscount = null;
    $discount_code.val('');
    $discount_info.addClass('hidden');
    calculateTotal();
}

// Add discount reset to the reset transaction function
function resetTransaction() {
    orders = [];
    resetDiscount(); // Add this line to existing function
    updateOrderList();
    calculateTotal();
    $input_bayar.val('');
    generateNewTransactionCode();
}
                // Submit transaction
                $('#transaction_form').on('submit', function(e) {
                    e.preventDefault();
                    if (orders.length === 0) {
                        showNotification('Mohon tambahkan barang ke daftar pesanan terlebih dahulu.',
                            'warning');
                        return;
                    }
                    if (parseFloat($total_kembali.text()) < 0) {
                        showNotification('Jumlah bayar kurang dari total belanja.', 'warning');
                        return;
                    }

                    Swal.fire({
                        title: 'Konfirmasi Transaksi',
                        text: "Apakah Anda yakin ingin menyelesaikan transaksi ini?",
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Ya, Selesaikan!',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            submitTransaction();
                        }
                    });
                });

                function submitTransaction() {
    const formData = {
        kode_transaksi: $kode_transaksi_input.val(),
        total_harga: parseFloat($total_bayar.text().replace(/\./g, '').replace(',', '.')),
        bayar: parseFloat($input_bayar.val()),
        kembali: parseFloat($total_kembali.text().replace(/\./g, '').replace(',', '.')),
        items: orders.map(order => ({
            product_id: order.id,
            quantity: order.jumlah,
            price: order.harga
        })),
        discount_code: currentDiscount ? currentDiscount.code : null,
        discount_amount: currentDiscount ? parseFloat(currentDiscount.amount) : 0
    };

                    // Show loading indicator
                    Swal.fire({
                        title: 'Memproses transaksi...',
                        text: 'Mohon tunggu sebentar',
                        allowOutsideClick: false,
                        showConfirmButton: false,
                        willOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    $.ajax({
                        url: '{{ route('transactions.store') }}',
                        method: 'POST',
                        data: JSON.stringify(formData),
                        contentType: 'application/json',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            console.log('Transaction success:', response);
                            Swal.close();

                            if (response.success) {
                                displayReceipt(response.transaction);
                            } else {
                                showNotification('Terjadi kesalahan: ' + response.message, 'error');
                            }
                        },
                        error: function(xhr) {
                            handleTransactionError(xhr);
                        }
                    });
                }
                // New function to show receipt and handle transaction reset
                // Standardize receipt display
                function displayReceipt(transaction) {
                    try {
                        const tanggal = new Date(transaction.created_at || Date.now()).toLocaleDateString('id-ID', {
                            day: 'numeric',
                            month: 'short',
                            year: 'numeric'
                        });
                        const waktu = new Date(transaction.created_at || Date.now()).toLocaleTimeString('id-ID', {
                            hour: '2-digit',
                            minute: '2-digit'
                        });
                        const kasir = $('#kasir_name').text();

                        // Build product list html
                        let productsHtml = '<ul class="list-disc list-inside">';
        if (transaction.products && Array.isArray(transaction.products) && transaction.products.length > 0) {
            transaction.products.forEach(product => {
                productsHtml +=
                    `<li>${product.nama_barang} - ${product.pivot.quantity} x Rp ${formatRupiah(product.pivot.price)}</li>`;
            });
        } else {
            productsHtml = '<p>Data produk tidak tersedia</p>';
        }
        productsHtml += '</ul>';

        // Add discount info if exists
        let discountHtml = '';
        if (transaction.discount_amount && transaction.discount_amount > 0) {
            discountHtml = `
                <div class="mt-3">
                    <p><strong>Subtotal:</strong> Rp ${formatRupiah(parseFloat(transaction.total_harga) + parseFloat(transaction.discount_amount))}</p>
                    <p class="text-red-600"><strong>Diskon:</strong> - Rp ${formatRupiah(transaction.discount_amount)}</p>
                </div>
            `;
        }

        Swal.fire({
            title: 'Transaksi Berhasil!',
            html: `
                <div class="text-left">
                    <p><strong>Kode Transaksi:</strong> ${transaction.kode_transaksi}</p>
                    <p><strong>Tanggal:</strong> ${tanggal} ${waktu}</p>
                    <p><strong>Kasir:</strong> ${kasir}</p>
                    ${discountHtml}
                    <p><strong>Total:</strong> Rp ${formatRupiah(transaction.total_harga)}</p>
                    <p><strong>Dibayar:</strong> Rp ${formatRupiah(transaction.bayar)}</p>
                    <p><strong>Kembali:</strong> Rp ${formatRupiah(transaction.kembali)}</p>
                    <hr class="my-3">
                    <h4 class="text-center font-bold mb-2">Daftar Produk</h4>
                    ${productsHtml}
                </div>
            `,
                            icon: 'success',
                            showCancelButton: true,
                            confirmButtonText: 'Lihat Semua Transaksi',
                            cancelButtonText: 'Transaksi Baru',
                            reverseButtons: true,
                            allowOutsideClick: false
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.location.href = '{{ route('transactions.index') }}';
                            } else {
                                resetForNewTransaction();
                            }
                        });
                    } catch (error) {
                        console.error('Error displaying receipt:', error);
                        showNotification('Transaksi berhasil tetapi gagal menampilkan struk', 'warning');
                        resetForNewTransaction();
                    }
                }
                // Add this new function to properly reset for a new transaction
                function resetForNewTransaction() {
    // Clear orders array
    orders = [];
    updateOrderList();
    calculateTotal();

    // Clear payment amount input
    $input_bayar.val('');

    // Reset discount
    resetDiscount();

    // Get a new transaction code from the server
    fetchNewTransactionCode();
}

                // This function requests a fresh transaction code from server
                function fetchNewTransactionCode() {
                    // Show loading state
                    $kode_transaksi_display.html('<span class="animate-pulse">Memuat...</span>');

                    $.ajax({
                        url: '{{ route('transactions.generate-code') }}',
                        method: 'GET',
                        success: function(response) {
                            console.log('New transaction code generated:', response.code);
                            $kode_transaksi_display.text(response.code);
                            $kode_transaksi_input.val(response.code);
                            showNotification('Siap untuk transaksi baru', 'success');
                        },
                        error: function(xhr) {
                            console.error('Error generating code:', xhr.responseText);
                            showNotification('Gagal membuat kode transaksi baru', 'error');
                        }
                    });
                }
                // Replace the existing generateNewTransactionCode function with this one
                function generateNewTransactionCode() {
                    fetchNewTransactionCode();
                }

                // Cancel entire order
                $('#btn_cancel_order').on('click', function() {
                    Swal.fire({
                        title: 'Batalkan Pesanan?',
                        text: "Semua item dalam pesanan akan dihapus. Apakah Anda yakin?",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Ya, Batalkan!',
                        cancelButtonText: 'Tidak'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            resetTransaction();
                            showNotification('Pesanan telah dibatalkan', 'info');
                        }
                    });
                });

                function resetTransaction() {
                    orders = [];
                    updateOrderList();
                    calculateTotal();
                    $input_bayar.val('');
                    generateNewTransactionCode();
                }

                function generateNewTransactionCode() {
                    $.ajax({
                        url: '{{ route('transactions.generate-code') }}',
                        method: 'GET',
                        success: function(response) {
                            $kode_transaksi_display.text(response.code);
                            $kode_transaksi_input.val(response.code);
                        },
                        error: function(xhr) {
                            console.error('Error generating new transaction code:', xhr.responseText);
                        }
                    });
                }

                function displaySuccessModal(transaction) {
                    const tanggal = new Date(transaction.created_at).toLocaleDateString('id-ID', {
                        day: 'numeric',
                        month: 'short',
                        year: 'numeric'
                    });
                    const waktu = new Date(transaction.created_at).toLocaleTimeString('id-ID', {
                        hour: '2-digit',
                        minute: '2-digit'
                    });
                    const kasir = $('#kasir_name').text();

                    Swal.fire({
                        title: 'Transaksi Berhasil',
                        html: `
                <div class="text-left">
                    <p><strong>Kode Transaksi:</strong> ${transaction.kode_transaksi}</p>
                    <p><strong>Tanggal:</strong> ${tanggal} ${waktu}</p>
                    <p><strong>Kasir:</strong> ${kasir}</p>
                    <p><strong>Total Bayar:</strong> Rp ${parseFloat(transaction.total_harga).toLocaleString('id-ID')}</p>
                    <p><strong>Total Kembali:</strong> Rp ${parseFloat(transaction.kembali).toLocaleString('id-ID')}</p>
                    <h4 class="mt-4 mb-2 font-bold">Daftar Produk:</h4>
                    <ul class="list-disc list-inside">
                        ${transaction.products.map(product => `
                                        <li>${product.nama_barang} - ${product.pivot.quantity} x Rp ${product.pivot.price.toFixed(3)}</li>
                                        `).join('')}
                    </ul>
                </div>
                `,
                        icon: 'success',
                        confirmButtonText: 'OK',
                    }).then((result) => {
                        if (result.isConfirmed) {
                            resetTransaction();
                        }
                    });
                }

                // Barcode scanner functions
                function openScanModal() {
                    $scanModal.removeClass('hidden');
                    initQuagga();
                }

                function closeScanModal() {
                    $scanModal.addClass('hidden');
                    if (Quagga) {
                        Quagga.stop();
                    }
                }

                function resetScanner() {
                    $('#barcode-result').val('');
                    if (Quagga) {
                        Quagga.stop();
                        initQuagga();
                    }
                }
                let quaggaInitialized = false;

                function initQuagga() {
                    if (quaggaInitialized) {
                        Quagga.start();
                        return;
                    }
                    if (typeof Quagga === 'undefined') {
                        showNotification('Library scanner tidak dimuat', 'error');
                        return;
                    }
                    Quagga.init({
                        inputStream: {
                            name: "Live",
                            type: "LiveStream",
                            target: document.querySelector("#scanner-container"),
                            constraints: {
                                width: 480,
                                height: 320,
                                facingMode: "environment"
                            },
                        },
                        decoder: {
                            readers: [
                                "code_128_reader",
                                "ean_reader",
                                "ean_8_reader",
                                "code_39_reader",
                                "code_39_vin_reader",
                                "codabar_reader",
                                "upc_reader",
                                "upc_e_reader",
                                "i2of5_reader"
                            ]
                        },
                        locate: true
                    }, function(err) {
                        if (err) {
                            console.error("Failed to initialize scanner:", err);
                            showNotification('Gagal menginisialisasi scanner', 'error');
                            return;
                        }
                        quaggaInitialized = true;
                        Quagga.start();
                    });

                    Quagga.onDetected(function(result) {
                        const code = result.codeResult.code;
                        $('#barcode-result').val(code);
                        Quagga.stop();
                        $scanModal.addClass('hidden');
                        addScannedProduct();
                    });
                }

                // Export functions for global access
                window.cancelOrderItem = cancelOrderItem;
                window.openScanModal = openScanModal;
                window.closeScanModal = closeScanModal;
                window.addScannedProduct = addScannedProduct;
                window.resetScanner = resetScanner;
            });
        </script>
    @endpush
</x-app-layout>
