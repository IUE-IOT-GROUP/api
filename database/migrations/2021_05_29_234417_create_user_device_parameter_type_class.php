<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('device_parameter', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_device_id')->constrained('user_devices')->onDelete('cascade');
            $table->foreignUuid('parameter_type_id')->constrained('parameter_types');
            $table->string('expected_parameter')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('device_parameter');
    }
};
