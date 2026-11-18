<?php

namespace App\Http\Controllers;

use App\Models\BusSchedule;
use App\Models\ScheduleBookmark;
use App\Models\Ticket;
use App\Models\TravelSchedule;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\Eloquent\Builder as EloBuilder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class TicketController extends Controller
{
    public function index()
    {
        return view('home');
    }

    public function show_tickets()
    {
        return view('paid-tickets');
    }

    public function show_saved_schedules()
    {
        return view('saved');
    }

    public function get_schedules(Request $req)
    {
        // search (deskripsi, nama bus, depatrure, final), filter (date), order by desc
        $userId = auth()->id();
        $search = addcslashes(trim($req->query('s', '')), "%_");
        $dateFilter = $req->query('date');
        $size = $req->query('size', 10);
        if ($size < 0) $size = 0;
        $page = $req->query('page', 1);
        $offset = ($page - 1) * $size;
        $searchWildcard = "%$search%";
        $query = BusSchedule::when(!empty($search), function (EloBuilder $q0) use ($searchWildcard) {
            $q0->where(function (EloBuilder $q) use ($searchWildcard) {
                $q->whereHas('bus', function (EloBuilder $qb) use ($searchWildcard) {
                    $qb->where('name', 'like', $searchWildcard);
                })
                    ->orWhereHas('travelSchedule.routeDetail', function (EloBuilder $qrd) use ($searchWildcard) {
                        $qrd->where('description', 'like', $searchWildcard)
                            ->orWhere('departure_point', 'like', $searchWildcard)
                            ->orWhere('final_destination', 'like', $searchWildcard);
                    });
            });
        })
            ->when(preg_match("/\d{4}-\d{2}-\d{2}/", $dateFilter), function (EloBuilder $q0) use ($dateFilter) {
                $q0->where(function (EloBuilder $q) use ($dateFilter) {
                    $q->whereHas('travelSchedule', function (EloBuilder $qb) use ($dateFilter) {
                        $qb->where('date', $dateFilter);
                    });
                });
            })->where(function (EloBuilder $q) {
                $q->whereHas('travelSchedule', function (EloBuilder $ts) {
                    $ts->where('date', '>', today());
                })->where('max_slot', '>', function (Builder $q) {
                    $q->selectRaw('count(*)')->from('tickets')->whereColumn('tickets.bus_schedule_id', 'bus_schedules.id');
                });
            })->with([
                'bus',
                'travelSchedule.routeDetail'
            ])->withExists(['bookmarkedBy as is_saved' => function (EloBuilder $q) use ($userId) {
                $q->where('user_id', $userId);
            }])
            ->withCount('tickets')
            ->orderByDesc(
                TravelSchedule::select('date')
                    ->whereColumn('id', 'bus_schedules.travel_schedule_id')
                    ->orderByDesc('date')
            );
        $count = $query->count();
        $query = $query->when($size > 0, function (EloBuilder $qb) use ($offset, $size) {
            $qb->offset($offset)->limit($size);
        })->get();
        $data = $query->map(fn($data) => [
            'id' => $data->id,
            'date' => $data->travelSchedule->date,
            'description' => $data->travelSchedule->routeDetail->description,
            'time' => $data->travelSchedule->departure_time,
            'price' => $data->travelSchedule->price,
            'bus_name' => $data->bus->name,
            'max_slot' => $data->max_slot,
            'is_saved' => $data->is_saved,
            'ticket_purchased' => $data->tickets_count,
            'departure_point' => $data->travelSchedule->routeDetail->departure_point,
            'final_destination' => $data->travelSchedule->routeDetail->final_destination,
            'created_at' => $data->created_at,
            'updated_at' => $data->updated_at,
        ]);
        return response()->json([
            'pagination' => [
                'page' => $page,
                'size' => $size,
                'max_page' => (int)ceil((float)$count / (float)$size)
            ],
            'data' => $data
        ]);
    }

    public function buy_ticket(BusSchedule $schedule)
    {
        $ticketCount = $schedule->tickets_count;
        if ($schedule->max_slot <= $ticketCount) return response()->json(['message' => 'Tidak ada slot yang tersisa'], 422);
        // timezone quirk, code below isn't using Asia/Jakarta timezone but still showing it in the timezone prop.
        $datetime = Carbon::parse($schedule->travelSchedule->date)->setTimeFromTimeString($schedule->travelSchedule->departure_time);
        $diff = $datetime->diffAsCarbonInterval(now(), false)->totalHours;
        if ($diff > -2) return response()->json(['message' => 'Sisa waktu keberangkatan kurang dari 2 jam'], 422);
        Ticket::create([
            'user_id' => auth()->id(),
            'bus_schedule_id' => $schedule->id,
            'status' => 'paid',
            'code' => Str::random(8)
        ]);
        return response()->json(['message' => 'Tiket berhasil dipesan']);
        // return response()->json(['diff' => $diff, 'now' => now("Asia/Jakarta"), 'target' => $datetime, 'tz' => date_default_timezone_get()]);
    }

    public function get_tickets(Request $req)
    {
        $search = addcslashes(trim($req->query('s', '')), "%_");
        $dateFilter = $req->query('date');
        $size = $req->query('size', 10);
        if ($size < 0) $size = 0;
        $page = $req->query('page', 1);
        $offset = ($page - 1) * $size;
        $searchWildcard = "%$search%";
        $query = Ticket::where('user_id', auth()->id())
            ->when(!empty($search), function (EloBuilder $q0) use ($searchWildcard) {
                $q0->where(function (EloBuilder $q) use ($searchWildcard) {
                    $q->whereHas('busSchedule.bus', function (EloBuilder $qb) use ($searchWildcard) {
                        $qb->where('name', 'like', $searchWildcard);
                    })
                        ->orWhereHas('busSchedule.travelSchedule.routeDetail', function (EloBuilder $qrd) use ($searchWildcard) {
                            $qrd->where('description', 'like', $searchWildcard)
                                ->orWhere('departure_point', 'like', $searchWildcard)
                                ->orWhere('final_destination', 'like', $searchWildcard);
                        });
                });
            })
            ->when(preg_match("/\d{4}-\d{2}-\d{2}/", $dateFilter), function (EloBuilder $q0) use ($dateFilter) {
                $q0->where(function (EloBuilder $q) use ($dateFilter) {
                    $q->whereHas('busSchedule.travelSchedule', function (EloBuilder $qb) use ($dateFilter) {
                        $qb->where('date', $dateFilter);
                    });
                });
            })->with([
                'busSchedule' => [
                    'bus',
                    'travelSchedule.routeDetail'
                ]
            ])->orderByDesc(
                TravelSchedule::select('date')
                    ->join('bus_schedules', 'bus_schedules.travel_schedule_id', '=', 'travel_schedules.id')
                    ->whereColumn('bus_schedules.id', 'tickets.bus_schedule_id')
                    ->orderByDesc('date')
            );

        $count = $query->count();
        $query = $query->when($size > 0, function (EloBuilder $qb) use ($offset, $size) {
            $qb->offset($offset)->limit($size);
        });
        $data = $query->get()->map(fn($data) => [
            'id' => $data->id,
            'date' => $data->busSchedule->travelSchedule->date,
            'description' => $data->busSchedule->travelSchedule->routeDetail->description,
            'time' => $data->busSchedule->travelSchedule->departure_time,
            'price' => $data->busSchedule->travelSchedule->price,
            'bus_name' => $data->busSchedule->bus->name,
            'code' => $data->code,
            'departure_point' => $data->busSchedule->travelSchedule->routeDetail->departure_point,
            'final_destination' => $data->busSchedule->travelSchedule->routeDetail->final_destination,
            'created_at' => $data->created_at,
            'updated_at' => $data->updated_at,
        ]);
        return response()->json([
            'pagination' => [
                'page' => $page,
                'size' => $size,
                'max_page' => (int)ceil((float)$count / (float)$size)
            ],
            'data' => $data
        ]);
    }
    
    public function get_saved_schedules(Request $req)
    {
        $userId = auth()->id();
        $search = addcslashes(trim($req->query('s', '')), "%_");
        $size = $req->query('size', 10);
        if ($size < 0) $size = 0;
        $page = $req->query('page', 1);
        $offset = ($page - 1) * $size;
        $searchWildcard = "%$search%";
        $query = ScheduleBookmark::where('user_id', $userId)->when(!empty($search), function (EloBuilder $q0) use ($searchWildcard) {
            $q0->where(function (EloBuilder $q1) use ($searchWildcard) {
                $q1->whereHas('busSchedule', function (EloBuilder $q) use ($searchWildcard) {
                    $q->whereHas('bus', function (EloBuilder $qb) use ($searchWildcard) {
                        $qb->where('name', 'like', $searchWildcard);
                    })
                        ->orWhereHas('travelSchedule.routeDetail', function (EloBuilder $qrd) use ($searchWildcard) {
                            $qrd->where('description', 'like', $searchWildcard)
                                ->orWhere('departure_point', 'like', $searchWildcard)
                                ->orWhere('final_destination', 'like', $searchWildcard);
                        });
                });
            });
        })
            ->where(function (EloBuilder $q0) {
                $q0->whereHas('busSchedule', function (EloBuilder $bs) {
                    $bs->whereHas('travelSchedule', function (EloBuilder $ts) {
                        $ts->where('date', '>', today());
                    })->where('max_slot', '>', function (Builder $q) {
                        $q->selectRaw('count(*)')->from('tickets')->whereColumn('tickets.bus_schedule_id', 'bus_schedules.id');
                    });
                });
            })->with([
                'busSchedule' => [
                    'bus',
                    'travelSchedule.routeDetail'
                ]
            ])->withCount('tickets')
            ->orderByDesc('created_at');
        $count = $query->count();
        $query = $query->when($size > 0, function (EloBuilder $qb) use ($offset, $size) {
            $qb->offset($offset)->limit($size);
        })->get();
        $data = $query->map(fn($data) => [
            'id' => $data->busSchedule->id,
            'date' => $data->busSchedule->travelSchedule->date,
            'description' => $data->busSchedule->travelSchedule->routeDetail->description,
            'time' => $data->busSchedule->travelSchedule->departure_time,
            'price' => $data->busSchedule->travelSchedule->price,
            'bus_name' => $data->busSchedule->bus->name,
            'max_slot' => $data->busSchedule->max_slot,
            'is_saved' => true,
            'ticket_purchased' => $data->tickets_count,
            'departure_point' => $data->busSchedule->travelSchedule->routeDetail->departure_point,
            'final_destination' => $data->busSchedule->travelSchedule->routeDetail->final_destination,
            'created_at' => $data->created_at,
            'updated_at' => $data->updated_at,
        ]);
        return response()->json([
            'pagination' => [
                'page' => $page,
                'size' => $size,
                'max_page' => (int)ceil((float)$count / (float)$size)
            ],
            'data' => $data
        ]);
    }

    public function toggle_save_schedule(BusSchedule $schedule)
    {
        $userId = auth()->id();
        if ($schedule->bookmarks()->where('user_id', $userId)->exists()) {
            try {
                $schedule->bookmarks()->where('user_id', $userId)->delete();
                return response()->json(['message' => "Tiket tersimpan berhasil dihapus", 'is_saved' => false]);
            } catch (Exception $e) {
                return response()->json(['message' => "Gagal menghapus tiket yang tersimpan"], 422);
            }
        }
        try {
            $schedule->bookmarks()->create(['user_id' => $userId]);
            return response()->json(['message' => "Tiket tersimpan", 'is_saved' => true]);
        } catch (Exception $e) {
            return response()->json(['message' => "Gagal menyimpan tiket"], 422);
        }
    }
}
