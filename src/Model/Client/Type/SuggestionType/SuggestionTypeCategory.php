<?php

/**
 * @author : Edwin Jacobs, email: ejacobs@emico.nl.
 * @copyright : Copyright Emico B.V. 2020.
 */

namespace Emico\Tweakwise\Model\Client\Type\SuggestionType;

use Emico\TweakwiseExport\Model\Helper;
use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\CategoryRepository;
use Magento\Framework\Exception\NoSuchEntityException;

class SuggestionTypeCategory extends SuggestionTypeAbstract
{
    /**
     * @var Helper
     */
    private $exportHelper;

    /**
     * @var CategoryRepository
     */
    protected $categoryRepository;

    /**
     * SuggestionTypeCategory constructor.
     * @param CategoryRepository $categoryRepository Empty category model used to resolve urls
     * @param Helper $exportHelper
     * @param array $data
     */
    public function __construct(
        CategoryRepository $categoryRepository,
        Helper $exportHelper,
        array $data = []
    ) {
        parent::__construct($data);
        $this->categoryRepository = $categoryRepository;
        $this->exportHelper = $exportHelper;
    }

    /**
     * @return string
     */
    public function getUrl(): string
    {
        $categoryId = $this->getCategoryId();
        if (!$categoryId) {
            return '';
        }
        try {
            /** @var Category $category */
            $category = $this->categoryRepository->get($categoryId);
            return $category->getUrl();
        } catch (NoSuchEntityException $e) {
            return '';
        }
    }

    /**
     * @return integer|null
     */
    protected function getCategoryId()
    {
        $path = $this->data['navigationLink']['context']['category']['path'];
        if (!$path) {
            return null;
        }
        $path = explode('-', $path);
        $twCategoryId = end($path);

        return $this->exportHelper->getStoreId((int)$twCategoryId);
    }
}
