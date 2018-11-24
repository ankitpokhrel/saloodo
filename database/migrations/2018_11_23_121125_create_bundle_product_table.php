<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBundleProductTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bundle_product', function (Blueprint $table) {
            $table->unsignedInteger('bundle_id');
            $table->unsignedInteger('product_id');
        });

        Schema::table('bundle_product', function (Blueprint $table) {
            $table->foreign('bundle_id')->references('id')->on('bundles');
            $table->foreign('product_id')->references('id')->on('products');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bundle_product');
    }
}
