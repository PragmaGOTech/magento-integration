<?php
declare(strict_types=1);

namespace Pragma\PragmaPayCore\Gateway\Data\Order;

use Magento\Payment\Gateway\Data\OrderAdapterInterface;
use Magento\Sales\Api\Data\OrderItemInterface;
use Magento\Sales\Model\Order;

class OrderAdapter implements OrderAdapterInterface
{
    /**
     * OrderAdapter constructor.
     *
     * @param Order $order
     * @param AddressAdapterFactory $addressAdapterFactory
     */
    public function __construct(
        private readonly Order $order,
        private readonly AddressAdapterFactory $addressAdapterFactory
    ) {
    }

    /**
     * Returns currency code
     *
     * @return string
     */
    public function getCurrencyCode(): string
    {
        return $this->order->getOrderCurrencyCode();
    }

    /**
     * Returns currency code
     *
     * @return string
     */
    public function getBaseCurrencyCode(): string
    {
        return $this->order->getBaseCurrencyCode();
    }

    /**
     * Returns order increment id
     *
     * @return string
     */
    public function getOrderIncrementId(): string
    {
        return $this->order->getIncrementId();
    }

    /**
     * Returns customer ID
     *
     * @return int|null
     */
    public function getCustomerId(): ?int
    {
        return $this->order->getCustomerId() ? (int)$this->order->getCustomerId() : null;
    }

    /**
     * Returns billing address
     *
     * @return AddressAdapter|null
     */
    public function getBillingAddress(): ?AddressAdapter
    {
        if ($this->order->getBillingAddress()) {
            return $this->addressAdapterFactory->create(
                ['address' => $this->order->getBillingAddress()]
            );
        }

        return null;
    }

    /**
     * Returns order store id
     *
     * @return int
     */
    public function getStoreId(): int
    {
        return (int)$this->order->getStoreId();
    }

    /**
     * Returns shipping address
     *
     * @return AddressAdapter|null
     */
    public function getShippingAddress(): ?AddressAdapter
    {
        if ($this->order->getShippingAddress()) {
            return $this->addressAdapterFactory->create(
                ['address' => $this->order->getShippingAddress()]
            );
        }

        return null;
    }

    /**
     * Returns order id
     *
     * @return int
     */
    public function getId(): int
    {
        return (int)$this->order->getEntityId();
    }

    /**
     * Returns order grand total amount
     *
     * @return float|null
     */
    public function getGrandTotalAmount(): ?float
    {
        return (float)$this->order->getGrandTotal();
    }

    /**
     * Returns order grand total amount
     *
     * @return float|null
     */
    public function getBaseGrandTotalAmount(): ?float
    {
        return (float)$this->order->getBaseGrandTotal();
    }

    /**
     * Returns list of line items in the cart
     *
     * @return OrderItemInterface[]
     */
    public function getItems(): array
    {
        return $this->order->getItems();
    }

    /**
     * Return quote_id
     *
     * @return int|null
     */
    public function getQuoteId(): ?int
    {
        return (int)$this->order->getQuoteId();
    }

    public function getRemoteIp(): ?string
    {
        return $this->order->getRemoteIp();
    }
}
