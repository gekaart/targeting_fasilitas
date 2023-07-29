<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TonaseCIF extends Model
{
    use HasFactory;
    protected $table = 'tonasecif';
    protected $guarded = ['id'];
}
