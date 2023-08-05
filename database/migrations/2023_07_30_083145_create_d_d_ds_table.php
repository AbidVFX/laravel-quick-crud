<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('d_d_ds', function (Blueprint $table) {
            $table->id();
            			$table->string('name');
			$table->string('description');
			$table->string('date');

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('d_d_ds');
    }
};
