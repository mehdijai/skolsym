<?php
namespace App\Enum;

enum StatesEnum: string {
    case ACTIVE = 'active';
    case PENDING = 'pending';
    case REMOVED = 'removed';
    case FINISHED = 'finished';
    case CLOSED = 'closed';
    case ONE_TIME = 'one-time';
    case MONTHLY = 'monthly';
    case PAID = 'paid';
}