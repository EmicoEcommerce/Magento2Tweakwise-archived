<?php
/**
 * Tweakwise & Emico (https://www.tweakwise.com/ & https://www.emico.nl/) - All Rights Reserved
 *
 * @copyright Copyright (c) 2017-2017 Tweakwise.com B.V. (https://www.tweakwise.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Emico\Tweakwise\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

class QueryFilterType implements OptionSourceInterface
{
    /**
     * Possible filter types
     */
    public const TYPE_SPECIFIC = 'specific';
    public const TYPE_REGEX = 'regex';
    public const TYPE_NONE = 'none';

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
            ['value' => self::TYPE_NONE, 'label' => __('Dont\'t filter')],
            ['value' => self::TYPE_SPECIFIC, 'label' => __('Specific arguments')],
            ['value' => self::TYPE_REGEX, 'label' => __('Regex matching')],
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
