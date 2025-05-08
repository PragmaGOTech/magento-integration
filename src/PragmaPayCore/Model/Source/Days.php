<?php

declare(strict_types=1);

namespace Pragma\PragmaPayCore\Model\Source;

use Magento\Framework\Data\OptionSourceInterface;

class Days implements OptionSourceInterface
{

    public function toOptionArray(): array
    {
        return [
            [
                'label' => __('14'),
                'value' => 14
            ],
            [
                'label' => __('30'),
                'value' => 30
            ],
            [
                'label' => __('60'),
                'value' => 60
            ],
        ];
    }
}
