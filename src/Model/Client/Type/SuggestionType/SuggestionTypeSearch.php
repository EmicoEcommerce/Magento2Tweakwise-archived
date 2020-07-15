<?php

/**
 * @author : Edwin Jacobs, email: ejacobs@emico.nl.
 * @copyright : Copyright Emico B.V. 2020.
 */

namespace Emico\Tweakwise\Model\Client\Type\SuggestionType;

use Emico\Tweakwise\Model\Catalog\Layer\Url\Strategy\QueryParameterStrategy;
use Emico\TweakwiseExport\Model\Helper;
use Magento\Framework\UrlInterface;

/**
 * Class SuggestionTypeSearch
 * @package Emico\Tweakwise\Model\Client\Type\SuggestionType
 */
class SuggestionTypeSearch extends SuggestionTypeAbstract
{
    /**
     * @var UrlInterface
     */
    protected $url;

    /**
     * @var Helper
     */
    protected $exportHelper;

    /**
     * SuggestionTypeSearch constructor.
     * @param UrlInterface $url
     * @param Helper $exportHelper
     * @param array $data
     */
    public function __construct(
        UrlInterface $url,
        Helper $exportHelper,
        array $data = []
    ) {
        parent::__construct($data);
        $this->url = $url;
        $this->exportHelper = $exportHelper;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        $query = [
            'q' => $this->getSearchTerm()
        ];

        $categoryIds = $this->getCategoryIds();

        if ($categoryIds) {
            $query[QueryParameterStrategy::PARAM_CATEGORY] = implode($categoryIds);
        }

        return $this->url->getUrl(
            'catalogsearch/result',
            [
                '_query' => $query
            ]
        );
    }

    /**
     * @return string
     */
    protected function getSearchTerm()
    {
        return $this->data['navigationLink']['context']['searchterm'];
    }
}
