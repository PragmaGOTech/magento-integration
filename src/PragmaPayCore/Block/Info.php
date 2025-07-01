<?php

declare(strict_types=1);

namespace Pragma\PragmaPayCore\Block;

use Magento\Framework\DataObject;
use Magento\Payment\Block\ConfigurableInfo;

class Info extends ConfigurableInfo
{
    /**
     * @param $transport
     * @return DataObject|null
     */
    protected function _prepareSpecificInformation($transport = null)
    {
        $transport = parent::_prepareSpecificInformation($transport);
        $hideOnFront = $this->getData('hideOnFront') ?? false;
        if ($hideOnFront && $transport instanceof DataObject) {
            $transport->unsetData();
        }

        return $transport;
    }

    /**
     * Returns label
     *
     * @param string $field
     * @return string
     */
    protected function getLabel($field): string
    {
        $label = $this->getData('labelMapper')[$field] ?? $field;
        return __($label)->render();
    }
}
