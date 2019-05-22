<?php
/**
 * Tweakwise & Emico (https://www.tweakwise.com/ & https://www.emico.nl/) - All Rights Reserved
 *
 * @copyright Copyright (c) 2017-2017 Tweakwise.com B.V. (https://www.tweakwise.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Emico\Tweakwise\Model\Config\Source;

use Emico\Tweakwise\Model\Catalog\Layer\Url\Strategy\PathSlugStrategy;
use Emico\Tweakwise\Model\Catalog\Layer\Url\Strategy\QueryParameterStrategy;
use Magento\Framework\Option\ArrayInterface;

class UrlStrategy implements ArrayInterface
{
    /**
     * Possible filter types
     */
    const STRATEGY_QUERY_PARAM = QueryParameterStrategy::class;
    const STRATEGY_PATH_SLUGS = PathSlugStrategy::class;

    /**
     * @var array[]
     */
    protected $options;

    /**
     * @return array
     */
    protected function buildOptions()
    {
        return [
            ['value' => self::STRATEGY_QUERY_PARAM, 'label' => __('Query params (?color=Red&size=M)')],
            ['value' => self::STRATEGY_PATH_SLUGS, 'label' => __('SEO Path slugs (/color/red/size/m)')],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        if (!$this->options) {
            $this->options = $this->buildOptions();
        }
        return $this->options;
    }
}