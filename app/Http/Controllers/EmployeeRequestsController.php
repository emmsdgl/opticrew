<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class EmployeeRequestsController extends Controller
{
    public function create()
    {
        return view('employee.requests.create');
    }
}