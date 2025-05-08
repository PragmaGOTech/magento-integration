<?php
declare(strict_types=1);

namespace Pragma\PragmaPayWebApi\Api;

use Pragma\PragmaPayWebApi\Api\Data\NotificationObjectInterface;

interface ConfirmNotifyInterface
{
    /**
     * @param string $id
     * @param \Pragma\PragmaPayWebApi\Api\Data\NotificationObjectInterface $object
     * @param string $type
     * @param string $date
     * @param string $timestamp
     * @return string
     */
    public function execute(
        string $id,
        NotificationObjectInterface $object,
        string $type,
        string $date,
        string $timestamp
    ): string;
}
