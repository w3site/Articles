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

namespace WSite\Articles\Controller\Adminhtml\Category;

class Save extends \Magento\Backend\App\Action
{
    protected $_categoryFactory;
    
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \WSite\Articles\Model\CategoryFactory $categoryFactory
    ) {
        $this->_categoryFactory = $categoryFactory;
        
        parent::__construct($context);
    }
    
    public function execute()
    {
        $id = (int) $this->getRequest()->getParam('entity_id');
        $data = $this->getRequest()->getPostValue();

        $resultRedirect = $this->resultRedirectFactory->create();
        if ($data) {
            $model = $this->_categoryFactory->create();

            if ($id) {
                $model->load($id);
            }
            else{
                unset($data['entity_id']);
            }

            try {
                $model->setData($data)
                    ->autoFill()
                    ->generateCategoryPath()
                    ->save()
                    ->generateUrlRewrite();
                $id = $model->getId();

                $this->messageManager->addSuccessMessage(__('The category was successfully saved.'));
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
        return $this->_authorization->isAllowed('WSite_Articles::content_articles');
    }
}
