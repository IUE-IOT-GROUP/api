<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('device_parameter', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('device_id')->constrained('devices')->onDelete('cascade');
            $table->foreignUuid('parameter_id')->constrained('parameters');
            $table->string('expected_parameter')->nullable();
            $table->timestamps();

            $table->boolean('is_synchronized')->default(false);
            $table->timestamp('synchronization_time')->nullable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('device_parameter');
    }
};
