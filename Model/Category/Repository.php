<?php

namespace MagentoYo\Articles\Model\Category;

class Repository
{
    protected $_byId = [];
    protected $_categoryFactory;
    
    public function __construct(
        \MagentoYo\Articles\Model\CategoryFactory $categoryFactory
    ) {
        $this->_categoryFactory = $categoryFactory;
    }
    
    public function get($id)
    {
        if (isset($this->_byId[$id])) {
            return $this->_byId[$id];
        }
        
        $categoryModel = $this->_categoryFactory->create()->load($id);
        $this->_byId[$id] = $categoryModel;
        
        return $categoryModel;
    }
}