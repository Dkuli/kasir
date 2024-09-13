<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ReportController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');


    Route::get('/reports/sales', [ReportController::class, 'salesReport'])->name('reports.sales');
    Route::get('/reports/form', [ReportController::class, 'showReportForm'])->name('reports.form');

//account
    Route::get('account', [AccountController::class, 'index'])->name('account.index');

//produk
    Route::get('products', [ProductController::class, 'index'])->name('products.index');
    Route::get('products/create', [ProductController::class, 'create'])->name('products.create');
    Route::post('products', [ProductController::class, 'store'])->name('products.store');
    Route::get('products/{product}/edit', [ProductController::class, 'edit'])->name('products.edit');
    Route::put('products/{product}', [ProductController::class, 'update'])->name('products.update');
    Route::delete('products/{product}', [ProductController::class, 'destroy'])->name('products.destroy');

//transaksi
Route::get('/transactions', [TransactionController::class, 'index'])->name('transactions.index');
Route::post('/transactions', [TransactionController::class, 'store'])->name('transactions.store');
Route::get('/transactions/search', [TransactionController::class, 'searchProducts'])->name('transactions.search');
Route::get('/transactions/get-product/{kode_barang}', [TransactionController::class, 'getProduct'])->name('transactions.get-product');
Route::get('/transactions/generate-code', [TransactionController::class, 'generateTransactionCode'])->name('transactions.generate-code');
Route::get('/transactions/history', [TransactionController::class, 'history'])->name('transactions.history');
Route::get('/transactions/{id}', [TransactionController::class, 'show'])->name('transactions.show');
Route::get('/transactions/success/{id}', [TransactionController::class, 'success'])->name('transactions.success');
Route::get('/transactions', [TransactionController::class, 'index'])->name('transactions.index');
Route::post('/transactions', [TransactionController::class, 'store'])->name('transactions.store');
Route::get('/transactions/search', [TransactionController::class, 'searchProducts'])->name('transactions.search');
Route::get('/transactions/get-product/{kode_barang}', [TransactionController::class, 'getProduct'])->name('transactions.get-product');
Route::get('/transactions/generate-code', [TransactionController::class, 'generateTransactionCode'])->name('transactions.generate-code');
Route::get('/transactions/history', [TransactionController::class, 'history'])->name('transactions.history');
Route::get('/transactions/{id}', [TransactionController::class, 'show'])->name('transactions.show');
Route::get('/transactions/success/{id}', [TransactionController::class, 'success'])->name('transactions.success');


// Rute untuk form import dan import produk
    Route::get('products/import', [ProductController::class, 'importForm'])->name('products.import');
    Route::post('products/import', [ProductController::class, 'import']);

    Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
});

// useless routes
// Just to demo sidebar dropdown links active states.
/*
    Route::get('/transaction', function () {
        return view('transaction.transaction');
    })->middleware(['auth'])->name('transaction.transaction');
*/
Route::resource('transactions', TransactionController::class);



Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
Route::resource('categories', CategoryController::class);


    Route::get('/buttons/text-icon', function () {
        return view('buttons-showcase.text-icon');
    })->middleware(['auth'])->name('buttons.text-icon');









require __DIR__ . '/auth.php';
