<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Setting;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class SettingsController extends Controller
{
    /**
     * Store or update site settings.
     */

    public function index()
    {
        try {
            // Fetch the first settings record
            $settings = Setting::first();

            if (!$settings) {
                return response()->json([
                    'success' => false,
                    'message' => 'No settings found',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $settings,
            ], 200);
        } catch (\Exception $e) {
            // Log unexpected errors
            Log::error('Settings fetch error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred. Please try again later.'
            ], 500);
        }
    }


    public function storeOrUpdate(Request $request)
    {
        try {
            // Validate input
            $data = $request->validate([
                'site_name' => 'nullable|string|max:255',
                'title' => 'nullable|string|max:255',
                'meta_title' => 'nullable|string|max:60',
                'meta_description' => 'nullable|string|max:160',
                'meta_keywords' => 'nullable|string',
                'og_title' => 'nullable|string|max:255',
                'og_description' => 'nullable|string|max:255',
                'og_image' => 'nullable|url|max:255',
                'og_type' => 'nullable|string|max:50',
                'twitter_title' => 'nullable|string|max:255',
                'twitter_description' => 'nullable|string|max:255',
                'twitter_image' => 'nullable|url|max:255',
                'twitter_card_type' => 'nullable|string|max:50',
                'canonical_url' => 'nullable|url|max:255',
                'robots' => 'nullable|string|max:50',
            ]);

            // Fetch first settings record or create a new one
            $settings = Setting::first() ?? new Setting();

            // Fill and save
            $settings->fill($data);
            $settings->save();

            return response()->json([
                'success' => true,
                'message' => 'Settings saved successfully',
                'data' => $settings
            ], 200);
        } catch (ValidationException $e) {
            // Return validation errors
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            // Log unexpected errors
            Log::error('Settings save error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred. Please try again later.'
            ], 500);
        }
    }
}
