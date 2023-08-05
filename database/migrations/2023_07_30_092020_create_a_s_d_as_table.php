<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('a_s_d_as', function (Blueprint $table) {
            $table->id();
            			$table->string('name');
			$table->string('description');
			$table->string('date');

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('a_s_d_as');
    }
};
