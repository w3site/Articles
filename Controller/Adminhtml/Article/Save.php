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

namespace MagentoYo\Articles\Controller\Adminhtml\Article;

class Save extends \Magento\Backend\App\Action
{
    protected $_categoryFactory;
    protected $_boundFactory;
    
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \MagentoYo\Articles\Model\CatalogFactory $categoryFactory,
        \MagentoYo\Articles\Model\BoundFactory $boundFactory
    ) {
        $this->_categoryFactory = $categoryFactory;
        $this->_boundFactory = $boundFactory;
        
        parent::__construct($context);
    }
    
    public function execute()
    {
        $id = (int) $this->getRequest()->getParam('entity_id');
        $data = $this->getRequest()->getPostValue();

        $resultRedirect = $this->resultRedirectFactory->create();
        if ($data) {
            $model = $this->_categoryFactory->create();
            $boundModel = $this->_boundFactory->create();

            if ($id) {
                $model->load($id);
            }
            else{
                unset($data['entity_id']);
            }

            if (isset($data['category_ids']) && is_string($data['category_ids'])){
                $data['category_ids'] = [$data['category_ids']];
            }
            
            try {
                $model->setData($data)
                    ->autoFill()
                    ->save()
                    ->saveCategories()
                    ->generateUrlRewrite();
                
                $id = $model->getId();

                $this->messageManager->addSuccessMessage(__('The article was successfully saved.'));
                $this->_getSession()->setFormData(false);
                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', ['entity_id' => $id, '_current' => true]);
                }
                return $resultRedirect->setPath('*/*/');
            } catch (\Magento\Framework\Exception\ValidatorException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\RuntimeException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage(
                    $e,
                    __($e->getMessage() . ' Something went wrong while saving the item.')
                );
            }
            $this->_getSession()->setFormData($data);
        } else {
            $this->messageManager->addErrorMessage(__('Incorrect data'));
        }

        return $resultRedirect->setPath('*/*/edit', ['entity_id' => $id, '_current' => true]);
    }
    
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('MagentoYo_Articles::content_articles');
    }
}
