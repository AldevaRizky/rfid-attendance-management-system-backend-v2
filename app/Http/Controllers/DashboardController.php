<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class DashboardController extends Controller
{
    public function admin()
    {
        $totalEmployees = User::where('role', 'employee')->count();
        $activeEmployees = User::where('role', 'employee')->where('status', 'active')->count();

        return view('dashboard.admin', [
            'totalEmployees' => $totalEmployees,
            'activeEmployees' => $activeEmployees,
            'recentActivities' => 5, // Replace with actual data
        ]);
    }

    public function employee()
    {
        return view('dashboard.employee');
    }
}
