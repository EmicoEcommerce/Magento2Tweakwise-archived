<?php
/**
 * Tweakwise & Emico (https://www.tweakwise.com/ & https://www.emico.nl/) - All Rights Reserved
 *
 * @copyright Copyright (c) 2017-2017 Tweakwise.com B.V. (https://www.tweakwise.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Emico\Tweakwise\Model\Client;

use Emico\Tweakwise\Model\Client\Type\Type;
use Emico\TweakwiseExport\Model\Helper;

class Response extends Type
{
    /**
     * @var Request
     */
    protected $request;

    /**
     * @var Helper
     */
    protected $helper;

    /**
     * Response constructor.
     *
     * @param Helper $helper
     * @param Request $request
     * @param array $data
     */
    public function __construct(Helper $helper, Request $request, array $data = null)
    {
        $this->request = $request;
        $this->helper = $helper;
        parent::__construct($data);
    }
}