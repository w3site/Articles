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
    protected $_categoryRepository;
    protected $_categoryModel;
    
    public function __construct(
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\Backend\Block\Widget\Context $context,
        \WSite\Articles\Model\Category\Repository $categoryRepository,
        array $data = array()
    ) {
        $this->_backendHelper = $backendHelper;
        $this->_categoryRepository = $categoryRepository;
        
        parent::__construct($context, $data);
    }
    
    protected function _construct(
    )
    {
        $this->_controller = 'adminhtml_category_grid';
        $this->_blockGroup = 'WSite_Articles';
        $this->_headerText = __('Articles');
        $this->_addButtonLabel = __('Create');
        
        //parent::_construct();
    }
    
    /**
     * @return $this
     */
    protected function _prepareLayout()
    {
        $gridBlock = $this->getLayout()->getBlock('wsite_articles_category_grid.grid');
        $filter = $gridBlock->getParam($gridBlock->getVarNameFilter(), null);
        $data = $this->_backendHelper->prepareFilterString($filter);
        
        if (isset($data['parent_id']) && $data['parent_id']) {
            $categoryModel = $this->_categoryRepository->get($data['parent_id']);
            $this->_categoryModel = $categoryModel;
        }
        
        $rootUrl = $this->getUrl(
            '*/*/index', 
            ['filter'=> base64_encode('parent_id=')]
        );

        $this->addButton(
            'new',
            [
                'label' => 'Create',
                'onclick' => 'setLocation(\'' . $this->getCreateUrl() . '\')',
                'class' => 'primary'
            ]
        );
        
        $this->addButton(
            'all_items',
            [
                'label' => 'All',
                'onclick' => 'setLocation(\'' . $rootUrl . '\')',
                'class' => ''
            ]
        );
        
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
                    'label' => 'Root',
                    'onclick' => 'setLocation(\'' . $rootUrl . '\')',
                    'class' => ''
                ]
            );
        }
        
        if (isset($data['parent_id']) && $data['parent_id']) {
            $parentUrl = $this->getUrl(
                '*/*/index', 
                ['filter'=> base64_encode('parent_id=' . $categoryModel->getParentId())]
            );

            $this->addButton(
                'parent_items',
                [
                    'label' => 'Parent',
                    'onclick' => 'setLocation(\'' . $parentUrl . '\')',
                    'class' => ''
                ]
            );
        }
        
        return parent::_prepareLayout();
    }

    /**
     * @return string
     */
    public function getCreateUrl()
    {
        if ($this->_categoryModel){
            return $this->getUrl('*/*/edit', ['parent_id' => $this->_categoryModel->getId()]);
        }
        
        return $this->getUrl('*/*/edit');
    }
}
