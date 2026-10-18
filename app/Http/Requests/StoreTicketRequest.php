<?php

namespace App\Http\Requests;

use App\Enums\TicketCategory;
use App\Enums\TicketPriority;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class StoreTicketRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() !== null && $this->user()->is_active;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'priority' => ['required', new Enum(TicketPriority::class)],
            'category' => ['required', new Enum(TicketCategory::class)],
        ];

        // If the user is staff, they must select a customer or provide customer details
        if ($this->user()?->isStaff()) {
            $rules['customer_id'] = ['required', 'exists:customers,id'];
            $rules['assigned_to_user_id'] = ['nullable', 'exists:users,id'];
        }

        return $rules;
    }
}
