<?php

namespace App\Http\Controllers\Api\V1\User;

use App\Http\Controllers\Controller;
use App\Models\SupportTicket;
use App\Models\TicketReply;
use App\Mail\UserReplyMail;
use App\Mail\NewTicketMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class SupportTicketController extends Controller
{
    /**
     * Get all tickets for the authenticated user
     */
    public function index(Request $request)
    {
        try {
            $user = $request->user();

            $query = SupportTicket::where('email', $user->email)
                ->with(['replies.admin'])
                ->orderBy('created_at', 'desc');

            // Filter by status
            if ($request->has('status') && $request->status !== 'all') {
                $query->where('status', $request->status);
            }

            $tickets = $query->get();

            return response()->json([
                'success' => true,
                'data' => $tickets,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch tickets',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get a single ticket for the authenticated user
     */
    public function show(Request $request, $id)
    {
        try {
            $user = $request->user();

            $ticket = SupportTicket::where('id', $id)
                ->where('email', $user->email)
                ->with(['replies.admin'])
                ->firstOrFail();

            return response()->json([
                'success' => true,
                'data' => $ticket,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ticket not found',
                'error' => $e->getMessage(),
            ], 404);
        }
    }

    /**
     * Create a new ticket
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
            'priority' => 'nullable|in:low,medium,high,urgent',
            'category' => 'nullable|string|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $user = $request->user();

            $ticket = SupportTicket::create([
                'ticket_number' => SupportTicket::generateTicketNumber(),
                'subject' => $request->subject,
                'message' => $request->message,
                'email' => $user->email,
                'name' => $user->name,
                'user_id' => $user->id,
                'priority' => $request->priority ?? 'medium',
                'category' => $request->category,
                'status' => 'open',
            ]);

            // Send email notification to support team
            Mail::to(['support@snapshotalbums.net', 'snapshotalbums2023@gmail.com'])
                ->send(new NewTicketMail($ticket));

            return response()->json([
                'success' => true,
                'message' => 'Ticket created successfully',
                'data' => $ticket,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create ticket',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Reply to a ticket (user response)
     */
    public function reply(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'message' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $user = $request->user();

            // Verify ticket belongs to user
            $ticket = SupportTicket::where('id', $id)
                ->where('email', $user->email)
                ->firstOrFail();

            $reply = TicketReply::create([
                'ticket_id' => $ticket->id,
                'message' => $request->message,
                'is_admin_reply' => false,
                'sender_email' => $user->email,
            ]);

            // Update ticket status if it was closed
            if ($ticket->status === 'closed' || $ticket->status === 'resolved') {
                $ticket->update(['status' => 'open']);
            }

            // Send email notification to support team
            Mail::to(['support@snapshotalbums.net', 'snapshotalbums2023@gmail.com'])
                ->send(new UserReplyMail($ticket, $reply));

            return response()->json([
                'success' => true,
                'message' => 'Reply sent successfully',
                'data' => $reply,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to send reply',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get ticket statistics for user
     */
    public function statistics(Request $request)
    {
        try {
            $user = $request->user();

            $stats = [
                'total' => SupportTicket::where('email', $user->email)->count(),
                'open' => SupportTicket::where('email', $user->email)->where('status', 'open')->count(),
                'in_progress' => SupportTicket::where('email', $user->email)->where('status', 'in_progress')->count(),
                'resolved' => SupportTicket::where('email', $user->email)->where('status', 'resolved')->count(),
                'closed' => SupportTicket::where('email', $user->email)->where('status', 'closed')->count(),
            ];

            return response()->json([
                'success' => true,
                'data' => $stats,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch statistics',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
