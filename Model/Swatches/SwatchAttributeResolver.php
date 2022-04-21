<?php

/**
 * @author : Edwin Jacobs, email: ejacobs@emico.nl.
 * @copyright : Copyright Emico B.V. 2020.
 */
namespace Emico\Tweakwise\Model\Swatches;

use Emico\Tweakwise\Model\Catalog\Layer\Filter;
use Emico\Tweakwise\Model\Catalog\Layer\Filter\Item;
use Magento\Catalog\Api\Data\ProductAttributeInterface;
use Magento\Eav\Api\AttributeRepositoryInterface;
use Magento\Eav\Model\Entity\Attribute\Source\SourceInterface;
use Magento\Eav\Model\Entity\Attribute\Source\Table;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Swatches\Model\SwatchAttributeCodes;
use Magento\Swatches\Model\SwatchAttributeType;

class SwatchAttributeResolver
{
    /**
     * @var SwatchAttributeCodes
     */
    protected $swatchAttributeCodes;

    /**
     * @var AttributeRepositoryInterface
     */
    protected $attributeRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;

    /**
     * @var array
     */
    protected $swatchMap;

    /**
     * @var SwatchAttributeType
     */
    protected $swatchAttributeTypeHelper;

    /**
     * SwatchAttributeResolver constructor.
     * @param SwatchAttributeCodes $swatchAttributeCodes
     * @param AttributeRepositoryInterface $attributeRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param SwatchAttributeType $swatchAttributeTypeHelper
     */
    public function __construct(
        SwatchAttributeCodes $swatchAttributeCodes,
        AttributeRepositoryInterface $attributeRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        SwatchAttributeType $swatchAttributeTypeHelper
    ) {
        $this->swatchAttributeCodes = $swatchAttributeCodes;
        $this->attributeRepository = $attributeRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->swatchAttributeTypeHelper = $swatchAttributeTypeHelper;
    }

    /**
     * @param Filter $filter
     * @return array
     */
    public function getSwatchData(Filter $filter): array
    {
        // Get a map of filter item labels keyed by label
        $labels = array_flip(
            array_map(
                static function (Item $filterItem) {
                    return $filterItem->getLabel();
                },
                $filter->getItems()
            )
        );
        // Get all possible swatches
        $swatchMap = $this->getSwatchMap();

        $hits = 0;
        $targetSwatchAttribute = null;
        $resolvedOptions = [];
        // try to resolve an attribute code based on the labels given by tweakwise
        foreach ($swatchMap as $swatchConfiguration) {
            $swatchOptions = $swatchConfiguration['options'];
            // Compare the labels from tweakwise with the swatch labels, the more labels match the better
            $matchingAttributeOptions = array_intersect_key($swatchOptions, $labels);
            if (count($labels) === count($matchingAttributeOptions)) {
                // All labels match, we pick this swatch attribute
                $targetSwatchAttribute = $swatchConfiguration['attribute'];
                $resolvedOptions = $matchingAttributeOptions;
                break;
            }
            if ($matchingAttributeOptions > $hits) {
                $hits = $matchingAttributeOptions;
                $targetSwatchAttribute = $swatchConfiguration['attribute'];
                $resolvedOptions = $matchingAttributeOptions;
            }
        }

        if (!$targetSwatchAttribute || !$resolvedOptions) {
            return [];
        }

        return [
            'attribute' => $targetSwatchAttribute,
            'options' => $resolvedOptions,
        ];
    }

    /**
     * @return array
     */
    protected function getSwatchMap(): array
    {
        if ($this->swatchMap !== null) {
            return $this->swatchMap;
        }

        $swatchAttributeCodes = $this->swatchAttributeCodes->getCodes();
        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter('attribute_code', $swatchAttributeCodes, 'in')
            ->create();

        $swatchAttributes = $this->attributeRepository->getList(
            ProductAttributeInterface::ENTITY_TYPE_CODE,
            $searchCriteria
        );

        $this->swatchMap = [];

        foreach ($swatchAttributes->getItems() as $swatchAttribute) {
            if (!$this->swatchAttributeTypeHelper->isSwatchAttribute($swatchAttribute)) {
                // This should not happen however just to be sure
                continue;
            }

            if (!$swatchAttribute->usesSource() || !($source = $swatchAttribute->getSource())) {
                // We cannot resolve an attribute without source.
                continue;
            }
            /** @var Table $source */
            $options = $source->getAllOptions(true, false);

            $optionLabels = array_column($options, 'label');
            $optionValues = array_column($options, 'value');
            $this->swatchMap[] = [
                'attribute' => $swatchAttribute,
                'options' => array_filter(array_combine($optionLabels, $optionValues))
            ];
        }

        return $this->swatchMap;
    }
}
