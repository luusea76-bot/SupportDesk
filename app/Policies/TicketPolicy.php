<?php

namespace App\Policies;

use App\Enums\TicketStatus;
use App\Models\Ticket;
use App\Models\User;

class TicketPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->is_active;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Ticket $ticket): bool
    {
        if (! $user->is_active) {
            return false;
        }

        if ($user->isAdmin() || $user->isAgent()) {
            return true;
        }

        // Customer can only view their own tickets
        return $ticket->customer_id === $user->customer?->id
            || $ticket->created_by_user_id === $user->id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->is_active;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Ticket $ticket): bool
    {
        if (! $user->is_active) {
            return false;
        }

        if ($user->isAdmin()) {
            return true;
        }

        if ($user->isAgent()) {
            return true;
        }

        // Customer can only edit title/description if ticket is still open
        return ($ticket->customer_id === $user->customer?->id || $ticket->created_by_user_id === $user->id)
            && $ticket->status === TicketStatus::Open;
    }

    /**
     * Determine whether the user can assign the ticket to an agent.
     */
    public function assign(User $user, Ticket $ticket): bool
    {
        return $user->is_active && ($user->isAdmin() || $user->isAgent());
    }

    /**
     * Determine whether the user can change status or priority.
     */
    public function changeStatus(User $user, Ticket $ticket): bool
    {
        if (! $user->is_active) {
            return false;
        }

        if ($user->isAdmin() || $user->isAgent()) {
            return true;
        }

        // Customer can close or reopen their own ticket
        return $ticket->customer_id === $user->customer?->id || $ticket->created_by_user_id === $user->id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Ticket $ticket): bool
    {
        // Only Administrator can permanently delete tickets
        return $user->is_active && $user->isAdmin();
    }

    /**
     * Determine whether the user can add internal staff-only notes.
     */
    public function addInternalNote(User $user, Ticket $ticket): bool
    {
        return $user->is_active && ($user->isAdmin() || $user->isAgent());
    }
}
