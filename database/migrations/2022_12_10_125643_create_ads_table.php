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
        Schema::create('ads', function (Blueprint $table) {
            $table->id();
            $table->string('name', 45);
            $table->string('description');
            $table->float('price');
            $table->enum('type', ['N', 'U'])->default('N');
            // $table->foreignId('category_id');
            // $table->foreign('category_id')->on('categories')->references('id')->cascadeOnDelete();

            $table->foreignId('user_id');
            $table->foreign('user_id')->on('users')->references('id')->cascadeOnDelete();

            $table->boolean('active')->default(true);
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
        Schema::dropIfExists('ads');
    }
};
