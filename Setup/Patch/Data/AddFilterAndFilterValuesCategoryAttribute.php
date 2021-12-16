<?php

namespace Emico\Tweakwise\Setup\Patch\Data;

use Emico\Tweakwise\Model\Config;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;

class AddFilterAndFilterValuesCategoryAttribute implements DataPatchInterface
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

        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Category::ENTITY,
            Config::ATTRIBUTE_FILTER_WHITELIST_CODE,
            [
                'type' => 'varchar',
                'label' => 'Filter whitelist',
                'input' => 'text',
                'sort_order' => 10,
                'source' => '',
                'global' => 1,
                'visible' => true,
                'required' => false,
                'user_defined' => true,
                'default' => null,
                'group' => '',
                'backend' => ''
            ]
        );


        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Category::ENTITY,
            Config::ATTRIBUTE_FILTER_VALUES_WHITELIST_CODE,
            [
                'type' => 'varchar',
                'label' => 'Filter values whitelist',
                'input' => 'text',
                'sort_order' => 10,
                'source' => '',
                'global' => 1,
                'visible' => true,
                'required' => false,
                'user_defined' => true,
                'default' => null,
                'group' => '',
                'backend' => ''
            ]
        );
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
