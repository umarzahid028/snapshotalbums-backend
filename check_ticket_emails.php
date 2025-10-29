<?php

// Run this with: php artisan tinker < check_ticket_emails.php

use App\Models\SupportTicket;
use App\Models\User;

echo "\n=== Checking Ticket Emails ===\n\n";

// Find the specific ticket from the screenshot
$ticket = SupportTicket::where('ticket_number', 'TKT-69020A1F4CB96')->first();

if ($ticket) {
    echo "Ticket Found:\n";
    echo "  Ticket Number: " . $ticket->ticket_number . "\n";
    echo "  Email: " . $ticket->email . "\n";
    echo "  Name: " . $ticket->name . "\n";
    echo "  User ID: " . $ticket->user_id . "\n";
    
    if ($ticket->user_id) {
        $user = User::find($ticket->user_id);
        if ($user) {
            echo "\nLinked User:\n";
            echo "  User Email: " . $user->email . "\n";
            echo "  User Name: " . $user->name . "\n";
            
            if ($ticket->email !== $user->email) {
                echo "\n⚠️  ERROR: Ticket email does NOT match user email!\n";
                echo "  Ticket email: " . $ticket->email . "\n";
                echo "  Should be: " . $user->email . "\n";
            }
        }
    }
} else {
    echo "Ticket TKT-69020A1F4CB96 not found\n";
}

// Check all tickets with support email
echo "\n\n=== Tickets with Support Email ===\n";
$badTickets = SupportTicket::where('email', 'support@snapshotalbums.net')
    ->orWhere('email', 'snapshotalbums2023@gmail.com')
    ->get();

echo "Found " . $badTickets->count() . " tickets with support email addresses:\n\n";

foreach ($badTickets as $t) {
    echo "  " . $t->ticket_number . " - User ID: " . $t->user_id . "\n";
}

