<?php
/**
 * Tweakwise & Emico (https://www.tweakwise.com/ & https://www.emico.nl/) - All Rights Reserved
 *
 * @copyright Copyright (c) 2017-2017 Tweakwise.com B.V. (https://www.tweakwise.com)
 * @license   Proprietary and confidential, Unauthorized copying of this file, via any medium is strictly prohibited
 */

namespace Emico\Tweakwise\Setup;

use Emico\Tweakwise\Model\Config;
use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\Product;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\UpgradeDataInterface;

class UpgradeData implements UpgradeDataInterface
{
    /**
     * @var EavSetupFactory
     */
    protected $eavSetupFactory;

    /**
     * @var WriterInterface
     */
    protected $writer;

    /**
     * UpgradeData constructor.
     *
     * @param EavSetupFactory $eavSetupFactory
     * @param WriterInterface $writer
     */
    public function __construct(
        EavSetupFactory $eavSetupFactory,
        WriterInterface $writer
    ) {
        $this->eavSetupFactory = $eavSetupFactory;
        $this->writer = $writer;
    }

    /**
     * {@inheritdoc}
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if (version_compare($context->getVersion(), '2.0.0', '<=')) {
            $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);
            $this->ensureCrosssellTemplateAttribute($eavSetup);
            $this->ensureUpsellTemplateAttribute($eavSetup);
            $this->ensureFeaturedTemplateAttribute($eavSetup);
        }

        if (version_compare($context->getVersion(), '2.0.1', '<=')) {
            $this->updateNavigatorBaseUrl();
        }

        $setup->endSetup();
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
     * Update tw server url as the old url will be retired
     */
    protected function updateNavigatorBaseUrl()
    {
        $this->writer->save('tweakwise/general/server_url', 'https://gateway.tweakwisenavigator.com/');
    }
}
