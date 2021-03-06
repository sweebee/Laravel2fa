<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Create2faTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('2fa_settings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('model_id');
            $table->string('model_type');
	        $table->boolean('enabled')->default(false);
	        $table->text('secret');
            $table->string('remember_token')->nullable();
            $table->unique(['model_id', 'model_type']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('2fa');
    }
}
