<?php
/**
 * Tweakwise & Emico (https://www.tweakwise.com/ & https://www.emico.nl/) - All Rights Reserved
 *
 * @copyright Copyright (c) 2017-2017 Tweakwise.com B.V. (https://www.tweakwise.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Emico\Tweakwise\Model\Client\Response\Catalog\Recommendation;

use Emico\Tweakwise\Model\Client\Response;
use Emico\Tweakwise\Model\Client\Type\Recommendation\OptionType;

/**
 * Class OptionsResponse
 *
 * @package Emico\Tweakwise\Model\Client\Response\Catalog\Recommendation
 *
 * @method OptionType[] getRecommendations();
 */
class OptionsResponse extends Response
{
    /**
     * @param OptionType[]|array[] $options
     * @return $this
     */
    public function setRecommendations(array $options)
    {
        $templates = $this->normalizeArray($options, 'recommendation');

        $values = [];
        foreach ($templates as $value) {
            if (!$value instanceof OptionType) {
                $value = new OptionType($value);
            }

            $values[] = $value;
        }

        $this->data['recommendations'] = $values;
        return $this;
    }
}