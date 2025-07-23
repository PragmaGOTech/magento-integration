<?php
declare(strict_types=1);

namespace Pragma\PragmaPayCore\Gateway\Data\Order;

use Magento\Payment\Gateway\Data\OrderAdapterInterface;
use Magento\Sales\Api\Data\OrderItemInterface;
use Magento\Sales\Model\Order;

class OrderAdapter implements OrderAdapterInterface
{
    private Order $order;

    private AddressAdapterFactory $addressAdapterFactory;

    public function __construct(Order $order, AddressAdapterFactory $addressAdapterFactory)
    {
        $this->order = $order;
        $this->addressAdapterFactory = $addressAdapterFactory;
    }

    public function getCurrencyCode(): string
    {
        return $this->order->getOrderCurrencyCode();
    }

    public function getBaseCurrencyCode(): string
    {
        return $this->order->getBaseCurrencyCode();
    }

    public function getOrderIncrementId(): string
    {
        return $this->order->getIncrementId();
    }

    public function getCustomerId(): ?int
    {
        return $this->order->getCustomerId() ? (int)$this->order->getCustomerId() : null;
    }

    public function getBillingAddress(): ?AddressAdapter
    {
        if ($this->order->getBillingAddress()) {
            return $this->addressAdapterFactory->create(
                ['address' => $this->order->getBillingAddress()]
            );
        }

        return null;
    }

    public function getStoreId(): int
    {
        return (int)$this->order->getStoreId();
    }

    public function getShippingAddress(): ?AddressAdapter
    {
        if ($this->order->getShippingAddress()) {
            return $this->addressAdapterFactory->create(
                ['address' => $this->order->getShippingAddress()]
            );
        }

        return null;
    }

    public function getId(): int
    {
        return (int)$this->order->getEntityId();
    }

    public function getGrandTotalAmount(): ?float
    {
        return (float)$this->order->getGrandTotal();
    }

    public function getBaseGrandTotalAmount(): ?float
    {
        return (float)$this->order->getBaseGrandTotal();
    }

    public function getItems(): array
    {
        return $this->order->getItems();
    }

    public function getQuoteId(): ?int
    {
        return (int)$this->order->getQuoteId();
    }

    public function getRemoteIp(): ?string
    {
        return $this->order->getRemoteIp();
    }
}
