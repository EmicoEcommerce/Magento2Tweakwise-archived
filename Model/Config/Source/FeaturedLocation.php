<?php
/**
 * Tweakwise & Emico (https://www.tweakwise.com/ & https://www.emico.nl/) - All Rights Reserved
 *
 * @copyright Copyright (c) 2017-2017 Tweakwise.com B.V. (https://www.tweakwise.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Emico\Tweakwise\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

class FeaturedLocation implements OptionSourceInterface
{
    /**
     * Possible product locations
     */
    public const LOCATION_BEFORE = 'before';
    public const LOCATION_AFTER = 'after';

    /**
     * @var array[]
     */
    protected array $options;

    /**
     * @return array
     */
    protected function buildOptions(): array
    {
        return [
            ['value' => self::LOCATION_BEFORE, 'label' => __('Before category products')],
            ['value' => self::LOCATION_AFTER, 'label' => __('After category products')],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function toOptionArray(): array
    {
        if (!$this->options) {
            $this->options = $this->buildOptions();
        }
        return $this->options;
    }
}
