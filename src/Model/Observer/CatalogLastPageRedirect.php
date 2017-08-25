<?php
/**
 * Tweakwise & Emico (https://www.tweakwise.com/ & https://www.emico.nl/) - All Rights Reserved
 *
 * @copyright Copyright (c) 2017-2017 Tweakwise.com B.V. (https://www.tweakwise.com)
 * @license   Proprietary and confidential, Unauthorized copying of this file, via any medium is strictly prohibited
 */

namespace Emico\Tweakwise\Model\Observer;

use Emico\Tweakwise\Model\Catalog\Layer\NavigationContext\CurrentContext;
use Emico\Tweakwise\Model\Config;
use Magento\Framework\App\Action\Action;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\HTTP\PhpEnvironment\Response;
use Magento\Framework\UrlInterface;

class CatalogLastPageRedirect implements ObserverInterface
{
    /**
     * @var Config
     */
    protected $config;

    /**
     * @var CurrentContext
     */
    protected $context;

    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * CatalogLastPageRedirect constructor.
     *
     * @param Config $config
     * @param CurrentContext $context
     * @param UrlInterface $urlBuilder
     */
    public function __construct(Config $config, CurrentContext $context, UrlInterface $urlBuilder)
    {
        $this->config = $config;
        $this->context = $context;
        $this->urlBuilder = $urlBuilder;
    }


    /**
     * @param mixed $controller
     * @return Response|null
    */
    protected function getHttpResponse($controller)
    {
        if (!$controller instanceof Action) {
            return null;
        }

        $response = $controller->getResponse();
        if (!$response instanceof Response) {
            return null;
        }

        return $response;
    }

    /**
     * @return bool
     */
    protected function hasTweakwiseResponse()
    {
        if (!$this->config->isLayeredEnabled()) {
            return false;
        }

        if (!$this->context->getContext()->hasResponse()) {
            return false;
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(Observer $observer)
    {
        $response = $this->getHttpResponse($observer->getData('controller_action'));
        if (!$response) {
            return;
        }

        if (!$this->hasTweakwiseResponse()) {
            return;
        }

        $properties = $this->context->getResponse()->getProperties();
        $lastPage = $properties->getNumberOfPages();
        $page = $properties->getCurrentPage();
        if ($page <= $lastPage) {
            return;
        }

        $url = $this->urlBuilder->getUrl('*/*/*', [
            '_current' => true,
            '_use_rewrite' => true,
            '_query' => ['p' => $lastPage]
        ]);

        $response->setRedirect($url);
    }
}