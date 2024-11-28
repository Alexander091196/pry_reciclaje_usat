<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Action extends Model
{
    use HasFactory;
    protected $guarded=[];

    protected $fillable = [
        'date',
        'description',
        'image',
        'horarie_id',
    ];
}
