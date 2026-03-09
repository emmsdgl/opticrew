<?php

namespace App\Http\Controllers\Applicant;

use App\Http\Controllers\Controller;
use App\Models\JobApplication;
use App\Models\JobPosting;
use Illuminate\Support\Facades\Auth;

class ApplicantDashboardController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();

        $jobPostings = JobPosting::active()->orderBy('created_at', 'desc')->get();

        $myApplications = JobApplication::where('email', $user->email)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('applicant.dashboard', compact('user', 'jobPostings', 'myApplications'));
    }
}
