<?php

namespace Emico\Tweakwise\Setup\Patch\Data;

use Emico\Tweakwise\Model\Config;
use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\Product;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;

class AddUpSellAndCrossSellAttribute implements DataPatchInterface
{
    /**
     * @var ModuleDataSetupInterface
     */
    private $moduleDataSetup;

    /**
     * @var EavSetupFactory
     */
    private $eavSetupFactory;

    /**
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param EavSetupFactory $eavSetupFactory
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        EavSetupFactory $eavSetupFactory
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->eavSetupFactory = $eavSetupFactory;
    }

    public function apply()
    {
        $eavSetup = $this->eavSetupFactory->create(['setup' => $this->moduleDataSetup]);

        $this->ensureCrosssellTemplateAttribute($eavSetup);
        $this->ensureUpsellTemplateAttribute($eavSetup);
        $this->ensureFeaturedTemplateAttribute($eavSetup);
    }

    protected function ensureCrosssellTemplateAttribute(EavSetup $eavSetup)
    {
        foreach ([Category::ENTITY, Product::ENTITY] as $entityType) {
            $eavSetup->addAttribute($entityType, Config::ATTRIBUTE_CROSSSELL_TEMPLATE, [
                'type' => 'int',
                'label' => 'Crosssell template',
                'input' => 'select',
                'required' => false,
                'sort_order' => 10,
                'global' => ScopedAttributeInterface::SCOPE_STORE,
                'group' => 'Tweakwise',
                'source' => 'Emico\Tweakwise\Model\Config\Source\RecommendationOption\Product',
            ]);

            $eavSetup->addAttribute($entityType, Config::ATTRIBUTE_CROSSSELL_GROUP_CODE, [
                'type' => 'varchar',
                'label' => 'Crosssell group code',
                'input' => 'text',
                'required' => false,
                'sort_order' => 10,
                'global' => ScopedAttributeInterface::SCOPE_STORE,
                'group' => 'Tweakwise',
            ]);
        }
    }

    protected function ensureUpsellTemplateAttribute(EavSetup $eavSetup)
    {
        foreach ([Category::ENTITY, Product::ENTITY] as $entityType) {
            $eavSetup->addAttribute($entityType, Config::ATTRIBUTE_UPSELL_TEMPLATE, [
                'type' => 'int',
                'label' => 'Upsell template',
                'input' => 'select',
                'required' => false,
                'sort_order' => 10,
                'global' => ScopedAttributeInterface::SCOPE_STORE,
                'group' => 'Tweakwise',
                'source' => 'Emico\Tweakwise\Model\Config\Source\RecommendationOption\Product',
            ]);

            $eavSetup->addAttribute($entityType, Config::ATTRIBUTE_UPSELL_GROUP_CODE, [
                'type' => 'varchar',
                'label' => 'Upsell group code',
                'input' => 'text',
                'required' => false,
                'sort_order' => 10,
                'global' => ScopedAttributeInterface::SCOPE_STORE,
                'group' => 'Tweakwise',
            ]);
        }
    }

    protected function ensureFeaturedTemplateAttribute(EavSetup $eavSetup)
    {
        $eavSetup->addAttribute(Category::ENTITY, Config::ATTRIBUTE_FEATURED_TEMPLATE, [
            'type' => 'int',
            'label' => 'Featured products template',
            'input' => 'select',
            'required' => false,
            'sort_order' => 10,
            'global' => ScopedAttributeInterface::SCOPE_STORE,
            'group' => 'Tweakwise',
            'source' => 'Emico\Tweakwise\Model\Config\Source\RecommendationOption\Featured',
        ]);
    }


    /**
     * {@inheritdoc}
     */
    public static function getDependencies()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getAliases()
    {
        return [];
    }
}
