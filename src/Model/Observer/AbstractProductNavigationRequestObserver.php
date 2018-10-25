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
use Magento\Framework\App\Action\Context;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\HTTP\PhpEnvironment\Response;

abstract class AbstractProductNavigationRequestObserver implements ObserverInterface
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
     * @var \Magento\Framework\UrlInterface
     */
    protected $urlBuilder;

    /**
     * @var \Magento\Framework\App\ResponseInterface
     */
    protected $response;

    /**
     * AbstractProductNavigationRequestObserver constructor.
     * @param Config $config
     * @param CurrentContext $context
     * @param Context $actionContext
     */
    public function __construct(Config $config, CurrentContext $context, Context $actionContext)
    {
        $this->config = $config;
        $this->context = $context;
        $this->urlBuilder = $actionContext->getUrl();
        $this->response = $actionContext->getResponse();
    }

    /**
     * @return Response|null
     */
    protected function getHttpResponse()
    {
        $response = $this->response;
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
        $response = $this->getHttpResponse();
        if (!$response) {
            return;
        }

        if (!$this->hasTweakwiseResponse()) {
            return;
        }

        $this->_execute($observer);
    }

    /**
     * @param Observer $observer
     */
    abstract protected function _execute(Observer $observer);
}
