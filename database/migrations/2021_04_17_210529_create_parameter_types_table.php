<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateParameterTypesTable extends Migration
{
    public function up()
    {
        Schema::create('parameter_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('unit');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('parameter_types');
    }
}
