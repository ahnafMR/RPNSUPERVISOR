<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CheckIn;

class CheckInController extends Controller
{
    public function index()
    {
        $checkins = CheckIn::with(['user', 'lokasi'])->latest('waktu_checkin')->get();

        return view('admin.checkin.index', compact('checkins'));
    }

    public function show(CheckIn $checkin)
    {
        $checkin->load(['user', 'lokasi', 'laporans']);

        return view('admin.checkin.show', compact('checkin'));
    }
}
