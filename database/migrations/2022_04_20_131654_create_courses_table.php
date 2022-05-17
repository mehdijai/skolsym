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
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teacher_id')->references('id')->on('teachers')->onDelete('cascade');
            $table->string('title');
            $table->integer('period')->unsigned()->default(4);
            $table->float('price')->default(500.00);
            $table->float('teacher_percentage')->default(50.00);
            $table->string('payment_type')->nullable()->default('monthly');
            $table->string('state')->default('active');
            $table->boolean('archived')->default(false);
            $table->dateTime('archived_at')->nullable();
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
        Schema::dropIfExists('courses');
    }
};
