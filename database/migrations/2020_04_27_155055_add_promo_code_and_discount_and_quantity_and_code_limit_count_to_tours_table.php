<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPromoCodeAndDiscountAndQuantityAndCodeLimitCountToToursTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tours', function (Blueprint $table) {
            $table->string('promo_code')->nullable();
            $table->integer('discount')->nullable();
            $table->integer('quantity')->nullable();
            $table->integer('promo_code_limit')->default(4);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tours', function (Blueprint $table) {
            $table->dropColumn(['promo_code', 'discount', 'quantity', 'promo_code_limit']);
        });
    }
}
