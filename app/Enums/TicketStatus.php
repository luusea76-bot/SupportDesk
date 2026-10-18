<?php

namespace App\Enums;

enum TicketStatus: string
{
    case Open = 'open';
    case InProgress = 'in_progress';
    case Resolved = 'resolved';
    case Closed = 'closed';

    public function label(): string
    {
        return match ($this) {
            self::Open => 'Open',
            self::InProgress => 'Pending',
            self::Resolved => 'Resolved',
            self::Closed => 'Closed',
        };
    }

    public function badgeClasses(): string
    {
        return match ($this) {
            self::Open => 'bg-sky-100 text-sky-700',
            self::InProgress => 'bg-amber-100 text-amber-800',
            self::Resolved => 'bg-emerald-100 text-emerald-800',
            self::Closed => 'bg-slate-100 text-slate-600',
        };
    }

    /**
     * Determine if a status transition is legally permitted.
     */
    public function canTransitionTo(self $target): bool
    {
        if ($this === $target) {
            return true;
        }

        return match ($this) {
            self::Open => in_array($target, [self::InProgress, self::Resolved, self::Closed], true),
            self::InProgress => in_array($target, [self::Open, self::Resolved, self::Closed], true),
            self::Resolved => in_array($target, [self::InProgress, self::Closed], true),
            self::Closed => in_array($target, [self::Open, self::InProgress], true),
        };
    }
}
