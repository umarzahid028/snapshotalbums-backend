<?php

// Run this with: php artisan tinker < fix_ticket_emails.php

use App\Models\SupportTicket;
use App\Models\User;

echo "\n=== Fixing Ticket Emails ===\n\n";

// Find all tickets that have a user_id but wrong email
$tickets = SupportTicket::whereNotNull('user_id')->get();

$fixed = 0;
$correct = 0;

foreach ($tickets as $ticket) {
    $user = User::find($ticket->user_id);
    
    if ($user && $ticket->email !== $user->email) {
        echo "Fixing ticket " . $ticket->ticket_number . ":\n";
        echo "  Old email: " . $ticket->email . "\n";
        echo "  New email: " . $user->email . "\n";
        
        $ticket->update(['email' => $user->email]);
        $fixed++;
    } else if ($user) {
        $correct++;
    }
}

echo "\n✅ Fixed: $fixed tickets\n";
echo "✅ Already correct: $correct tickets\n";
echo "\nDone!\n";

