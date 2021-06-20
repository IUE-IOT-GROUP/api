<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('actions', function (Blueprint $table) {
            $table->foreignUuid('id')->primary();
            $table->morphs('loggable');
            $table->string('action');
            $table->timestamps();

            $table->boolean('is_synchronized')->default(false);
            $table->timestamp('synchronization_time')->nullable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('actions');
    }
};
