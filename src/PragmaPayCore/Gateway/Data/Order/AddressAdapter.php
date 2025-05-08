<?php
declare(strict_types=1);

namespace Pragma\PragmaPayCore\Gateway\Data\Order;

use Magento\Payment\Gateway\Data\Order\AddressAdapter as MagentoAddressAdapter;
use Magento\Sales\Api\Data\OrderAddressInterface;

class AddressAdapter extends MagentoAddressAdapter
{
    public function __construct(private readonly OrderAddressInterface $address)
    {
        parent::__construct($address);
    }

    /**
     * @return array
     */
    public function getStreet(): array
    {
        $street = $this->address->getStreet();

        return empty($street) ? [] : $street;
    }

    /**
     * @return string|null
     */
    public function getVatId(): ?string
    {
        $vatId = $this->address->getVatId();

        return empty($vatId) ? null : $vatId;
    }
}
