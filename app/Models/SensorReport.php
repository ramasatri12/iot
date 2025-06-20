<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SensorReport extends Model
{
    const STATUS_NORMAL = 'normal';
    const STATUS_WARNING = 'warning';
    const STATUS_CRITICAL = 'critical';


    protected $fillable = ['tinggi_air','debit','status'];
    
    protected $casts = [
        'tinggi_air' => 'float',
        'debit' => 'float',
        'status' => 'string',
    ];
}
