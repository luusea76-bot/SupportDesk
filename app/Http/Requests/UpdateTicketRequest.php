<?php

namespace App\Http\Requests;

use App\Enums\TicketCategory;
use App\Enums\TicketPriority;
use App\Enums\TicketStatus;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class UpdateTicketRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $ticket = $this->route('ticket');

        return $this->user() && $this->user()->can('update', $ticket);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $isStaff = $this->user()?->isStaff();

        if (! $isStaff) {
            return [
                'title' => ['required', 'string', 'max:255'],
                'description' => ['required', 'string'],
            ];
        }

        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'customer_id' => ['required', 'exists:customers,id'],
            'assigned_to_user_id' => ['nullable', 'exists:users,id'],
            'priority' => ['required', new Enum(TicketPriority::class)],
            'status' => ['required', new Enum(TicketStatus::class)],
            'category' => ['required', new Enum(TicketCategory::class)],
        ];
    }
}
