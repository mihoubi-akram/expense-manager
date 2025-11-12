<?php

namespace App\Enums;

enum ExportStatus: string
{
    case PENDING = 'pending';
    case READY = 'ready';
    case FAILED = 'failed';
}
