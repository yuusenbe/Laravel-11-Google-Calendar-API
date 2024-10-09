<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HolidayController;

Route::get('/holidays/{year}', [HolidayController::class, 'index']);

//example: /holidays/2024

Route::get('/holidays/{year}/{regions?}', [HolidayController::class, 'getHolidaysByYearAndRegions']);

//example: /holidays/2024/Selangor,Perak,Putrajaya,Johor,Sarawak