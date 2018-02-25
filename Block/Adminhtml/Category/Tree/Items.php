<?php

namespace MagentoYo\Articles\Block\Adminhtml\Category\Tree;

class Items extends \Magento\Backend\Block\Template
{
    protected $_itemsBlockFactory;
    protected $_categoryRepository;
    protected $_selectedId;

    public function __construct(
        \MagentoYo\Articles\Block\Adminhtml\Category\Tree\ItemsFactory $itemsBlockFactory,
        \Magento\Backend\Block\Template\Context $context,
        array $data = array()
    ) {
        $this->_itemsBlockFactory = $itemsBlockFactory;
        
        parent::__construct($context, $data);
    }
    
    protected function _construct() {
        $this->setTemplate('articles/category/tree/items.phtml');
        
        parent::_construct();
    }
    
    public function setSelectedId($id)
    {
        $this->_selectedId = $id;
        return $this;
    }
    
    public function renderTree($items)
    {
        $itemsBlock = $this->_itemsBlockFactory->create();
        $itemsBlock->assign('selectedId', $this->_selectedId);
        $itemsBlock->assign('items', $items);
        $itemsBlock->setCategoryRegistry($this->_categoryRegistry);
        $itemsBlock->setSelectedId($this->_selectedId);
        return $itemsBlock->toHtml();
    }
    
    public function setCategoryRegistry(&$categoryRegistry)
    {
        $this->_categoryRegistry = &$categoryRegistry;
        return $this;
    }
    
    public function getCategoryModel($id)
    {
        if (!isset($this->_categoryRegistry[$id])) {
            return;
        }
        
        return $this->_categoryRegistry[$id];
    }
}