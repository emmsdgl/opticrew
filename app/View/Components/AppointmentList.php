<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AppointmentList extends Controller
{
    public function index()
    {
        $appointments = [
            [
                'id' => 1,
                'title' => 'Deep Cleaning',
                'location' => 'Cabin 1',
                'status' => 'in_progress',
                'duration' => '02 h 30 m',
                'progress' => 30,
                'date' => '2025-07-07',
                'time' => '10:00 AM'
            ],
            [
                'id' => 2,
                'title' => 'Room Inspection',
                'location' => 'Cabin 2',
                'status' => 'complete',
                'duration' => '01 h 15 m',
                'progress' => 100,
                'date' => '2025-07-07',
                'time' => '02:00 PM'
            ],
        ];
        
        return view('client-dash', compact('appointments'));
    }
}