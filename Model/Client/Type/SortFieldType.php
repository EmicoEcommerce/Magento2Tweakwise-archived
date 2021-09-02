<?php
/**
 * Tweakwise & Emico (https://www.tweakwise.com/ & https://www.emico.nl/) - All Rights Reserved
 *
 * @copyright Copyright (c) 2017-2017 Tweakwise.com B.V. (https://www.tweakwise.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Emico\Tweakwise\Model\Client\Type;

class SortFieldType extends Type
{
    /**
     * @return string
     */
    public function getTitle(): string
    {
        return (string) $this->getDataValue('title');
    }

    /**
     * @return string
     */
    public function getDisplayTitle(): string
    {
        return (string) $this->getDataValue('displaytitle');
    }

    /**
     * @return string
     */
    public function getOrder(): string
    {
        return (string) $this->getDataValue('order');
    }

    /**
     * @return bool
     */
    public function getIsSelected(): bool
    {
        return $this->getBoolValue('isselected');
    }

    /**
     * @return string
     */
    public function getUrl(): string
    {
        return (string) $this->getDataValue('url');
    }

    /**
     * @return string
     */
    public function getUrlValue(): string
    {
        return $this->getTitle();
    }
}
