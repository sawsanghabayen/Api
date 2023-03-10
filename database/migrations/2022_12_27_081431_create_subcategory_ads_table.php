<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('subcategory_ads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ads_id');
            $table->foreign('ads_id')->on('ads')->references('id')->cascadeOnDelete();

            $table->foreignId('subcategories_id');
            $table->foreign('subcategories_id')->on('subcategories')->references('id')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('subcategory_ads');
    }
};
