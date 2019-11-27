<?php
/**
 * @author : Edwin Jacobs, email: ejacobs@emico.nl.
 * @copyright : Copyright Emico B.V. 2019.
 */

namespace Emico\Tweakwise\Model\Client\Request\Catalog;

use Emico\Tweakwise\Model\Client\Request;
use Emico\Tweakwise\Model\Client\Response\Catalog\LanguageResponse;

/**
 * Class LanguageRequest
 * @package Emico\Tweakwise\Model\Client\Request\Catalog
 */
class LanguageRequest extends Request
{
    /**
     * @var string
     */
    protected $path = 'catalog/languages';

    /**
     * @return string
     */
    public function getResponseType()
    {
        return LanguageResponse::class;
    }
}