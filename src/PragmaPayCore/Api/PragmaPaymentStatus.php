<?php
declare(strict_types=1);

namespace Pragma\PragmaPayCore\Api;

interface PragmaPaymentStatus
{
    public const STATUS_NEW = 'NEW';
    public const STATUS_FINANCED = 'FINANCED';
    public const STATUS_REJECTED = 'REJECTED';
    public const STATUS_WAITING = 'WAITING';
    public const STATUS_CANCELED = 'CANCELED';

}
