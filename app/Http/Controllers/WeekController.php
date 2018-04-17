<?php

namespace App\Http\Controllers;

use App\Rooms;
use App\Meetings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;

class WeekController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('admin');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

    $rooms = Rooms::where('status', 1)
             ->get();

    $meetings = DB::table('meetings')
            ->join('rooms', 'meetings.room', '=', 'rooms.id')
            ->join('users', 'meetings.user_id', '=', 'users.id')
            ->where('meetings.status', '=', 1)
            ->where('users.status', '=', 1)
            ->select('meetings.id', 'meetings.name AS meeting_name', 'meetings.start_date', 'meetings.start_time','meetings.end_date', 'meetings.end_time','meetings.description','meetings.room','meetings.status','rooms.name AS room_name','rooms.color','rooms.capacity','users.name AS user_name')
            ->orderBy('meetings.start_date', 'desc')
            ->orderBy('meetings.start_time', 'asc')
            ->get();

    $countRooms = DB::table('rooms')
          ->where('status', '=', 1)
          ->count('id');

    $currentMonth = date('m');
    $currentYear = date('Y');
    $dataMeet = DB::table("meetings")
                ->where('status', '=', 1)
                ->where('user_id', '=', 1)
                ->whereRaw('MONTH(start_date) = ?',[$currentMonth])
                ->whereRaw('YEAR(start_date) = ?',[$currentYear])
                ->count('id');

    $rooms_week = DB::table('rooms')
                  ->where('status', '=', 1)
                  ->select('id','name','color','capacity','opening_time','closing_time','status')
                  ->orderBy('id', 'asc')
                  ->get();

    $login = DB::table('login')->first();

    return view('core.pages.schedule.weekly', compact('rooms','meetings','rooms_week','countRooms','dataMeet','login'));

    }


}
