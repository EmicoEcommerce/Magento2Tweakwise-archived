<?php
/**
 * Tweakwise & Emico (https://www.tweakwise.com/ & https://www.emico.nl/) - All Rights Reserved
 *
 * @copyright Copyright (c) 2017-2017 Tweakwise.com B.V. (https://www.tweakwise.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Emico\Tweakwise\Model\Client\Request\Catalog;

use Emico\Tweakwise\Model\Client\Request;
use Emico\Tweakwise\Model\Client\Response\Catalog\TemplateResponse;

class TemplateRequest extends Request
{
    /**
     * {@inheritDoc}
     */
    protected string $path = 'catalog/templates';

    /**
     * @return string
     */
    public function getResponseType(): string
    {
        return TemplateResponse::class;
    }
}
