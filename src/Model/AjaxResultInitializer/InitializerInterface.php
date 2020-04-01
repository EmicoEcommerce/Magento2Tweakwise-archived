<?php

/**
 * @author : Edwin Jacobs, email: ejacobs@emico.nl.
 * @copyright : Copyright Emico B.V. 2020.
 */

namespace Emico\Tweakwise\Model\AjaxResultInitializer;

use Emico\Tweakwise\Model\AjaxNavigationResult;
use Magento\Framework\App\RequestInterface;

interface InitializerInterface
{
    public const LAYOUT_HANDLE_SEARCH = 'tweakwise_ajax_search';
    public const LAYOUT_HANDLE_CATEGORY = 'tweakwise_ajax_category';

    /**
     * Initialize the ajax navigation result object (i.e. add layouts, create layers, populate registry etc)
     *
     * @param AjaxNavigationResult $ajaxNavigationResult
     * @param RequestInterface $request
     * @return mixed
     */
    public function initializeAjaxResult(
        AjaxNavigationResult $ajaxNavigationResult,
        RequestInterface $request
    );
}
