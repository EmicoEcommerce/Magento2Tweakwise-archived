<?php
/**
 * Tweakwise & Emico (https://www.tweakwise.com/ & https://www.emico.nl/) - All Rights Reserved
 *
 * @copyright Copyright (c) 2017-2017 Tweakwise.com B.V. (https://www.tweakwise.com)
 * @license   Proprietary and confidential, Unauthorized copying of this file, via any medium is strictly prohibited
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
        parent::__construct($data);
        $this->request = $request;
        $this->helper = $helper;
    }
}