<?php
/**
 * Tweakwise & Emico (https://www.tweakwise.com/ & https://www.emico.nl/) - All Rights Reserved
 *
 * @copyright Copyright (c) 2017-2017 Tweakwise.com B.V. (https://www.tweakwise.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Emico\Tweakwise\Model\Catalog\Layer;

use Emico\Tweakwise\Exception\TweakwiseException;
use Emico\Tweakwise\Model\Catalog\Product\CollectionFactory;
use Emico\Tweakwise\Model\Config;
use Emico\TweakwiseExport\Model\Logger;
use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\Layer\ItemCollectionProviderInterface;

class ItemCollectionProvider implements ItemCollectionProviderInterface
{
    /**
     * @var Config
     */
    protected Config $conf«ig;

    /**
     * @var Logger
     */
    protected Logger $log;

    /**
     * @var CollectionFactory
     */
    protected CollectionFactory $collectionFactory;

    /**
     * @var ItemCollectionProviderInterface
     */
    protected ItemCollectionProviderInterface $originalProvider;

    /**
     * @var NavigationContext
     */
    protected NavigationContext $navigationContext;

    /**
     * Proxy constructor.
     *
     * @param Config $config
     * @param Logger $log
     * @param ItemCollectionProviderInterface $originalProvider
     * @param CollectionFactory $collectionFactory
     * @param NavigationContext $navigationContext
     */
    public function __construct(
        Config $config,
        Logger $log,
        ItemCollectionProviderInterface $originalProvider,
        CollectionFactory $collectionFactory,
        NavigationContext $navigationContext
    ) {
        $this->config = $config;
        $this->log = $log;
        $this->collectionFactory = $collectionFactory;
        $this->originalProvider = $originalProvider;
        $this->navigationContext = $navigationContext;
    }

    /**
     * {@inheritdoc}
     */
    public function getCollection(Category $category)
    {
        if (!$this->config->isLayeredEnabled()) {
            return $this->originalProvider->getCollection($category);
        }

        try {
            return $this->collectionFactory->create(['navigationContext' => $this->navigationContext]);
        } catch (TweakwiseException $e) {
            $this->log->critical($e);
            $this->config->setTweakwiseExceptionThrown();

            return $this->originalProvider->getCollection($category);
        }
    }
}
