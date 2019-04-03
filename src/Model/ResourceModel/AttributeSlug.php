<?php
/**
 * Tweakwise & Emico (https://www.tweakwise.com/ & https://www.emico.nl/) - All Rights Reserved
 *
 * @copyright Copyright (c) 2017-2019 Tweakwise.com B.V. (https://www.tweakwise.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Emico\Tweakwise\Model\ResourceModel;

use Emico\Tweakwise\Api\Data\AttributeSlugInterface;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class AttributeSlug extends AbstractDb
{

    protected function _construct()
    {
        $this->_init('tweakwise_attribute_slug', AttributeSlugInterface::ATTRIBUTE);
        $this->_isPkAutoIncrement = false;
    }

}