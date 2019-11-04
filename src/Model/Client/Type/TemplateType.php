<?php
/**
 * Tweakwise & Emico (https://www.tweakwise.com/ & https://www.emico.nl/) - All Rights Reserved
 *
 * @copyright Copyright (c) 2017-2017 Tweakwise.com B.V. (https://www.tweakwise.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Emico\Tweakwise\Model\Client\Type;

/**
 * @method string getName();
 */
class TemplateType extends Type
{
    /**
     * @var string
     */
    protected $idField;

    /**
     * TemplateType constructor.
     * @param array $data
     * @param string $idField
     */
    public function __construct(array $data = [], string $idField = 'templateid')
    {
        parent::__construct($data);
        $this->idField = $idField;
    }

    /**
     * @return int
     */
    public function getTemplateId()
    {
        return (int) $this->getDataValue($this->idField);
    }
}