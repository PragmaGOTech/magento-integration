<?php

declare(strict_types=1);

namespace Pragma\PragmaPayCore\Model\Source;

use Magento\Framework\Data\OptionSourceInterface;

class Installments implements OptionSourceInterface
{

    public function toOptionArray(): array
    {
        return [
            [
                'label' => __('3'),
                'value' => 3
            ],
            [
                'label' => __('6'),
                'value' => 6
            ],
            [
                'label' => __('9'),
                'value' => 9
            ],
            [
                'label' => __('12'),
                'value' => 12
            ],
        ];
    }
}
