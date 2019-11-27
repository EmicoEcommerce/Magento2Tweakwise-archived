<?php
/**
 * @author : Edwin Jacobs, email: ejacobs@emico.nl.
 * @copyright : Copyright Emico B.V. 2019.
 */

namespace Emico\Tweakwise\Model\Client\Response\Catalog;

use Emico\Tweakwise\Model\Client\Response;

class LanguageResponse extends Response
{
    /**
     * Format response to a list of languages
     *
     * @return array
     */
    public function getLanguages(): array
    {
        return $this->normalizeArray($this->data, 'language');
    }
}