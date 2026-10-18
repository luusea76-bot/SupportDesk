<?php

namespace App\Models;

use Database\Factories\TicketActivityFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['ticket_id', 'user_id', 'activity_type', 'description', 'properties', 'created_at'])]
class TicketActivity extends Model
{
    /** @use HasFactory<TicketActivityFactory> */
    use HasFactory;

    public $timestamps = false;

    protected function casts(): array
    {
        return [
            'properties' => 'array',
            'created_at' => 'datetime',
        ];
    }

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Record a new audit event for a ticket.
     */
    public static function log(
        Ticket $ticket,
        string $activityType,
        string $description,
        ?array $properties = null,
        ?int $userId = null
    ): self {
        return self::create([
            'ticket_id' => $ticket->id,
            'user_id' => $userId ?? auth()->id(),
            'activity_type' => $activityType,
            'description' => $description,
            'properties' => $properties,
            'created_at' => now(),
        ]);
    }
}
