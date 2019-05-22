<?php
/**
 * Tweakwise & Emico (https://www.tweakwise.com/ & https://www.emico.nl/) - All Rights Reserved
 *
 * @copyright Copyright (c) 2017-2019 Tweakwise.com B.V. (https://www.tweakwise.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Emico\Tweakwise\Model\Catalog\Layer\Url\Strategy;

use Emico\Tweakwise\Api\AttributeSlugRepositoryInterface;
use Emico\Tweakwise\Api\Data\AttributeSlugInterfaceFactory;
use Emico\Tweakwise\Exception\UnexpectedValueException;
use Emico\Tweakwise\Model\AttributeSlug;
use Emico\Tweakwise\Model\Catalog\Layer\Filter\Item;
use Magento\Framework\Api\SearchCriteria;
use Magento\Framework\App\CacheInterface;
use Magento\Framework\Filter\TranslitUrl;
use Magento\Framework\Serialize\SerializerInterface;

/**
 * @author Bram Gerritsen <bgerritsen@emico.nl>
 * @copyright (c) Emico B.V. 2017
 */
class FilterSlugManager
{
    private const CACHE_KEY = 'tweakwise.slug.lookup';

    /**
     * @var TranslitUrl
     */
    private $translitUrl;

    /**
     * @var AttributeSlugRepositoryInterface
     */
    private $attributeSlugRepository;

    /**
     * @var AttributeSlugInterfaceFactory
     */
    private $attributeSlugFactory;

    /**
     * @var CacheInterface
     */
    private $cache;

    /**
     * @var array
     */
    private $lookupTable;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @param TranslitUrl $translitUrl
     * @param AttributeSlugRepositoryInterface $attributeSlugRepository
     * @param AttributeSlugInterfaceFactory $attributeSlugFactory
     * @param CacheInterface $cache
     * @param SerializerInterface $serializer
     */
    public function __construct(
        TranslitUrl $translitUrl,
        AttributeSlugRepositoryInterface $attributeSlugRepository,
        AttributeSlugInterfaceFactory $attributeSlugFactory,
        CacheInterface $cache,
        SerializerInterface $serializer
    ) {
        $this->translitUrl = $translitUrl;
        $this->attributeSlugRepository = $attributeSlugRepository;
        $this->attributeSlugFactory = $attributeSlugFactory;
        $this->cache = $cache;
        $this->serializer = $serializer;
    }

    /**
     * @param Item $filterItem
     * @return string
     */
    public function getSlugForFilterItem(Item $filterItem): string
    {
        $lookupTable = $this->getLookupTable();
        $attribute = $filterItem->getAttribute()->getTitle();

        if (isset($lookupTable[$attribute])) {
            return $lookupTable[$attribute];
        }

        $slug = $this->translitUrl->filter($attribute);

        /** @var AttributeSlug $attributeSlugEntity */
        $attributeSlugEntity = $this->attributeSlugFactory->create();
        $attributeSlugEntity->setAttribute($attribute);
        $attributeSlugEntity->setSlug($slug);

        $this->attributeSlugRepository->save($attributeSlugEntity);
        $this->cache->remove(self::CACHE_KEY);

        return $slug;
    }

    /**
     * @param string $slug
     * @return string
     * @throws \Emico\Tweakwise\Exception\UnexpectedValueException
     */
    public function getAttributeBySlug(string $slug): string
    {
        $attribute = array_search($slug, $this->getLookupTable(), true);
        if ($attribute === false) {
            // Check if slug matched the pattern for a slider filter (i.e. 80-120).
            if (preg_match('/^\d+-\d+$/', $slug)) {
                return $slug;
            }

            throw new UnexpectedValueException(sprintf('No attribute found for slug "%s"', $slug));
        }
        return $attribute;
    }

    /**
     * @return array
     */
    public function getLookupTable(): array
    {
        if ($this->lookupTable === null) {
            $this->lookupTable = $this->loadLookupTable();
        }
        return $this->lookupTable;
    }

    /**
     * @return array
     */
    protected function loadLookupTable(): array
    {
        $lookupTable = $this->cache->load(self::CACHE_KEY);
        if ($lookupTable === false) {
            $attributeSlugs = $this->attributeSlugRepository->getList(new SearchCriteria());
            $lookupTable = [];
            foreach ($attributeSlugs->getItems() as $attributeSlug) {
                $lookupTable[$attributeSlug->getAttribute()] = $attributeSlug->getSlug();
            }
            $this->cache->save($this->serializer->serialize($lookupTable), self::CACHE_KEY);
        } else {
            $lookupTable = $this->serializer->unserialize($lookupTable);
        }
        return $lookupTable;
    }
}