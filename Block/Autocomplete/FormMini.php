<?php
/**
 * Tweakwise & Emico (https://www.tweakwise.com/ & https://www.emico.nl/) - All Rights Reserved
 *
 * @copyright Copyright (c) 2017-2017 Tweakwise.com B.V. (https://www.tweakwise.com)
 * @license   Proprietary and confidential, Unauthorized copying of this file, via any medium is strictly prohibited
 */

namespace Emico\Tweakwise\Block\Autocomplete;

use Emico\Tweakwise\Model\Config;
use Magento\Catalog\Api\Data\CategoryInterface;
use Magento\Framework\Locale\Format as LocaleFormat;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;

class FormMini extends Template
{
    /**
     * @var Config
     */
    protected $config;

    /**
     * @var LocaleFormat
     */
    protected $localeFormat;

    /**
     * @var Registry
     */
    protected $registry;

    /**
     * FormMini constructor.
     * @param Config $config
     * @param LocaleFormat $localeFormat
     * @param Registry $registry
     * @param Context $context
     * @param array $data
     */
    public function __construct(Config $config, LocaleFormat $localeFormat, Registry $registry, Context $context, array $data = [])
    {
        parent::__construct($context, $data);

        $this->config = $config;
        $this->localeFormat = $localeFormat;
        $this->registry = $registry;
    }

    /**
     * @return string
     */
    public function getJsonPriceFormat()
    {
        return json_encode($this->localeFormat->getPriceFormat());
    }

    /**
     * @return string
     */
    public function getAjaxUrl()
    {
        $routeParams = ['_secure' => $this->getRequest()->isSecure()];

        $category = $this->registry->registry('current_category');
        if ($category instanceof CategoryInterface) {
            $routeParams['cid'] = $category->getId();
        }

        return $this->getUrl('search/ajax/suggest', $routeParams);
    }
}