<?php

namespace App\Enums;

enum TicketCategory: string
{
    case Technical = 'Technical';
    case Billing = 'Billing';
    case Account = 'Account';
    case Integration = 'Integration';
    case General = 'General';

    public function label(): string
    {
        return $this->value;
    }
}
