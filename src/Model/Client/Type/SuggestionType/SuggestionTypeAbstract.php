<?php

/**
 * @author : Edwin Jacobs, email: ejacobs@emico.nl.
 * @copyright : Copyright Emico B.V. 2020.
 */

namespace Emico\Tweakwise\Model\Client\Type\SuggestionType;

use Emico\Tweakwise\Model\Client\Type\Type;

/**
 * Class SuggestionTypeAbstract
 * @package Emico\Tweakwise\Model\Client\Type\SuggestionType
 */
abstract class SuggestionTypeAbstract extends Type
{
    /**
     * @return string
     */
    abstract public function getUrl();

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->data['match'] ?? 'Suggestions';
    }
}
