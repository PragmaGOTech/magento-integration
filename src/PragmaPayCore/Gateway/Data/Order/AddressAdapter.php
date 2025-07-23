<?php
declare(strict_types=1);

namespace Pragma\PragmaPayCore\Gateway\Data\Order;

use Magento\Payment\Gateway\Data\Order\AddressAdapter as MagentoAddressAdapter;
use Magento\Sales\Api\Data\OrderAddressInterface;

class AddressAdapter extends MagentoAddressAdapter
{
    private OrderAddressInterface $address;

    public function __construct(OrderAddressInterface $address)
    {
        $this->address = $address;
        parent::__construct($address);
    }

    public function getStreet(): array
    {
        $street = $this->address->getStreet();

        return empty($street) ? [] : $street;
    }

    public function getVatId(): ?string
    {
        $vatId = $this->address->getVatId();

        return empty($vatId) ? null : $vatId;
    }
}
