<?php

namespace App\Enums;

enum ExpenseCategory: string
{
    case MEAL = 'meal';
    case TRAVEL = 'travel';
    case HOTEL = 'hotel';
    case OTHER = 'other';
}
