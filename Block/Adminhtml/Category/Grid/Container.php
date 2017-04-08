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

namespace WSite\Articles\Block\Adminhtml\Category\Grid;

class Container extends \Magento\Backend\Block\Widget\Grid\Container
{
    protected $_backendHelper;
    protected $_categoryFactory;
    
    public function __construct(
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\Backend\Block\Widget\Context $context,
        \WSite\Articles\Model\CategoryFactory $categoryFactory,
        array $data = array()
    ) {
        $this->_backendHelper = $backendHelper;
        $this->_categoryFactory = $categoryFactory;
        
        parent::__construct($context, $data);
    }
    
    protected function _construct(
    )
    {
        $this->_controller = 'adminhtml_category_grid';
        $this->_blockGroup = 'WSite_Articles';
        $this->_headerText = __('Articles');
        $this->_addButtonLabel = __('Create');
        
        parent::_construct();
    }
    
    protected function _prepareLayout()
    {
        $gridBlock = $this->getChildBlock('grid');
        $filter = $gridBlock->getParam($gridBlock->getVarNameFilter(), null);
        $data = $this->_backendHelper->prepareFilterString($filter);
        
        if (
            !isset($data['parent_id']) ||
            (isset($data['parent_id']) && $data['parent_id'] != '0')
        ) {
            $rootUrl = $this->getUrl(
                '*/*/index', 
                ['filter'=> base64_encode('parent_id=0')]
            );

            $this->addButton(
                'root_item',
                [
                    'label' => 'Root category',
                    'onclick' => 'setLocation(\'' . $rootUrl . '\')',
                    'class' => ''
                ]
            );
        }
        
        if (isset($data['parent_id']) && $data['parent_id']) {
            $categoryModel = $this->_categoryFactory->create()->load($data['parent_id']);
            
            $parentUrl = $this->getUrl(
                '*/*/index', 
                ['filter'=> base64_encode('parent_id=' . $categoryModel->getParentId())]
            );

            $this->addButton(
                'parent_items',
                [
                    'label' => 'Parent category',
                    'onclick' => 'setLocation(\'' . $parentUrl . '\')',
                    'class' => ''
                ]
            );
        }
        
        return parent::_prepareLayout();
    }
    
    public function getCreateUrl()
    {
        return $this->getUrl('*/*/edit');
    }
}
