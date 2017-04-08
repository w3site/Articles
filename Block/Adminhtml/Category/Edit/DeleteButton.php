<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace WSite\Articles\Block\Adminhtml\Category\Edit;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;
use Magento\Backend\Block\Widget\Context;

/**
 * Class DeleteButton
 */
class DeleteButton implements ButtonProviderInterface
{
    protected $_context;
    
    public function __construct(
        Context $context
    ) {
        $this->_context = $context;
    }
    
    /**
     * @return array
     */
    public function getButtonData()
    {
        $data = [];
        $entityId = $this->_context->getRequest()->getParam('entity_id');
        if ($entityId) {
            $data = [
                'label' => __('Delete'),
                'class' => 'delete',
                'on_click' => 'deleteConfirm(\'' . __(
                    'Are you sure you want to do this?'
                ) . '\', \'' . $this->getDeleteUrl($entityId) . '\')',
                'sort_order' => 20,
            ];
        }
        return $data;
    }

    /**
     * @return string
     */
    public function getDeleteUrl($entityId)
    {
        return $this->_getUrl('*/*/massRemove', ['entity_id' => $entityId]);
    }
    
    protected function _getUrl($route = '', $params = [])
    {
        return $this->_context->getUrlBuilder()->getUrl($route, $params);
    }
}
