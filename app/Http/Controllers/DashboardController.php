<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index(): RedirectResponse
    {
        if (Auth::user()->isAdmin()) {
            return redirect()->route('admin.monitoring.index');
        }

        return redirect()->route('user.submissions.index');
    }
}
