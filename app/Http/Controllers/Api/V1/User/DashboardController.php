<?php

namespace App\Http\Controllers\Api\V1\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Album;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\AlbumResource;

class DashboardController extends Controller
{

    public function index(Request $request)
    {
        try {
            $user = Auth::id();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized'
                ], 401);
            }

            // 1. Total albums (all time)
            $totalAlbums = Album::where('user_id', $user)->count();

            // 2. Total files
            $totalFiles = Album::where('user_id', $user)->sum('total_files');

            // 3. Total guests
            $totalGuest = Album::where('user_id', $user)->sum('total_guests');

            // 4. Total albums created this month
            $totalAlbumsThisMonth = Album::where('user_id', $user)
                ->whereMonth('created_at', Carbon::now()->month)
                ->whereYear('created_at', Carbon::now()->year)
                ->count();

            // 5. Recent events (latest 3 albums)
            $recentEvents = Album::where('user_id', $user)->latest()->take(3)->get();

            return response()->json([
                'success' => true,
                'total_albums' => $totalAlbums,
                'total_file' => $totalFiles,
                'total_guest' => $totalGuest,
                'total_albums_this_month' => $totalAlbumsThisMonth,
                'recent_events' => AlbumResource::collection($recentEvents),
            ], 200);
        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => 'Something went wrong. Please try again later.'. $e->getMessage()
            ], 500);
        }
    }


    public function profile()
    {
        try {
            // $user = Auth::id();

            $user = Auth::user();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized'
                ], 401);
            }

            return response()->json([
                'success' => true,
                'user' => $user,
            ], 200);
        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => 'Something went wrong. Please try again later.'. $e->getMessage()
            ], 500);
        }
    }
}
