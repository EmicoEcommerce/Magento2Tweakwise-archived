<?php
/**
 * @author : Edwin Jacobs, email: ejacobs@emico.nl.
 * @copyright : Copyright Emico B.V. 2019.
 */

namespace Emico\Tweakwise\Model\Client\Response\Catalog;


use Emico\Tweakwise\Model\Client\Response;
use Emico\Tweakwise\Model\Client\Type\TemplateType;

class SortTemplateResponse extends Response
{
    /**
     * @param TemplateType[]|array[] $templates
     * @return $this
     */
    public function setSorttemplate(array $templates)
    {
        $templates = $this->normalizeArray($templates, 'sorttemplate');

        $values = [];
        foreach ($templates as $value) {
            if (!$value instanceof TemplateType) {
                $value = new TemplateType($value, 'sorttemplateid');
            }

            $values[] = $value;
        }

        $this->data['templates'] = $values;
        return $this;
    }
}