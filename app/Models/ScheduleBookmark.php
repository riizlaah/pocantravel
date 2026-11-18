<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScheduleBookmark extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function busSchedule() {
        return $this->belongsTo(BusSchedule::class);
    }

    public function tickets() {
        return $this->hasManyThrough(Ticket::class, BusSchedule::class, 'id', 'bus_schedule_id', 'bus_schedule_id', 'id');
    }
}
