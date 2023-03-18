<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GudangBerikat extends Model
{
    use HasFactory;
    protected $table = 'gudang_berikat';
    protected $guarded = ['id'];
}
