<?php
/**
 * Tweakwise & Emico (https://www.tweakwise.com/ & https://www.emico.nl/) - All Rights Reserved
 *
 * @copyright Copyright (c) 2017-2017 Tweakwise.com B.V. (https://www.tweakwise.com)
 * @license   Proprietary and confidential, Unauthorized copying of this file, via any medium is strictly prohibited
 */

namespace Emico\Tweakwise\Model\Observer;

use Emico\Tweakwise\Model\Catalog\Layer\NavigationContext;
use Emico\Tweakwise\Model\Catalog\Layer\NavigationContext\CurrentContext;
use Emico\Tweakwise\Model\Config;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Event\Observer;
use Emico\Tweakwise\Model\Catalog\Layer\Url\AbstractUrl;

class CatalogSearchRedirect extends AbstractProductNavigationRequestObserver
{
    /**
     * CatalogSearchRedirect constructor.
     * @param Config $config
     * @param CurrentContext $context
     * @param Context $actionContext
     * @param NavigationContext $navigationContext
     */
    public function __construct(Config $config, CurrentContext $context, Context $actionContext, NavigationContext $navigationContext)
    {
        parent::__construct($config, $context, $actionContext);

        if ($this->config->isSearchEnabled() && $actionContext->getRequest()->getParam(AbstractUrl::PARAM_SEARCH)) {
            $this->context->getResponse();
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function _execute(Observer $observer)
    {
        $redirects = $this->context->getResponse()->getRedirects();
        if (!$redirects) {
            return;
        }


        $redirect = current($redirects);
        $url = $redirect->getUrl();
        if (strpos($url, 'http') !== 0) {
            $url = $this->urlBuilder->getUrl($url);
        }

        $this->getHttpResponse()->setRedirect($url);
        /** @var Action $controller */
        $controller = $observer->getData('controller_action');
        $controller->getActionFlag()->set('', Action::FLAG_NO_DISPATCH, 1);
    }
}
