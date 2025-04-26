<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DiscountController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\MemberTierController;


Route::redirect('/', '/dashboard');

Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');
Route::middleware('auth')->group(function () {


    Route::post('/discounts/check', [DiscountController::class, 'checkDiscount'])->name('discounts.check');
    Route::resource('discounts', DiscountController::class);
    Route::get('/discounts/applicable', [DiscountController::class, 'getApplicableDiscounts'])->name('discounts.applicable');


Route::resource('members', MemberController::class);
Route::get('/members/search', [MemberController::class, 'search'])->name('members.search');
Route::post('/members/{member}/adjust-points', [MemberController::class, 'adjustPoints'])->name('members.adjust-points');


Route::resource('member-tiers', MemberTierController::class);

Route::get('/transactions/product-by-barcode/{barcode}', [TransactionController::class, 'getProductByBarcode'])->name('transactions.product-by-barcode');


    Route::get('/products/barcodes', [App\Http\Controllers\ProductController::class, 'barcodes'])->name('products.barcodes');
    Route::get('/products/by-category', [App\Http\Controllers\ProductController::class, 'getByCategory'])->name('products.by.category');
    Route::get('/categories/list', [App\Http\Controllers\CategoryController::class, 'getList'])->name('categories.list');
    Route::get('/dashboard/weekly-product-data', [DashboardController::class, 'getWeeklyProductData'])->name('dashboard.weekly-product-data');
    // Profile routes
    Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');
    Route::resource('users', UserController::class);
    Route::get('/dashboard/weekly-product-data', [DashboardController::class, 'getWeeklyProductData'])->name('dashboard.weekly-product-data');
    // Profile routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Reports routes
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/export/excel', [ReportController::class, 'exportExcel'])->name('reports.export.excel');
    Route::get('/reports/export/pdf', [ReportController::class, 'exportPdf'])->name('reports.export.pdf');

    // Account routes
    Route::get('account', [AccountController::class, 'index'])->name('account.index');

    // Product routes
    Route::get('products', [ProductController::class, 'index'])->name('products.index');
    Route::get('products/create', [ProductController::class, 'create'])->name('products.create');
    Route::post('products', [ProductController::class, 'store'])->name('products.store');
    Route::get('products/{product}/edit', [ProductController::class, 'edit'])->name('products.edit');
    Route::put('products/{product}', [ProductController::class, 'update'])->name('products.update');
    Route::delete('products/{product}', [ProductController::class, 'destroy'])->name('products.destroy');

    // Transactions routes
    Route::get('/transactions', [TransactionController::class, 'index'])->name('transactions.index');
    Route::post('/transactions', [TransactionController::class, 'store'])->name('transactions.store');
    Route::get('/transactions/search', [TransactionController::class, 'searchProducts'])->name('transactions.search');
    Route::get('/transactions/get-product/{kode_barang}', [TransactionController::class, 'getProduct'])->name('transactions.get-product');
    Route::get('/transactions/generate-code', [TransactionController::class, 'generateTransactionCode'])->name('transactions.generate-code');
    Route::get('/transactions/history', [TransactionController::class, 'history'])->name('transactions.history');
    Route::get('/transactions/{id}', [TransactionController::class, 'show'])->name('transactions.show');
    Route::get('/transactions/success/{id}', [TransactionController::class, 'success'])->name('transactions.success');

    // Import Product routes
    Route::get('products/import', [ProductController::class, 'importForm'])->name('products.import.form');
    Route::post('products/import', [ProductController::class, 'import'])->name('products.import');

    // Category routes
    Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
    Route::get('/categories/create', [CategoryController::class, 'create'])->name('categories.create'); // Add this line
    Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
    Route::get('/categories/{category}/edit', [CategoryController::class, 'edit'])->name('categories.edit');
    Route::put('/categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
    Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');
});

// Buttons demo route (just a showcase)
Route::get('/buttons/text-icon', function () {
    return view('buttons-showcase.text-icon');
})->middleware(['auth'])->name('buttons.text-icon');

// Auth routes
require __DIR__ . '/auth.php';
