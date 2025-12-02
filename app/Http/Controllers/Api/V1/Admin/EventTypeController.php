<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\EventType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EventTypeController extends Controller
{
    public function index()
    {
        try {
            $eventTypes = EventType::where('is_active', true)
                ->orderBy('order')
                ->get();
            return response()->json($eventTypes, 200);
        } catch (\Exception $e) {
            Log::error('Event Types Index Error: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to fetch event types'], 500);
        }
    }

    public function all()
    {
        try {
            $eventTypes = EventType::orderBy('order')->get();
            return response()->json($eventTypes, 200);
        } catch (\Exception $e) {
            Log::error('Event Types All Error: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to fetch event types'], 500);
        }
    }

    public function show($id)
    {
        try {
            $eventType = EventType::findOrFail($id);
            return response()->json($eventType, 200);
        } catch (\Exception $e) {
            Log::error('Event Type Show Error: ' . $e->getMessage());
            return response()->json(['error' => 'Event type not found'], 404);
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'icon' => 'required|string|max:255',
            'description' => 'required|string',
            'image' => 'required|string',
            'features' => 'required|array',
            'is_active' => 'boolean',
            'order' => 'numeric',
        ]);

        try {
            $eventType = DB::transaction(function () use ($request) {
                return EventType::create($request->all());
            });

            return response()->json([
                'success' => true,
                'message' => 'Event type created successfully',
                'data' => $eventType
            ], 201);
        } catch (\Exception $e) {
            Log::error('Event Type Store Error: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to create event type'], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'icon' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|required|string',
            'image' => 'sometimes|required|string',
            'features' => 'sometimes|required|array',
            'is_active' => 'boolean',
            'order' => 'numeric',
        ]);

        try {
            $eventType = EventType::findOrFail($id);

            DB::transaction(function () use ($eventType, $request) {
                $eventType->update($request->all());
            });

            return response()->json([
                'success' => true,
                'message' => 'Event type updated successfully',
                'data' => $eventType->fresh()
            ], 200);
        } catch (\Exception $e) {
            Log::error('Event Type Update Error: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to update event type'], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $eventType = EventType::findOrFail($id);

            DB::transaction(function () use ($eventType) {
                $eventType->delete();
            });

            return response()->json(['message' => 'Event type deleted successfully'], 200);
        } catch (\Exception $e) {
            Log::error('Event Type Delete Error: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to delete event type'], 500);
        }
    }
}
