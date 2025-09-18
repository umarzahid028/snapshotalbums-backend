<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\DriveAccount;
use App\Models\Album;
use Illuminate\Http\Request;
use App\Http\Resources\DriveAccountResource;
use App\Http\Resources\AlbumResource;
use Illuminate\Support\Facades\Auth;


class DriveAccountController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
{
    $driveAccounts = DriveAccount::all();

    $totalStorageGB = $driveAccounts->sum('drive_storage');
    $totalUsedGB = $driveAccounts->sum('used_storage');
    $totalAccount = $driveAccounts->count();
    $totalAccountConnect = $driveAccounts->where('status', 'connected')->count();

    $folders = Album::all();

    return DriveAccountResource::collection($driveAccounts)
        ->additional([
            'other'   => [
                'total_storage'         => $totalStorageGB,
                'total_used'            => $totalUsedGB,
                'total_account'         => $totalAccount,
                'total_account_connect' => $totalAccountConnect,
            ],
            'folders' => AlbumResource::collection($folders),
        ]);
}
}
