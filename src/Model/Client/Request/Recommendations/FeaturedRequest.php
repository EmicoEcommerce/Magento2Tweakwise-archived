<?php
/**
 * Tweakwise & Emico (https://www.tweakwise.com/ & https://www.emico.nl/) - All Rights Reserved
 *
 * @copyright Copyright (c) 2017-2017 Tweakwise.com B.V. (https://www.tweakwise.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Emico\Tweakwise\Model\Client\Request\Recommendations;

use Emico\Tweakwise\Exception\ApiException;
use Emico\Tweakwise\Model\Client\Request;
use Emico\Tweakwise\Model\Client\Response\RecommendationsResponse;

class FeaturedRequest extends Request
{
    /**
     * {@inheritDoc}
     */
    protected $path = 'recommendations/featured';

    /**
     * @var int
     */
    protected $templateId;

    /**
     * {@inheritDoc}
     */
    public function getResponseType()
    {
        return RecommendationsResponse::class;
    }

    /**
     * @return int|string
     */
    public function getTemplate()
    {
        return $this->templateId;
    }

    /**
     * @param int|string $templateId
     * @return $this
     */
    public function setTemplate($templateId)
    {
        if (!is_string($templateId)) {
            $templateId = (int) $templateId;
        }

        $this->templateId = $templateId;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getPathSuffix()
    {
        if (!$this->templateId) {
            throw new ApiException('Featured products without template ID was requested.');
        }

        return  '/' . $this->templateId;
    }
}