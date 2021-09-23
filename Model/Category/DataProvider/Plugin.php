<?php

namespace Emico\Tweakwise\Model\Category\DataProvider;

use JetBrains\PhpStorm\ArrayShape;
use Magento\Catalog\Model\Category\DataProvider as CategoryDataProvider;
use Magento\Eav\Model\Config;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\AuthorizationInterface;
use Magento\Framework\Exception\LocalizedException;

class Plugin
{
    /**
     * @var Config
     */
    protected Config $eavConfig;

    /**
     * @var AuthorizationInterface
     */
    private AuthorizationInterface $auth;

    /**
     * DataProvider constructor.
     *
     * @param Config                      $eavConfig
     * @param AuthorizationInterface|null $auth
     */
    public function __construct(
        Config $eavConfig,
        ?AuthorizationInterface $auth = null
    ) {
        $this->eavConfig = $eavConfig;
        $this->auth      = $auth ?? ObjectManager::getInstance()->get(AuthorizationInterface::class);
    }

    /**
     * @param CategoryDataProvider $subject
     * @param array                $meta
     *
     * @return array
     *
     * @throws LocalizedException
     */
    public function afterPrepareMeta(CategoryDataProvider $subject, $meta): array
    {
        return array_replace_recursive(
            $meta,
            $this->prepareFieldsMeta(
                $this->getFieldsMap(),
                $subject->getAttributesMeta($this->eavConfig->getEntityType('catalog_category'))
            )
        );
    }

    /**
     * @param array $fieldsMap
     * @param array $fieldsMeta
     *
     * @return array
     */
    private function prepareFieldsMeta(array $fieldsMap, array $fieldsMeta): array
    {
        $canEditDesign = $this->auth->isAllowed('Magento_Catalog::edit_category_design');

        $result = [];
        foreach ($fieldsMap as $fieldSet => $fields) {
            foreach ($fields as $field) {
                if (isset($fieldsMeta[$field])) {
                    $config = $fieldsMeta[$field];
                    if (($fieldSet === 'design' || $fieldSet === 'schedule_design_update') && !$canEditDesign) {
                        $config['required']        = 1;
                        $config['disabled']        = 1;
                        $config['serviceDisabled'] = true;
                    }

                    $result[$fieldSet]['children'][$field]['arguments']['data']['config'] = $config;
                }
            }
        }

        return $result;
    }

    /**
     * @return array
     */
    protected function getFieldsMap(): array
    {
        return [
            'tweakwise' => [
                'tweakwise_featured_template',
                'tweakwise_crosssell_template',
                'tweakwise_crosssell_group_code',
                'tweakwise_upsell_template',
                'tweakwise_upsell_group_code',
            ],
        ];
    }
}
