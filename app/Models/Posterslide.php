<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Posterslide extends Model
{
    use HasFactory;

    protected $primaryKey = 'posterslideid';

    protected $fillable = [
        'posterslideimage',
        'posterslidename',
        'posterslidename2',
        'buttonname',
        'part',
        'status',
    ];
}
