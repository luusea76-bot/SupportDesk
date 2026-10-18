<?php

namespace App\Enums;

enum TicketCategory: string
{
    case Technical = 'Technical';
    case Billing = 'Billing';
    case Account = 'Account';
    case Access = 'Access';
    case Integration = 'Integration';
    case General = 'General';
    case Other = 'Other';

    public function label(): string
    {
        return $this->value;
    }
}
