<?php

/**
 * @author : Edwin Jacobs, email: ejacobs@emico.nl.
 * @copyright : Copyright Emico B.V. 2020.
 */

namespace Emico\Tweakwise\Model\FilterFormInputProvider;

use Emico\Tweakwise\Model\Catalog\Layer\NavigationContext\CurrentContext;
use Emico\Tweakwise\Model\Config;

class SearchInputProvider implements FilterFormInputProviderInterface
{
    /**
     *
     */
    public const TYPE = 'search';

    /**
     * @var CurrentContext
     */
    protected $currentNavigationContext;

    /**
     * @var Config
     */
    protected $config;

    /**
     * @var ToolbarInputProvider
     */
    protected $toolbarInputProvider;

    /**
     * SearchParameterProvider constructor.
     * @param Config $config
     * @param CurrentContext $currentNavigationContext
     * @param ToolbarInputProvider $toolbarInputProvider
     */
    public function __construct(
        Config $config,
        CurrentContext $currentNavigationContext,
        ToolbarInputProvider $toolbarInputProvider
    ) {
        $this->config = $config;
        $this->currentNavigationContext = $currentNavigationContext;
        $this->toolbarInputProvider = $toolbarInputProvider;
    }

    /**
     * @inheritDoc
     */
    public function getFilterFormInput(): array
    {
        $parameters = [
            'q' => $this->getSearchTerm()
        ];
        if (!$this->config->isAjaxFilters()) {
            return $parameters;
        }

        return array_merge(
            $parameters,
            [
                '__tw_ajax_type' => self::TYPE,
                '__tw_original_url' => 'catalogsearch/result/index',
            ],
            $this->toolbarInputProvider->getFilterFormInput()
        );
    }

    /**
     * @return string|null
     */
    protected function getSearchTerm()
    {
        return $this->currentNavigationContext
            ->getRequest()
            ->getParameter('tn_q');
    }
}
