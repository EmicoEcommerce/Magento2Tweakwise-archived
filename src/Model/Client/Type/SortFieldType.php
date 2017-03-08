<?php
/**
 * Tweakwise & Emico (https://www.tweakwise.com/ & https://www.emico.nl/) - All Rights Reserved
 *
 * @copyright Copyright (c) 2017-2017 Tweakwise.com B.V. (https://www.tweakwise.com)
 * @license   Proprietary and confidential, Unauthorized copying of this file, via any medium is strictly prohibited
 */

namespace Emico\Tweakwise\Model\Client\Type;

class SortFieldType extends Type
{
    /**
     * @return string
     */
    public function getTitle()
    {
        return (string) $this->getDataValue('title');
    }

    /**
     * @return string
     */
    public function getDisplayTitle()
    {
        return (string) $this->getDataValue('displaytitle');
    }

    /**
     * @return string
     */
    public function getOrder()
    {
        return (string) $this->getDataValue('order');
    }

    /**
     * @return bool
     */
    public function getIsSelected()
    {
        return $this->getBoolValue('isdirectsearch');
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->getBoolValue('url');
    }
}