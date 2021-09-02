<?php
/**
 * Tweakwise & Emico (https://www.tweakwise.com/ & https://www.emico.nl/) - All Rights Reserved
 *
 * @copyright Copyright (c) 2017-2017 Tweakwise.com B.V. (https://www.tweakwise.com)
 * @license   Proprietary and confidential, Unauthorized copying of this file, via any medium is strictly prohibited
 */

namespace Emico\Tweakwise\Model\Observer;

use Emico\Tweakwise\Model\Catalog\Layer\NavigationContext;
use Emico\Tweakwise\Model\Config;
use Exception;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\HTTP\PhpEnvironment\Response;
use Magento\Search\Model\QueryFactory;

class CatalogSearchRedirect implements ObserverInterface
{
    /**
     * @var Config
     */
    protected Config $config;

    /**
     * @var NavigationContext
     */
    protected NavigationContext $context;

    /**
     * @var Context
     */
    protected Context $actionContext;

    /**
     * CatalogSearchRedirect constructor.
     * @param Config $config
     * @param NavigationContext $context
     * @param Context $actionContext
     */
    public function __construct(Config $config, NavigationContext $context, Context $actionContext)
    {
        $this->config = $config;
        $this->context = $context;
        $this->actionContext = $actionContext;
    }

    /**
     * {@inheritdoc}
     * @throws Exception
     */
    public function execute(Observer $observer)
    {
        if (!$this->config->isSearchEnabled()) {
            return;
        }

        if (!$this->actionContext->getRequest()->getParam(QueryFactory::QUERY_VAR_NAME)) {
            return;
        }

        $redirects = $this->context->getResponse()->getRedirects();
        if (!$redirects) {
            return;
        }

        $redirect = current($redirects);
        $url = $redirect->getUrl();

        $isDirectUrl = str_starts_with($url, 'http');
        $isProtocolRelative = str_starts_with($url, '//');
        $isDomainRelative = !$isProtocolRelative && str_starts_with($url, '/');

        if (!$isDirectUrl && !$isProtocolRelative && !$isDomainRelative) {
            // In this case we just assume it should be a relative url
            $url = '/' . $url;
        }

        $response = $this->getHttpResponse();
        if (!$response) {
            return;
        }

        $response->setRedirect($url);
        /** @var Action $controller */
        $controller = $observer->getData('controller_action');
        $controller->getActionFlag()->set('', Action::FLAG_NO_DISPATCH, 1);
    }

    /**
     * @return Response|null
     */
    protected function getHttpResponse(): ?Response
    {
        $response = $this->actionContext->getResponse();
        if (!$response instanceof Response) {
            return null;
        }

        return $response;
    }
}
