<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHolidaysTable extends Migration
{
    public function up()
    {
        Schema::create('holidays', function (Blueprint $table) {
            $table->id();
            $table->string('holiday_name');  // Holiday name (summary)
            $table->date('holiday_date');    // Start date of the holiday
            $table->date('holiday_end_date')->nullable();  // End date (if applicable)
            $table->string('regions')->nullable();  // Regions (comma-separated)
            $table->integer('year');  // Year of the holiday
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('holidays');
    }
}