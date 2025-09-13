<?php

namespace App\Http\Controllers\Api\V1\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Album;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        // 1. Total albums (all time)
        $totalAlbums = Album::where('user_id', $user->id)->count();

        // 2. Total albums created this month
        $totalAlbumsThisMonth = Album::where('user_id', $user->id)
            ->whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->count();

        // 3. Recent events (latest 3 albums)
        $recentEvents = Album::where('user_id', $user->id)
            ->latest()
            ->take(3)
            ->get([
                'id',
                'event_title',
                'event_type',
                'event_date',
                'location',
                'qrCode',
                'created_at'
            ]);

        return response()->json([
            'total_albums' => $totalAlbums,
            'total_albums_this_month' => $totalAlbumsThisMonth,
            'recent_events' => $recentEvents,
        ]);
    }
}
