<?php
/**
 * @author Emico <info@emico.nl>
 * @copyright (c) Emico B.V. 2017
 */

namespace Emico\Tweakwise\Model\Config;

use Emico\Tweakwise\Model\Config;
use Emico\Tweakwise\Model\Config\Source\RecommendationOption;
use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\Product;

class TemplateFinder
{
    /**
     * @var Config
     */
    protected $config;

    /**
     * TemplateFinder constructor.
     * @param Config $config
     */
    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    /**
     * @param Product $product
     * @param string $type
     * @return int|string
     */
    public function forProduct(Product $product, $type)
    {
        $attribute = $this->getAttribute($type);
        $templateId = (int) $product->getData($attribute);

        if ($templateId === RecommendationOption::OPTION_CODE) {
            $groupAttribute = $this->getGroupCodeAttribute($type);
            return (string) $product->getData($groupAttribute);
        }

        if ($templateId) {
            return $templateId;
        }

        $category = $product->getCategory();
        if ($category) {
            return $this->forCategory($category, $type);
        }

        $defaultTemplateId = $this->config->getRecommendationsTemplate($type);

        if ($defaultTemplateId === RecommendationOption::OPTION_CODE) {
            return $this->config->getRecommendationsGroupCode($type);
        }

        return $defaultTemplateId;
    }

    /**
     * @param Category $category
     * @param string $type
     * @return int|string
     */
    public function forCategory(Category $category, $type)
    {
        $attribute = $this->getAttribute($type);
        $templateId = (int) $category->getData($attribute);

        if ($templateId === RecommendationOption::OPTION_CODE) {
            $groupAttribute = $this->getGroupCodeAttribute($type);
            return (string) $category->getData($groupAttribute);
        }

        if ($templateId) {
            return $templateId;
        }

        if ($category->getParentId()) {
            $parent = $category->getParentCategory();
            return $this->forCategory($parent, $type);
        }

        $defaultTemplateId = $this->config->getRecommendationsTemplate($type);

        if ($defaultTemplateId === RecommendationOption::OPTION_CODE) {
            return $this->config->getRecommendationsGroupCode($type);
        }

        return $defaultTemplateId;
    }

    /**
     * @param string $type
     * @return string
     */
    protected function getAttribute($type)
    {
        return sprintf('tweakwise_%s_template', $type);
    }

    /**
     * @param string $type
     * @return string
     */
    protected function getGroupCodeAttribute($type)
    {
        return sprintf('tweakwise_%s_group_code', $type);
    }
}
