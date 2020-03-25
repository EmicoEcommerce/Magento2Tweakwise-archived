<?php

/**
 * @author : Edwin Jacobs, email: ejacobs@emico.nl.
 * @copyright : Copyright Emico B.V. 2020.
 */

namespace Emico\Tweakwise\Model;

use Emico\Tweakwise\Model\Catalog\Layer\Url;
use Magento\Catalog\Model\Layer\Resolver;
use Magento\Framework;
use Magento\Framework\App\Response\HttpInterface as HttpResponseInterface;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\View;
use Magento\Framework\View\Result\Layout;

/**
 * Class AjaxNavigationResponse
 * @package Emico\Tweakwise\Model
 */
class AjaxNavigationResult extends Layout
{
    /**
     * @var Catalog\Layer\Url
     */
    protected $urlModel;

    /**
     * @var Resolver
     */
    protected $layerResolver;

    /**
     * @var Json
     */
    protected $serializer;

    /**
     * AjaxNavigationResult constructor.
     * @param View\Element\Template\Context $context
     * @param View\LayoutFactory $layoutFactory
     * @param View\Layout\ReaderPool $layoutReaderPool
     * @param Framework\Translate\InlineInterface $translateInline
     * @param View\Layout\BuilderFactory $layoutBuilderFactory
     * @param View\Layout\GeneratorPool $generatorPool
     * @param Url $urlModel
     * @param Resolver $layerResolver
     * @param Json $serializer
     * @param bool $isIsolated
     */
    public function __construct(
        View\Element\Template\Context $context,
        View\LayoutFactory $layoutFactory,
        View\Layout\ReaderPool $layoutReaderPool,
        Framework\Translate\InlineInterface $translateInline,
        View\Layout\BuilderFactory $layoutBuilderFactory,
        View\Layout\GeneratorPool $generatorPool,
        Url $urlModel,
        Resolver $layerResolver,
        Json $serializer,
        $isIsolated = false
    ) {
        parent::__construct(
            $context,
            $layoutFactory,
            $layoutReaderPool,
            $translateInline,
            $layoutBuilderFactory,
            $generatorPool,
            $isIsolated
        );

        $this->urlModel = $urlModel;
        $this->layerResolver = $layerResolver;
        $this->serializer = $serializer;
    }

    /**
     * @param HttpResponseInterface $response
     * @return Framework\Controller\AbstractResult|Layout
     */
    public function render(HttpResponseInterface $response)
    {
        // Order matters here, getResponseHtml will construct layer and corresponding filters this is needed in the
        // getResponseUrl method
        $html = $this->getLayout()->getOutput();
        $url = $this->getResponseUrl();

        $responseData = $this->serializer->serialize(['url' => $url, 'html' => $html]);
        $this->translateInline->processResponseBody($responseData, true);

        $response->setHeader('Content-Type', 'application/json', true);
        $response->appendBody($responseData);

        return $this;
    }

    /**
     * @return string
     */
    protected function getResponseUrl()
    {
        $layer = $this->layerResolver->get();
        $activeFilters = $layer->getState()->getFilters();
        return '/' . $this->urlModel->getFilterUrl($activeFilters);
    }
}
