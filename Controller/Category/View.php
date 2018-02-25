<?php
/**
 * ╔╗╔╗╔╦══╦══╦══╦════╦═══╗╔══╦═══╦═══╗
 * ║║║║║╠═╗║╔═╩╗╔╩═╗╔═╣╔══╝║╔╗║╔═╗║╔══╝
 * ║║║║║╠═╝║╚═╗║║──║║─║╚══╗║║║║╚═╝║║╔═╗
 * ║║║║║╠═╗╠═╗║║║──║║─║╔══╝║║║║╔╗╔╣║╚╗║
 * ║╚╝╚╝╠═╝╠═╝╠╝╚╗─║║─║╚══╦╣╚╝║║║║║╚═╝║
 * ╚═╝╚═╩══╩══╩══╝─╚╝─╚═══╩╩══╩╝╚╝╚═══╝
 * 
 * Examples and documentation at the: http://w3site.org
 * 
 * @copyright   Copyright (c) 2015-2016 Tereta Alexander. (http://www.w3site.org)
 * @license     http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

namespace MagentoYo\Articles\Controller\Category;

use \MagentoYo\Articles\Helper\Data as HelperData;

class View extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $_resultPageFactory;
    
    /**
     * @var \MagentoYo\Articles\Model\CategoryFactory
     */
    protected $_categoryModelFactory;
    
    /**
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;
    
    /**
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \MagentoYo\Articles\Model\CategoryFactory $categoryModelFactory
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Framework\App\Action\Context $context
     * @return void
     */
    public function __construct(
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \MagentoYo\Articles\Model\CategoryFactory $categoryModelFactory,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\App\Action\Context $context
    ) {
        $this->_resultPageFactory = $resultPageFactory;
        $this->_categoryModelFactory = $categoryModelFactory;
        $this->_coreRegistry = $coreRegistry;
        
        return parent::__construct($context);
    }
    
    /**
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute() {
        $id = $this->getRequest()->getParam('id');
        
        $categoryModel = $this->_categoryModelFactory->create();
        $categoryModel->load($id);
        
        if (!$categoryModel->getId() || !$categoryModel->getStatus()) {
            $this->_forward('defaultNoRoute');
            return;
        }
        
        $this->_coreRegistry->register('current_category', $categoryModel);
        
        $resultPage = $this->_resultPageFactory->create();
        
        if ($blockTitle = $resultPage->getLayout()->getBlock('page.main.title')) {
            $blockTitle->setPageTitle($categoryModel->getTitle());
        }
        
        $resultPage->getConfig()->getTitle()->set($categoryModel->getSeoTitle());
        $resultPage->getConfig()->setDescription($categoryModel->getSeoDescription());
        $resultPage->getConfig()->setKeywords($categoryModel->getSeoKeywords());
        
        //page.main.title
        
        switch ($categoryModel->getSeoRobots()) {
            case (HelperData::SEO_ROBOTS_NOINDEX_NOFOLLOW):
                $resultPage->getConfig()->setRobots('noindex, nofollow');
                break;
            case (HelperData::SEO_ROBOTS_INDEX_FOLLOW):
                $resultPage->getConfig()->setRobots('index, follow');
                break;
            case (HelperData::SEO_ROBOTS_INDEX_NOFOLLOW):
                $resultPage->getConfig()->setRobots('index, nofollow');
                break;
            case (HelperData::SEO_ROBOTS_NOINDEX_FOLLOW):
                $resultPage->getConfig()->setRobots('noindex, follow');
                break;
        }
        
        return $resultPage;
    }
}
