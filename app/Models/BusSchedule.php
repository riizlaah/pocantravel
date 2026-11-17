<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusSchedule extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function bus() {
        return $this->belongsTo(Bus::class);
    }

    public function travelSchedule() {
        return $this->belongsTo(TravelSchedule::class);
    }

    public function tickets() {
        return $this->hasMany(Ticket::class);
    }
}
