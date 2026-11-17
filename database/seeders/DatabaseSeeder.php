<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Bus;
use App\Models\BusSchedule;
use App\Models\Location;
use App\Models\RouteDetail;
use App\Models\TravelSchedule;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        RouteDetail::create([
            'description' => 'Urutan pemberhentian: Jakarta, Bekasi, Cirebon, Semarang, Surabaya, Banyuwangi',
            'departure_point' => 'Jakarta',
            'final_destination' => 'Banyuwangi'
        ]);
        RouteDetail::create([
            'description' => 'Urutan pemberhentian: Jakarta, Bandung, Yogyakarta, Solo, Malang',
            'departure_point' => 'Jakarta',
            'final_destination' => 'Malang'
        ]);
        RouteDetail::create([
            'description' => 'Urutan pemberhentian: Bandung, Cirebon, Semarang, Solo, Yogyakarta, Surabaya, Banyuwangi',
            'departure_point' => 'Bandung',
            'final_destination' => 'Banyuwangi'
        ]);
        RouteDetail::create([
            'description' => 'Urutan pemberhentian: Jakarta, Cirebon, Semarang, Surabaya',
            'departure_point' => 'Jakarta',
            'final_destination' => 'Surabaya'
        ]);
        RouteDetail::create([
            'description' => 'Urutan pemberhentian: Semarang, Solo, Yogyakarta, Surabaya, Malang',
            'departure_point' => 'Semarang',
            'final_destination' => 'Malang'
        ]);
        Bus::create(['name' => 'Armada Eksekutif 01']);
        Bus::create(['name' => 'Armada Eksekutif 02']);
        Bus::create(['name' => 'Patas Cepat 01']);
        Bus::create(['name' => 'Patas Cepat 02']);
        Bus::create(['name' => 'Ekonomi Sejahtera 01']);
        Bus::create(['name' => 'Ekonomi Sejahtera 02']);
        TravelSchedule::create([
            'route_detail_id' => 1,
            'date' => today(),
            'departure_time' => '05:00:00',
            'price' => 350000
        ]);
        TravelSchedule::create([
            'route_detail_id' => 2,
            'date' => today()->addDay(),
            'departure_time' => '06:00:00',
            'price' => 300000
        ]);
        TravelSchedule::create([
            'route_detail_id' => 3,
            'date' => today()->addDay(),
            'departure_time' => '06:00:00',
            'price' => 330000
        ]);
        TravelSchedule::create([
            'route_detail_id' => 4,
            'date' => today()->addDay(),
            'departure_time' => '06:00:00',
            'price' => 300000
        ]);
        TravelSchedule::create([
            'route_detail_id' => 5,
            'date' => today()->addDays(2),
            'departure_time' => '13:00:00',
            'price' => 290000
        ]);
        BusSchedule::create([
            'travel_schedule_id' => 1,
            'bus_id' => 1,
            'max_slot' => 60
        ]);
        BusSchedule::create([
            'travel_schedule_id' => 1,
            'bus_id' => 2,
            'max_slot' => 60
        ]);
        BusSchedule::create([
            'travel_schedule_id' => 2,
            'bus_id' => 3,
            'max_slot' => 60
        ]);
        BusSchedule::create([
            'travel_schedule_id' => 3,
            'bus_id' => 4,
            'max_slot' => 60
        ]);
        BusSchedule::create([
            'travel_schedule_id' => 4,
            'bus_id' => 5,
            'max_slot' => 60
        ]);
        BusSchedule::create([
            'travel_schedule_id' => 5,
            'bus_id' => 6,
            'max_slot' => 60
        ]);
        User::create([
            'name' => 'Kael Manafis',
            'email' => 'kaelman@gmail.com',
            'password' => Hash::make('P4ssword?'),
            'phone_number' => '087666289012'
        ]);
        User::create([
            'name' => 'Bowo Hafidzan',
            'email' => 'bowohafn@gmail.com',
            'password' => Hash::make('P4ssword?'),
            'phone_number' => '087666281092'
        ]);
    }
}
