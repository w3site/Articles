<?php
namespace WSite\Articles\Controller\Article;

use \WSite\Articles\Helper\Data as HelperData;

class View extends \Magento\Framework\App\Action\Action
{
    protected $_resultPageFactory;
    
    protected $_catalogModelFactory;
    
    protected $_coreRegistry;
    
    public function __construct(
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \WSite\Articles\Model\CatalogFactory $catalogModelFactory,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\App\Action\Context $context
    ) {
        $this->_resultPageFactory = $resultPageFactory;
        $this->_catalogModelFactory = $catalogModelFactory;
        $this->_coreRegistry = $coreRegistry;
        
        return parent::__construct($context);
    }
    
    public function execute() {
        $id = $this->getRequest()->getParam('id');
        
        $catalogModel = $this->_catalogModelFactory->create();
        $catalogModel->load($id);
        
        if (!$catalogModel->getId() || !$catalogModel->getStatus()) {
            $this->_forward('defaultNoRoute');
            return;
        }
        
        $this->_coreRegistry->register('current_article', $catalogModel);
        
        $resultPage = $this->_resultPageFactory->create();
        
        if ($blockTitle = $resultPage->getLayout()->getBlock('page.main.title')) {
            $blockTitle->setPageTitle($catalogModel->getTitle());
        }

        $resultPage->getConfig()->getTitle()->set($catalogModel->getSeoTitle());
        $resultPage->getConfig()->setDescription($catalogModel->getSeoDescription());
        $resultPage->getConfig()->setKeywords($catalogModel->getSeoKeywords());
        
        switch ($catalogModel->getSeoRobots()) {
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
