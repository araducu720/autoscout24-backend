<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TestDriveRequest;
use App\Models\Vehicle;
use App\Notifications\TestDriveRequestNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TestDriveController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'vehicle_id' => 'required|exists:vehicles,id',
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'preferred_date' => 'required|date|after_or_equal:today',
            'preferred_time' => 'required|date_format:H:i',
            'message' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
            ], 422);
        }

        $testDrive = TestDriveRequest::create([
            'vehicle_id' => $request->vehicle_id,
            'user_id' => auth()->id(),
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'preferred_date' => $request->preferred_date,
            'preferred_time' => $request->preferred_time,
            'message' => $request->message,
            'status' => 'pending',
        ]);

        // Load vehicle with relationships for notification
        $testDrive->load('vehicle.make', 'vehicle.model', 'vehicle.user');

        // Send email notification to vehicle owner (seller)
        $vehicleOwner = $testDrive->vehicle->user;
        if ($vehicleOwner && $vehicleOwner->shouldNotify('message_received', 'email')) {
            try {
                $vehicleOwner->notify(new TestDriveRequestNotification($testDrive));
            } catch (\Exception $e) {
                \Log::warning('Failed to send test drive notification: ' . $e->getMessage());
            }
        }

        return response()->json([
            'message' => 'Test drive request submitted successfully',
            'data' => $testDrive,
        ], 201);
    }

    public function index(Request $request)
    {
        $query = TestDriveRequest::with(['vehicle.make', 'vehicle.model']);

        // If user is authenticated, show their requests
        if (auth()->check()) {
            $query->where('user_id', auth()->id());
        }

        $testDrives = $query->orderBy('preferred_date', 'desc')->paginate(10);

        return response()->json($testDrives);
    }
}
