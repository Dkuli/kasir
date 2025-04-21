<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-gray-900 leading-tight">
            {{ __('Transaksi') }}
        </h2>
    </x-slot>

    <div class="container mx-auto p-6">
        <!-- Transaction Form -->
        <form id="transaction_form" action="{{ route('transactions.store') }}" method="POST" class="space-y-6">
            @csrf
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex justify-between items-center mb-4">
                    <div class="flex items-center space-x-4">
                        <div class="bg-gray-200 p-3 rounded-full">
                            <x-heroicon-s-switch-horizontal class="w-6 h-6 text-blue-700" />
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Kode Transaksi</p>
                            <p id="kode_transaksi" class="text-xl font-semibold">{{ $transactionCode }}</p>
                            <input type="hidden" name="kode_transaksi" value="{{ $transactionCode }}">
                        </div>
                    </div>
                    <div class="space-x-2 flex">
                        <button type="button"
                            class="bg-gray-200 hover:bg-gray-300 text-blue-700 p-2 rounded-full transition"
                            onclick="openProductSearch()">
                            <x-heroicon-s-search class="w-6 h-6" />
                        </button>
                        <button type="button"
                            class="bg-gray-200 hover:bg-gray-300 text-blue-700 p-2 rounded-full transition"
                            onclick="openScanModal()">
                            <x-heroicon-s-qrcode class="w-6 h-6" />
                        </button>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Order List -->
                <div class="bg-white rounded-lg shadow-md p-6 col-span-2">
                    <div class="text-lg font-semibold text-gray-700 mb-4 flex items-center space-x-3">
                        <x-heroicon-s-shopping-cart class="w-8 h-8 text-gray-500" />
                        <span>Daftar Pesanan</span>
                    </div>
                    <div class="overflow-x-auto">
                        <table id="order_list" class="table-auto w-full text-gray-700">
                            <thead>
                                <tr class="border-b border-gray-200">
                                    <th class="px-4 py-2 text-left">Nama Barang</th>
                                    <th class="px-4 py-2 text-right">Harga</th>
                                    <th class="px-4 py-2 text-center">Jumlah</th>
                                    <th class="px-4 py-2 text-right">Subtotal</th>
                                    <th class="px-4 py-2 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Order rows will be added dynamically here -->
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Payment Summary -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <div class="text-sm">
                        <table class="w-full mb-4">
                            <tr>
                                <td class="text-left text-gray-500">Tanggal</td>
                                <td class="text-right">{{ date('d M, Y') }}</td>
                            </tr>
                            <tr>
                                <td class="text-left text-gray-500">Waktu</td>
                                <td class="text-right">{{ date('H:i') }}</td>
                            </tr>
                            <tr>
                                <td class="text-left text-gray-500">Kasir</td>
                                <td class="text-right" id="kasir_name">{{ auth()->user()->name }}</td>
                            </tr>
                        </table>
                        <table class="w-full border-t border-gray-300 pt-4">
                            <tr>
                                <td class="text-left font-semibold">Total Bayar</td>
                                <td class="text-right font-semibold">Rp. <span id="total_bayar">0.00</span></td>
                            </tr>
                            <tr>
                                <td class="text-left">Bayar</td>
                                <td class="text-right">
                                    <input type="number" id="input_bayar" name="bayar"
                                        class="text-right bg-gray-100 border rounded w-full px-3 py-2" placeholder="0">
                                </td>
                            </tr>
                            <tr>
                                <td class="text-left">Kembali</td>
                                <td class="text-right text-green-500">Rp. <span id="total_kembali">0.00</span></td>
                            </tr>
                        </table>
                    </div>
                    <div class="mt-6">
                        <button type="submit" id="btn_transaction"
                            class="w-full bg-blue-700 hover:bg-blue-800 text-white px-4 py-2 rounded-lg transition disabled:opacity-50"
                            disabled>
                            Selesai
                        </button>
                    </div>
                </div>
            </div>
        </form>

        <!-- Product Search Modal -->
        <div id="productSearchModal"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 hidden">
            <div class="bg-white rounded-lg shadow-md max-w-md w-full p-6">
                <div class="flex justify-between items-center mb-4">
                    <h5 class="text-lg font-semibold">Cari Barang</h5>
                    <button type="button" class="text-gray-600 hover:text-gray-900" onclick="closeProductSearch()">
                        <span class="text-2xl">&times;</span>
                    </button>
                </div>
                <input type="text" id="productSearchInput" class="w-full p-2 border rounded mb-4"
                    placeholder="Cari barang...">
                <ul id="productSearchResults" class="space-y-2 max-h-60 overflow-y-auto">
                    <!-- Search results will be dynamically added here -->
                </ul>
            </div>
        </div>

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

        <!-- Success Modal -->
        <div id="successModal"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 hidden">
            <div class="bg-white rounded-lg shadow-md max-w-md w-full p-6">
                <div class="text-center mb-4">
                    <h4 class="font-semibold text-lg">Transaksi Berhasil</h4>
                </div>
                <div id="transactionDetails">
                    <!-- Transaction details will be dynamically added here -->
                </div>
                <div class="text-center mt-4">
                    <p class="text-green-600">Anda akan diarahkan ke halaman transaksi baru dalam 3 detik...</p>
                </div>
            </div>
        </div>

        <!-- Notification Container -->
        <div id="notificationContainer" class="fixed bottom-4 right-4 z-50"></div>
    </div>




    <!-- JavaScript Logic -->
    <script src="https://unpkg.com/quagga@0.12.1/dist/quagga.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script>
        $(document).ready(function() {
            let orders = [];
            const $total_bayar = $('#total_bayar');
            const $input_bayar = $('#input_bayar');
            const $total_kembali = $('#total_kembali');
            const $btn_transaction = $('#btn_transaction');
            const $productSearchModal = $('#productSearchModal');
            const $productSearchInput = $('#productSearchInput');
            const $productSearchResults = $('#productSearchResults');
            const $scanModal = $('#scanModal');
            const $kode_transaksi_display = $('#kode_transaksi');
            const $kode_transaksi_input = $('input[name="kode_transaksi"]');

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

                orders.forEach((order, index) => {
                    const $row = $('<tr>').html(`
                <td class="px-4 py-2">${order.nama}</td>
                <td class="px-4 py-2 text-right">Rp. ${order.harga.toFixed(2)}</td>
                <td class="px-4 py-2 text-center">
                    <input type="number" min="1" max="${order.stok}" value="${order.jumlah}" class="w-16 text-center bg-gray-100 border-none focus:ring-0" data-index="${index}">
                </td>
                <td class="px-4 py-2 text-right">Rp. ${(order.harga * order.jumlah).toFixed(2)}</td>
                <td class="px-4 py-2 text-center">
                    <button type="button" class="text-red-500 remove-order" data-index="${index}">
                        <i class="mdi mdi-delete"></i>
                    </button>
                </td>
            `);
                    $orderList.append($row);
                });
            }

            $(document).on('change', '#order_list input[type="number"]', function() {
                const index = $(this).data('index');
                const jumlah = parseInt($(this).val());
                updateJumlah(index, jumlah);
            });

            $(document).on('click', '.remove-order', function() {
                const index = $(this).data('index');
                removeOrder(index);
            });

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

            function removeOrder(index) {
                const removedOrder = orders.splice(index, 1)[0];
                updateOrderList();
                calculateTotal();
                showNotification(`${removedOrder.nama} dihapus dari pesanan`, 'info');
            }

            function calculateTotal() {
                let total = orders.reduce((sum, order) => sum + (order.harga * order.jumlah), 0);
                $total_bayar.text(total.toFixed(2));
                calculateKembali();
            }

            function calculateKembali() {
                const bayar = parseFloat($input_bayar.val()) || 0;
                const total = parseFloat($total_bayar.text());
                const kembali = bayar - total;
                $total_kembali.text(kembali.toFixed(2));
                $btn_transaction.prop('disabled', kembali < 0);
                $btn_transaction.toggleClass('opacity-50', kembali < 0);
            }

            $input_bayar.on('input', calculateKembali);

            function openProductSearch() {
                $productSearchModal.removeClass('hidden');
                $productSearchInput.focus();
            }

            function closeProductSearch() {
                $productSearchModal.addClass('hidden');
                $productSearchInput.val('');
                $productSearchResults.empty();
            }

            function displaySearchResults(results) {
                $productSearchResults.empty();
                if (results.length === 0) {
                    $productSearchResults.html('<li class="p-2 text-gray-500">Tidak ada produk ditemukan</li>');
                    return;
                }
                results.forEach(product => {
                    const $li = $('<li>').addClass(
                        'flex justify-between items-center p-2 border rounded bg-blue-50 mb-2 cursor-pointer hover:bg-blue-100'
                    ).html(`
                <div class="flex-grow">
                    <p class="font-semibold">${product.kode_barang}</p>
                    <p class="text-gray-600">${product.nama_barang}</p>
                </div>
                <div class="flex items-center space-x-2">
                    <span class="bg-gray-200 p-1 rounded"><i class="mdi mdi-cube-outline"></i></span>
                    <p>${product.stok}</p>
                </div>
            `);
                    $li.on('click', function() {
                        addToOrder(product.id, product.nama_barang, product.harga, product.stok);
                        closeProductSearch();
                    });
                    $productSearchResults.append($li);
                });
            }

            $productSearchInput.on('input', function() {
                const query = $(this).val().trim();
                if (query.length === 0) {
                    $productSearchResults.empty();
                    return;
                }
                $.ajax({
                    url: '{{ route('transactions.search') }}',
                    method: 'GET',
                    data: {
                        query: query
                    },
                    success: function(results) {
                        displaySearchResults(results);
                    },
                    error: function(xhr) {
                        console.error('Error searching products:', xhr.responseText);
                        showNotification('Terjadi kesalahan saat mencari produk', 'error');
                    }
                });
            });



            function openScanModal() {
                $scanModal.removeClass('hidden');
                initializeScanner();
            }

            function closeScanModal() {
                $scanModal.addClass('hidden');
                resetScanner();
            }

            function initializeScanner() {
                if (Quagga.initialized) {
                    Quagga.start();
                    return;
                }
                Quagga.init({
                    inputStream: {
                        name: "Live",
                        type: "LiveStream",
                        target: document.querySelector("#scanner-container"),
                        constraints: {
                            facingMode: "environment"
                        }
                    },
                    decoder: {
                        readers: ["ean_reader", "ean_8_reader", "code_39_reader", "code_128_reader"]
                    }
                }, function(err) {
                    if (err) {
                        showNotification('Gagal menginisialisasi pemindai barcode', 'error');
                        return;
                    }
                    Quagga.initialized = true;
                    Quagga.start();
                });
                Quagga.onDetected(handleDetected);
            }

            function handleDetected(result) {
                let code = result.codeResult.code;
                $('#barcode-result').val(code);
                Quagga.stop();
                $scanModal.addClass('hidden');
                addScannedProduct();
            }

            function resetScanner() {
                Quagga.stop();
                $('#barcode-result').val('');
            }

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

            function showNotification(message, type = 'info') {
                const icon = type === 'error' ? 'error' : 'success';
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
                    <p><strong>Total Bayar:</strong> Rp. ${transaction.total_harga.toFixed(2)}</p>
                    <p><strong>Total Kembali:</strong> Rp. ${transaction.kembali.toFixed(2)}</p>
                    <h4 class="mt-4 mb-2 font-bold">Daftar Produk:</h4>
                    <ul class="list-disc list-inside">
                        ${transaction.products.map(product => `
                                <li>${product.nama_barang} - ${product.pivot.quantity} x Rp. ${product.pivot.price.toFixed(2)}</li>
                            `).join('')}
                    </ul>
                </div>
            `,
                    icon: 'success',
                    confirmButtonText: 'OK',
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = '{{ route('transactions.index') }}';
                    }
                });
            }

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
                    total_harga: parseFloat($total_bayar.text()),
                    bayar: parseFloat($input_bayar.val()),
                    kembali: parseFloat($total_kembali.text()),
                    items: orders.map(order => ({
                        product_id: order.id,
                        quantity: order.jumlah,
                        price: order.harga
                    }))
                };

                $.ajax({
                    url: '{{ route('transactions.store') }}',
                    method: 'POST',
                    data: JSON.stringify(formData),
                    contentType: 'application/json',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success) {
                            window.location.href = '{{ route('transactions.success', '') }}/' +
                                response.transaction.id;
                        } else {
                            showNotification('Terjadi kesalahan: ' + response.message, 'error');
                        }
                    },
                    error: function(xhr) {
                        showNotification('Terjadi kesalahan: ' + xhr.responseJSON.message, 'error');
                    }
                });
            }

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

            // Initialize the page
            generateNewTransactionCode();

            // Function to cancel a specific product in the order list
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

            // Add event listener for cancel button on each product in order list
            $(document).on('click', '.cancel-order-item', function() {
                const index = $(this).data('index');
                cancelOrderItem(index);
            });

            // Update the updateOrderList function to include the cancel button
            function updateOrderList() {
                const $orderList = $('#order_list tbody');
                $orderList.empty();

                orders.forEach((order, index) => {
                    const $row = $('<tr>').html(`
                <td class="px-4 py-2">${order.nama}</td>
                <td class="px-4 py-2 text-right">Rp. ${order.harga.toFixed(2)}</td>
                <td class="px-4 py-2 text-center">
                    <input type="number" min="1" max="${order.stok}" value="${order.jumlah}" class="w-16 text-center bg-gray-100 border-none focus:ring-0" data-index="${index}">
                </td>
                <td class="px-4 py-2 text-right">Rp. ${(order.harga * order.jumlah).toFixed(2)}</td>
                <td class="px-4 py-2 text-center">
                    <button type="button" class="text-red-500 cancel-order-item" data-index="${index}">
                        <i class="mdi mdi-delete"></i>
                    </button>
                </td>
            `);
                    $orderList.append($row);
                });
            }

            // Export functions to window object for global access
            window.addToOrder = addToOrder;
            window.openProductSearch = openProductSearch;
            window.closeProductSearch = closeProductSearch;
            window.updateJumlah = updateJumlah;
            window.removeOrder = removeOrder;
            window.openScanModal = openScanModal;
            window.closeScanModal = closeScanModal;
            window.addScannedProduct = addScannedProduct;
            window.cancelOrderItem = cancelOrderItem;
        });
    </script>
</x-app-layout>
