<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TravelSchedule extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'price' => 'float',
        'time' => 'time'
    ];

    public function busSchedule() {
        return $this->hasMany(BusSchedule::class);
    }

    public function routeDetail() {
        return $this->belongsTo(RouteDetail::class);
    }
}
