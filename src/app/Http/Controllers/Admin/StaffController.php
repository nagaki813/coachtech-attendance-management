<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class StaffController extends Controller
{
    public function index()
    {
        $staffs = User::where('role', 'user')
            ->orderBy('id', 'asc')
            ->get();

        return view('admin.staffs.index', compact('staffs'));
    }
}
