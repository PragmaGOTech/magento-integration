<?php

declare(strict_types=1);

namespace Pragma\PragmaPayCore\Api;

interface PragmaConnectionConfigProviderInterface
{
    public const METHOD_NAME = 'pragma_payment';
    public const API_RESPONSE_REDIRECT_URL_FIELD = 'url';
    public const API_RESPONSE_PAYMENT_ID_FIELD = 'paymentId';
    public const API_RESPONSE_ITEMS_FIELD = 'items';
    public const API_RESPONSE_ITEM_ID_FIELD = 'itemId';
    public const API_RESPONSE_ITEM_STATUS_FIELD = 'status';
    public const ADDITION_KEY_REDIRECT_URL = 'pragma_payment_redirect_url';
    public const ADDITION_KEY_PAYMENT = 'pragma_payment_uuid';
    public const ADDITION_KEY_ITEM_ID = 'pragma_payment_item_id';
    public const REDIRECT_URI_FIELD = 'redirectUri';

    public function isActive(int $storeId): bool;
    public function isSandbox(int $storeId): bool;
    public function getApiUrl(int $storeId): string;
    public function getPartnerKey(int $storeId): string;
    public function getPartnerSecret(int $storeId): string;
    public function getNotificationUrl(int $storeId): string;
    public function getReturnUrl(int $storeId): string;
    public function getCancelUrl(int $storeId): string;
    public function isLogCartRequest(int $storeId): bool;
}
