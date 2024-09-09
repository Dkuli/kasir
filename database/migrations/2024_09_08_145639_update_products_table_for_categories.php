<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateProductsTableForCategories extends Migration
{
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('jenis_barang'); // Menghapus kolom 'jenis_barang'
            $table->foreignId('category_id')->constrained('categories')->onDelete('cascade'); // Menambahkan foreign key category_id
        });
    }

    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
            $table->string('jenis_barang');
        });
    }
}
