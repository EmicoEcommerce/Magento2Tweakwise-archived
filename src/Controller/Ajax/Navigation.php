<?php
/**
 * @author : Edwin Jacobs, email: ejacobs@emico.nl.
 * @copyright : Copyright Emico B.V. 2019.
 */

namespace Emico\Tweakwise\Controller\Ajax;

use Emico\Tweakwise\Model\Config;
use Magento\Catalog\Model\CategoryRepository;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Registry;

/**
 * Class Navigation
 * Handles ajax filtering requests
 * @package Emico\Tweakwise\Controller\Ajax
 */
class Navigation extends Action
{
    /**
     * @var Context
     */
    protected $context;

    /**
     * @var Config
     */
    protected $config;

    /**
     * @var ResultFactory
     */
    protected $resultFactory;
    /**
     * @var Registry
     */
    private $registry;
    /**
     * @var CategoryRepository
     */
    private $categoryRepository;

    /**
     * Navigation constructor.
     * @param Context $context Request context
     * @param Config $config Tweakwise configuration provider
     * @param ResultFactory $resultFactory
     * @param Registry $registry
     * @param CategoryRepository $categoryRepository
     */
    public function __construct(
        Context $context,
        Config $config,
        ResultFactory $resultFactory,
        Registry $registry,
        CategoryRepository $categoryRepository
    ) {
        parent::__construct($context);
        $this->config = $config;
        $this->resultFactory = $resultFactory;
        $this->registry = $registry;
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * Execute action based on request and return result
     *
     * Note: Request will be added as operation argument in future
     *
     * @return \Magento\Framework\Controller\ResultInterface|ResponseInterface
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function execute()
    {
        if (!$this->config->isAjaxFiltering()) {
            throw new NotFoundException(__('Page not found.'));
        }
        $categoryId = $this->getRequest()->getParam('category_id') ?: 2;

        if (!$this->registry->registry('current_category')) {
            $category = $this->categoryRepository->get((int)$categoryId);
            $this->registry->register('current_category', $category);
        }

        return $this->resultFactory->create(ResultFactory::TYPE_LAYOUT);
    }
}
