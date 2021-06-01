<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('parameter_type_user_device', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('user_device_id')->constrained('user_devices')->onDelete('cascade');
            $table->foreignId('parameter_type_id')->constrained('parameter_types');
            $table->string('expected_parameter')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('parameter_type_user_device');
    }
};
