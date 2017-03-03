<?php
/**
 * Tweakwise & Emico (https://www.tweakwise.com/ & https://www.emico.nl/) - All Rights Reserved
 *
 * @copyright Copyright (c) 2017-2017 Tweakwise.com B.V. (https://www.tweakwise.com)
 * @license   Proprietary and confidential, Unauthorized copying of this file, via any medium is strictly prohibited
 */

namespace Emico\Tweakwise\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;

class Config
{
    /**
     * @var ScopeConfigInterface
     */
    protected $config;

    /**
     * @var bool
     */
    protected $tweakwiseExceptionThrown = false;

    /**
     * Export constructor.
     *
     * @param ScopeConfigInterface $config
     */
    public function __construct(ScopeConfigInterface $config)
    {
        $this->config = $config;
    }

    /**
     * @param bool $thrown
     * @return $this
     */
    public function setTweakwiseExceptionThrown($thrown = true)
    {
        $this->tweakwiseExceptionThrown = (bool) $thrown;
        return $this;
    }

    /**
     * @return bool
     */
    public function isEnabled()
    {
        if ($this->tweakwiseExceptionThrown) {
            return false;
        }
        return (bool) $this->config->getValue('tweakwise/layered/enabled');
    }
}