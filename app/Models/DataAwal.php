<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DataAwal extends Model
{
    use HasFactory;
    protected $table = 'data_awal';
    protected $guarded = ['id'];
}
