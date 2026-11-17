<?php

namespace App\Http\Controllers;

use App\Models\BusSchedule;
use App\Models\ScheduleBookmark;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    public function index() {
        $schedules = BusSchedule::with([
            'bus',
            'travelSchedule.routeDetail',
        ])->withCount('tickets')->get();
        $bookmarks = ScheduleBookmark::with([
            'busSchedule' => [
                'bus',
                'travelSchedule.routeDetail'
            ]
        ])->withCount('tickets')->get();
        $tickets = Ticket::with([
            'busSchedule' => [
                'bus',
                'travelSchedule.routeDetail'
            ]
        ])->get();
        dd($schedules);
        return view('home', ['title' => 'Home', 'latest_schedules' => $schedules]);
    }
}
