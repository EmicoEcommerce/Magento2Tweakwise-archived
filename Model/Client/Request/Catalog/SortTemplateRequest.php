<?php
/**
 * @author : Edwin Jacobs, email: ejacobs@emico.nl.
 * @copyright : Copyright Emico B.V. 2019.
 */

namespace Emico\Tweakwise\Model\Client\Request\Catalog;

use Emico\Tweakwise\Model\Client\Request;
use Emico\Tweakwise\Model\Client\Response\Catalog\SortTemplateResponse;

class SortTemplateRequest extends Request
{
    /**
     * {@inheritDoc}
     */
    protected $path = 'catalog/sorttemplates';

    /**
     * @return string
     */
    public function getResponseType()
    {
        return SortTemplateResponse::class;
    }
}