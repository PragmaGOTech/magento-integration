<?php
declare(strict_types=1);

namespace Pragma\PragmaPayCore\Client;

use InvalidArgumentException;

class RequestManager
{
    public const DEFAULT_REGISTRATION_COUNTRY = 'PL';
    public const DEFAULT_PHONE_PREFIX = '48';

    public function formatAmount(float $amount): int
    {
        return (int)number_format(($amount * 100), 0, '.', '');
    }

    public function formatPhone(string $phone): ?array
    {
        if (empty($phone)) {
            return null;
        }

        if (!preg_match('/^\+?\d+$/', $phone)) {
            throw new InvalidArgumentException('Phone number contains invalid characters.');
        }

        $phone = ltrim(trim($phone), '+');
        if (strlen($phone) < 7 || strlen($phone) > 15) {
            throw new InvalidArgumentException('Phone number length is invalid.');
        }

        $prefix = '+' . self::DEFAULT_PHONE_PREFIX;
        if (str_starts_with($phone, self::DEFAULT_PHONE_PREFIX)) {
            $phone = substr($phone, 2);
        }

        return [
            'prefix' => $prefix,
            'number' => $phone,
        ];
    }

    public function formatRegistrationNumber(string $countryCode, string $vatId): ?array
    {
        if (!$vatId) {
            return null;
        }
        $countryCode = strtoupper(trim($countryCode));
        $vatId = strtoupper(trim($vatId));

        if (!$countryCode) {
            $countryCode = self::DEFAULT_REGISTRATION_COUNTRY;
        }

        if (!str_starts_with($vatId, $countryCode)) {
            $vatId = $countryCode . $vatId;
        }


        return [
            'registrationNumber' => $vatId,
            'country' => $countryCode,
        ];
    }
}
