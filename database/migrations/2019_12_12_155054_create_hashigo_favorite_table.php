<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHashigoFavoriteTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hashigo_favorite', function (Blueprint $table) {
            $table->integer('member_id')->unsigned();
            $table->integer('id')->autoIncrement();
            $table->string('favorite_store_id',255);

            $table->unique(['member_id', 'id']);
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
        Schema::dropIfExists('hashigo_favorite');
    }
}
