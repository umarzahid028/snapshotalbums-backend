<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Mail\TicketReplyMail;
use App\Mail\NewTicketMail;
use App\Models\SupportTicket;
use App\Models\TicketReply;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class SupportTicketController extends Controller
{
    /**
     * Get all support tickets
     */
    public function index(Request $request)
    {
        try {
            $query = SupportTicket::with(['user', 'assignedAdmin', 'replies']);

            // Filter by status
            if ($request->has('status') && $request->status !== 'all') {
                $query->where('status', $request->status);
            }

            // Filter by priority
            if ($request->has('priority')) {
                $query->where('priority', $request->priority);
            }

            // Search
            if ($request->has('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('ticket_number', 'like', "%{$search}%")
                        ->orWhere('subject', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('name', 'like', "%{$search}%");
                });
            }

            // Sort
            $sortBy = $request->get('sort_by', 'created_at');
            $sortOrder = $request->get('sort_order', 'desc');
            $query->orderBy($sortBy, $sortOrder);

            $perPage = $request->get('per_page', 15);
            $tickets = $query->paginate($perPage);

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
     * Get a single ticket with replies
     */
    public function show($id)
    {
        try {
            $ticket = SupportTicket::with(['user', 'assignedAdmin', 'replies.admin'])
                ->findOrFail($id);

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
     * Create a new ticket (from email or manual creation)
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
            'email' => 'required|email',
            'name' => 'nullable|string|max:255',
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
            \Log::info('Creating PUBLIC ticket (from contact form)', [
                'email' => $request->email,
                'name' => $request->name,
                'subject' => $request->subject,
            ]);

            $ticket = SupportTicket::create([
                'ticket_number' => SupportTicket::generateTicketNumber(),
                'subject' => $request->subject,
                'message' => $request->message,
                'email' => $request->email,  // This should be the customer's email from the form
                'name' => $request->name,
                'priority' => $request->priority ?? 'medium',
                'category' => $request->category,
                'status' => 'open',
            ]);

            \Log::info('PUBLIC ticket created successfully', [
                'ticket_id' => $ticket->id,
                'ticket_number' => $ticket->ticket_number,
                'ticket_email' => $ticket->email,
                'form_email' => $request->email,
            ]);

            // Send email notification to support team
            Mail::to(['umarzahid028@gmail.com'])
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
     * Update ticket status, priority, or assignment
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'nullable|in:open,in_progress,resolved,closed',
            'priority' => 'nullable|in:low,medium,high,urgent',
            'assigned_to' => 'nullable|exists:admins,id',
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
            $ticket = SupportTicket::findOrFail($id);

            $updateData = [];
            if ($request->has('status')) {
                $updateData['status'] = $request->status;
                if ($request->status === 'resolved' || $request->status === 'closed') {
                    $updateData['resolved_at'] = now();
                }
            }
            if ($request->has('priority')) {
                $updateData['priority'] = $request->priority;
            }
            if ($request->has('assigned_to')) {
                $updateData['assigned_to'] = $request->assigned_to;
            }
            if ($request->has('category')) {
                $updateData['category'] = $request->category;
            }

            $ticket->update($updateData);

            return response()->json([
                'success' => true,
                'message' => 'Ticket updated successfully',
                'data' => $ticket->fresh(['user', 'assignedAdmin', 'replies']),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update ticket',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Reply to a ticket
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
            $ticket = SupportTicket::findOrFail($id);

            // Debug logging - check ticket details
            \Log::info('Admin replying to ticket', [
                'ticket_id' => $ticket->id,
                'ticket_number' => $ticket->ticket_number,
                'ticket_email' => $ticket->email,
                'ticket_name' => $ticket->name,
                'user_id' => $ticket->user_id,
            ]);

            $reply = TicketReply::create([
                'ticket_id' => $ticket->id,
                'message' => $request->message,
                'is_admin_reply' => true,
                'admin_id' => auth('admin')->id(),
            ]);

            // Update ticket status to in_progress if it's currently open
            if ($ticket->status === 'open') {
                $ticket->update(['status' => 'in_progress']);
            }

            // Send email notification to the customer (user who created the ticket)
            // Email should go to $ticket->email (the user's email), NOT support email
            try {
                \Log::info('Sending admin reply email to USER (not support): ' . $ticket->email . ' for ticket: ' . $ticket->ticket_number);

                // Verify we're not sending to support email
                if ($ticket->email === 'support@snapshotalbums.net' || $ticket->email === 'snapshotalbums2023@gmail.com') {
                    \Log::error('ERROR: Ticket email is set to support email instead of user email! Ticket ID: ' . $ticket->id);
                }

                Mail::to('umarzahid028@gmail.com')->send(new TicketReplyMail($ticket, $reply));
                \Log::info('Admin reply email sent successfully to USER: ' . $ticket->email);
            } catch (\Exception $e) {
                // Log email error but don't fail the request
                \Log::error('Failed to send ticket reply email to ' . $ticket->email . ': ' . $e->getMessage());
            }

            return response()->json([
                'success' => true,
                'message' => 'Reply sent successfully',
                'data' => $reply->load('admin'),
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
     * Delete a ticket
     */
    public function destroy($id)
    {
        try {
            $ticket = SupportTicket::findOrFail($id);
            $ticket->delete();

            return response()->json([
                'success' => true,
                'message' => 'Ticket deleted successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete ticket',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get ticket statistics
     */
    public function statistics()
    {
        try {
            $stats = [
                'total' => SupportTicket::count(),
                'open' => SupportTicket::where('status', 'open')->count(),
                'in_progress' => SupportTicket::where('status', 'in_progress')->count(),
                'resolved' => SupportTicket::where('status', 'resolved')->count(),
                'closed' => SupportTicket::where('status', 'closed')->count(),
                'high_priority' => SupportTicket::where('priority', 'high')->orWhere('priority', 'urgent')->count(),
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
