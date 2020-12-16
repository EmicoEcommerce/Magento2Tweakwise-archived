<?php

/**
 * @author : Edwin Jacobs, email: ejacobs@emico.nl.
 * @copyright : Copyright Emico B.V. 2020.
 */

namespace Emico\Tweakwise\Model\Client\Response;

interface AutocompleteProductResponseInterface
{
    /**
     * @return int[]
     */
    public function getProductIds();
}
