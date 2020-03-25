<?php

/**
 * @author : Edwin Jacobs, email: ejacobs@emico.nl.
 * @copyright : Copyright Emico B.V. 2020.
 */

namespace Emico\Tweakwise\Model\FilterFormParameterProvider;

interface FilterFormParameterProviderInterface
{
    /**
     * Should return an array of hidden parameters which are added to src/view/frontend/templates/layer/view.phtml
     * This is needed for ajax filtering. The array should be formatted as 'name' => 'value'
     * name will be rendered as name attribute in an <input> tag and obviously value will be its value attribute
     *
     * @return string[]
     */
    public function getFilterFormParameters(): array;
}
