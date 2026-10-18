<?php

namespace App\Enums;

enum TicketPriority: string
{
    case Low = 'low';
    case Medium = 'medium';
    case High = 'high';
    case Urgent = 'urgent';

    public function label(): string
    {
        return match ($this) {
            self::Low => 'Low',
            self::Medium => 'Medium',
            self::High => 'High',
            self::Urgent => 'Urgent',
        };
    }

    public function badgeClasses(): string
    {
        return match ($this) {
            self::Low => 'bg-slate-100 text-slate-700 border-slate-200',
            self::Medium => 'bg-blue-50 text-blue-700 border-blue-200',
            self::High => 'bg-amber-50 text-amber-700 border-amber-200',
            self::Urgent => 'bg-rose-50 text-rose-700 border-rose-200',
        };
    }

    public function barColor(): string
    {
        return match ($this) {
            self::Low => 'bg-slate-300',
            self::Medium => 'bg-blue-500',
            self::High => 'bg-amber-500',
            self::Urgent => 'bg-rose-500',
        };
    }
}
