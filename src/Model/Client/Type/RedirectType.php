<?php
/**
 * Tweakwise & Emico (https://www.tweakwise.com/ & https://www.emico.nl/) - All Rights Reserved
 *
 * @copyright Copyright (c) 2017-2017 Tweakwise.com B.V. (https://www.tweakwise.com)
 * @license   Proprietary and confidential, Unauthorized copying of this file, via any medium is strictly prohibited
 */

namespace Emico\Tweakwise\Model\Client\Type;

class RedirectType extends Type
{
    /**
     * @return string
     */
    public function getUrl()
    {
        return (string) $this->getDataValue('url');
    }
}