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
            self::InProgress => 'In Progress',
            self::Resolved => 'Resolved',
            self::Closed => 'Closed',
        };
    }

    public function badgeClasses(): string
    {
        return match ($this) {
            self::Open => 'bg-indigo-50 text-indigo-700 border-indigo-200',
            self::InProgress => 'bg-amber-50 text-amber-700 border-amber-200',
            self::Resolved => 'bg-emerald-50 text-emerald-700 border-emerald-200',
            self::Closed => 'bg-slate-100 text-slate-600 border-slate-200',
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
