<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\TicketActivity;
use App\Models\TicketComment;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TicketCommentController extends Controller
{
    use AuthorizesRequests;

    public function store(Request $request, Ticket $ticket): RedirectResponse
    {
        $this->authorize('view', $ticket);

        $user = Auth::user();

        $validated = $request->validate([
            'body' => ['required', 'string', 'min:2'],
            'is_internal' => ['nullable', 'boolean'],
        ]);

        $isInternal = false;
        if ($user->isStaff() && ! empty($validated['is_internal'])) {
            $isInternal = true;
        }

        TicketComment::create([
            'ticket_id' => $ticket->id,
            'user_id' => $user->id,
            'body' => $validated['body'],
            'is_internal' => $isInternal,
        ]);

        $commentType = $isInternal ? 'Internal staff note' : 'Reply';
        TicketActivity::log(
            $ticket,
            'comment_added',
            "{$commentType} added by {$user->name}",
            ['is_internal' => $isInternal],
            $user->id
        );

        return back()->with('success', 'Comment submitted successfully.');
    }
}
