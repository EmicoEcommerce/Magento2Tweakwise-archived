<?php
/**
 * @author Emico <info@emico.nl>
 * @copyright (c) Emico B.V. 2017
 */

namespace Emico\Tweakwise\Model\Config;

use Emico\Tweakwise\Model\Config;
use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\Product;

class TemplateFinder
{
    /**
     * @var Config
     */
    private $config;

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
     * @return int
     */
    public function forProduct(Product $product, $type)
    {
        $attribute = $this->getAttribute($type);
        $templateId = $product->getData($attribute);
        if ($templateId) {
            return (int) $templateId;
        }

        $category = $product->getCategory();
        if ($category) {
            return $this->forCategory($category, $type);
        }

        return $this->config->getRecommendationsTemplate($type);
    }

    /**
     * @param Category $category
     * @param string $type
     * @return int
     */
    public function forCategory(Category $category, $type)
    {
        $attribute = $this->getAttribute($type);
        $templateId = $category->getData($attribute);
        if ($templateId) {
            return (int) $templateId;
        }

        $parent = $category->getParentCategory();
        if ($parent) {
            return $this->forCategory($parent, $type);
        }

        return $this->config->getRecommendationsTemplate($type);
    }

    /**
     * @param string $type
     * @return string
     */
    private function getAttribute($type)
    {
        return sprintf('tweakwise_%s_template', $type);
    }
}