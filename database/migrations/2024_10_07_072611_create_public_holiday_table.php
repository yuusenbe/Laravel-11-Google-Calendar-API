<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePublicHolidayTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('public_holiday', function (Blueprint $table) {
            $table->id(); // Auto-incrementing ID
            $table->string('holiday_name'); // Holiday name (from summary)
            $table->date('holiday_date'); // Start date of the holiday (from start.date)
            $table->date('holiday_end_date')->nullable(); // End date of the holiday (can be from end.date)
            $table->string('regions'); // Regions associated with the holiday (extracted from description)
            $table->timestamps(); // Created at and updated at timestamps
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('public_holiday'); // Drop the table if it exists
    }
}
