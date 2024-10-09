<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PublicHoliday extends Model
{
    use HasFactory;

    protected $table = 'public_holiday';

    protected $fillable = [
        'holiday_date',         
        'holiday_name',         
        'holiday_end_date',     
        'regions',              
        'created_at',           
        'updated_at',           
    ];

    protected $dates = [
        'holiday_date',
        'holiday_end_date',
        'created_at',
        'updated_at',
    ];
}
