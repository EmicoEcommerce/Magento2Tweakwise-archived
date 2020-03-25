<?php

/**
 * @author : Edwin Jacobs, email: ejacobs@emico.nl.
 * @copyright : Copyright Emico B.V. 2020.
 */

namespace Emico\Tweakwise\Model\FilterFormParameterProvider;

class EmptyParameterProvider implements FilterFormParameterProviderInterface
{
    /**
     * @inheritDoc
     */
    public function getFilterFormParameters(): array
    {
        return [];
    }
}
