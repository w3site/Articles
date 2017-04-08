<?php
namespace WSite\Articles\Controller\Adminhtml\Catalog;

class Index extends \Magento\Backend\App\Action
{
    protected $_resultPageFactory;
    
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('WSite_Articles::content_articles');
    }
    
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
        $this->_resultPageFactory = $resultPageFactory;
        
        parent::__construct($context);
    }
    
    protected function _initAction()
    {
        $resultPage = $this->_resultPageFactory->create();
        $resultPage->setActiveMenu('WSite_Articles::content_articles_catalog');
        return $resultPage;
    }
    
    public function execute()
    {
        $resultPage = $this->_initAction();
        $resultPage->getConfig()->getTitle()->prepend(__('Catalog of Articles'));
        return $resultPage;
    }
}
