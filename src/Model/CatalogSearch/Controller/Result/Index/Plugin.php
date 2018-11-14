<?php
/**
 * @author : Edwin Jacobs, email: ejacobs@emico.nl.
 * @copyright : Copyright Emico B.V. 2018.
 */

namespace Emico\Tweakwise\Model\CatalogSearch\Controller\Result\Index;

use Emico\Tweakwise\Model\Config;
use Magento\CatalogSearch\Controller\Result\Index;
use Magento\Search\Model\QueryFactory;

/**
 * Class Plugin
 *
 * @package Emico\Tweakwise\Model\CatalogSearch\Controller\Result\Index
 */
class Plugin
{
    /**
     * @var Config Tweakwise Config object used to query search settings
     */
    protected $config;

    /**
     * @var QueryFactory
     */
    protected $queryFactory;

    /**
     * Plugin constructor.
     *
     * @param Config $config Tweakwise Config object used to query search settings
     * @param QueryFactory $queryFactory
     */
    public function __construct(Config $config, QueryFactory $queryFactory)
    {
        $this->config = $config;
        $this->queryFactory = $queryFactory;
    }

    /**
     * If search is tweakwise search is enabled we do
     * not redirect to a magento redirect
     *
     * @param Index $subject Original Controller interceptor
     *
     * @return mixed
     */
    public function beforeExecute(Index $subject)
    {
        if ($this->config->isSearchEnabled()) {
            /* @var $query \Magento\Search\Model\Query */
            $query = $this->queryFactory->get();
            // Set redirect to '', so that it does not get executed
            $query->setRedirect('');
        }
    }
}