<?php

/**
 * @author : Edwin Jacobs, email: ejacobs@emico.nl.
 * @copyright : Copyright Emico B.V. 2020.
 */

namespace Emico\Tweakwise\Model\FilterFormInputProvider;

class EmptyInputProvider implements FilterFormInputProviderInterface
{
    /**
     * @inheritDoc
     */
    public function getFilterFormInput(): array
    {
        return [];
    }
}
