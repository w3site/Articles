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

namespace MagentoYo\Articles\Controller\Adminhtml\Category;

class MassRemove extends \Magento\Backend\App\Action
{
    protected $_categoryFactory;
    
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \MagentoYo\Articles\Model\CategoryFactory $categoryFactory
    ) {
        $this->_categoryFactory = $categoryFactory;
        
        parent::__construct($context);
    }
    
    public function execute()
    {
        $id = (int) $this->getRequest()->getParam('entity_id');
        $data = $this->getRequest()->getPostValue();
        if (!$data && $id){
            $data['entity_id'] = [$id];
        }

        $resultRedirect = $this->resultRedirectFactory->create();
        if ($data && isset($data['entity_id']) && is_array($data['entity_id']) && $data['entity_id']) {
            $ids = $data['entity_id'];
            $modelCollection = $this->_categoryFactory->create()->getCollection();
            $modelCollection->addFieldToFilter('entity_id', $ids);

            try {
                $modelCollection->walk('delete');
                $this->messageManager->addSuccessMessage(__('The the categories were successfully removed.'));
                return $resultRedirect->setPath('*/*');
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage(
                    $e,
                    __($e->getMessage() . ' Something went wrong while mass remove.')
                );
            }
            $this->_getSession()->setFormData($data);
        }

        return $resultRedirect->setPath('*/*');
    }
    
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('MagentoYo_Articles::content_articles');
    }
}
