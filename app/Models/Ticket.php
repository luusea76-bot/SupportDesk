<?php

namespace App\Models;

use App\Enums\TicketCategory;
use App\Enums\TicketPriority;
use App\Enums\TicketStatus;
use Database\Factories\TicketFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'ticket_number',
    'title',
    'description',
    'customer_id',
    'assigned_to_user_id',
    'created_by_user_id',
    'priority',
    'status',
    'category',
    'due_at',
    'resolved_at',
    'closed_at',
])]
class Ticket extends Model
{
    /** @use HasFactory<TicketFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'priority' => TicketPriority::class,
            'status' => TicketStatus::class,
            'category' => TicketCategory::class,
            'due_at' => 'datetime',
            'resolved_at' => 'datetime',
            'closed_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Ticket $ticket) {
            if (empty($ticket->ticket_number)) {
                $ticket->ticket_number = static::generateTicketNumber();
            }
            if (empty($ticket->category)) {
                $ticket->category = TicketCategory::Technical;
            }
            if (empty($ticket->priority)) {
                $ticket->priority = TicketPriority::Medium;
            }
            if (empty($ticket->status)) {
                $ticket->status = TicketStatus::Open;
            }
            if (empty($ticket->due_at)) {
                // Default SLA: 48 hours for Medium, 24 for High, 4 for Urgent, 72 for Low
                $hours = match ($ticket->priority) {
                    TicketPriority::Urgent => 4,
                    TicketPriority::High => 24,
                    TicketPriority::Low => 72,
                    default => 48,
                };
                $ticket->due_at = now()->addHours($hours);
            }
        });
    }

    /**
     * Generate unique ticket number format: SD-YYYY-000001
     */
    public static function generateTicketNumber(): string
    {
        $year = now()->format('Y');
        $prefix = "SD-{$year}-";

        $lastTicket = static::query()
            ->where('ticket_number', 'like', "{$prefix}%")
            ->orderByDesc('id')
            ->value('ticket_number');

        if (! $lastTicket) {
            $nextSequence = 1;
        } else {
            $numberPart = substr($lastTicket, strlen($prefix));
            $nextSequence = ((int) $numberPart) + 1;
        }

        return sprintf('%s%06d', $prefix, $nextSequence);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function assignedAgent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to_user_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(TicketComment::class)->orderBy('created_at', 'asc');
    }

    public function activities(): HasMany
    {
        return $this->hasMany(TicketActivity::class)->orderBy('created_at', 'desc');
    }

    public function isOverdue(): bool
    {
        return $this->due_at !== null
            && $this->due_at->isPast()
            && ! in_array($this->status, [TicketStatus::Resolved, TicketStatus::Closed], true);
    }

    /**
     * Calculate resolution time in hours
     */
    public function resolutionTimeHours(): ?float
    {
        if (! $this->resolved_at) {
            return null;
        }

        return round($this->created_at->diffInMinutes($this->resolved_at) / 60, 1);
    }

    /**
     * Scopes
     */
    public function scopeOpen(Builder $query): Builder
    {
        return $query->where('status', TicketStatus::Open);
    }

    public function scopeInProgress(Builder $query): Builder
    {
        return $query->where('status', TicketStatus::InProgress);
    }

    public function scopeResolved(Builder $query): Builder
    {
        return $query->where('status', TicketStatus::Resolved);
    }

    public function scopeClosed(Builder $query): Builder
    {
        return $query->where('status', TicketStatus::Closed);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->whereIn('status', [TicketStatus::Open, TicketStatus::InProgress]);
    }

    public function scopeOverdue(Builder $query): Builder
    {
        return $query->whereNotNull('due_at')
            ->where('due_at', '<', now())
            ->whereIn('status', [TicketStatus::Open->value, TicketStatus::InProgress->value]);
    }

    public function scopeFilter(Builder $query, array $filters): Builder
    {
        return $query
            ->when($filters['search'] ?? null, function (Builder $q, string $search) {
                $q->where(function (Builder $sub) use ($search) {
                    $sub->where('ticket_number', 'like', "%{$search}%")
                        ->orWhere('title', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%")
                        ->orWhereHas('customer', function (Builder $c) use ($search) {
                            $c->where('name', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%")
                                ->orWhere('company_name', 'like', "%{$search}%");
                        });
                });
            })
            ->when($filters['status'] ?? null, function (Builder $q, string $status) {
                $q->where('status', $status);
            })
            ->when($filters['priority'] ?? null, function (Builder $q, string $priority) {
                $q->where('priority', $priority);
            })
            ->when($filters['category'] ?? null, function (Builder $q, string $category) {
                $q->where('category', $category);
            })
            ->when($filters['assigned_to'] ?? null, function (Builder $q, $agentId) {
                if ($agentId === 'unassigned') {
                    $q->whereNull('assigned_to_user_id');
                } else {
                    $q->where('assigned_to_user_id', $agentId);
                }
            })
            ->when($filters['customer_id'] ?? null, function (Builder $q, $customerId) {
                $q->where('customer_id', $customerId);
            });
    }
}
