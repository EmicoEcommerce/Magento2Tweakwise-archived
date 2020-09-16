<?php
/**
 * Tweakwise & Emico (https://www.tweakwise.com/ & https://www.emico.nl/) - All Rights Reserved
 *
 * @copyright Copyright (c) 2017-2017 Tweakwise.com B.V. (https://www.tweakwise.com)
 * @license   Proprietary and confidential, Unauthorized copying of this file, via any medium is strictly prohibited
 */

namespace Emico\Tweakwise\Model\Observer;

use Magento\Framework\Event\Observer;
use Emico\Tweakwise\Model\Catalog\Layer\NavigationContext;
use Emico\Tweakwise\Model\Config;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\HTTP\PhpEnvironment\Response;

class CatalogLastPageRedirect implements ObserverInterface
{
    /**
     * @var Config
     */
    protected $config;

    /**
     * @var NavigationContext
     */
    protected $context;

    /**
     * @var Context
     */
    protected $actionContext;

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
     */
    public function execute(Observer $observer)
    {
        $response = $this->getHttpResponse();
        if (!$response || $response->isRedirect()) {
            return;
        }

        $properties = $this->context->getResponse()->getProperties();
        if (!$properties->getNumberOfItems()) {
            return;
        }

        $lastPage = $properties->getNumberOfPages();
        $page = $properties->getCurrentPage();
        if ($page <= $lastPage) {
            return;
        }

        $url = $this->actionContext->getUrl()->getUrl('*/*/*', [
            '_current' => true,
            '_use_rewrite' => true,
            '_query' => ['p' => $lastPage]
        ]);

        $response->setRedirect($url);
    }

    /**
     * @return Response|null
     */
    protected function getHttpResponse()
    {
        $response = $this->actionContext->getResponse();
        if (!$response instanceof Response) {
            return null;
        }

        return $response;
    }
}
