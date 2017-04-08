<?php

namespace WSite\Articles\Model\Category\Field;

class Options implements \Magento\Framework\Option\ArrayInterface
{
    protected $_categoryFactory;
    protected $_oprionsArray;
    
    public function __construct(
        \WSite\Articles\Model\CategoryFactory $categoryFactory
    ) {
        $this->_categoryFactory = $categoryFactory;
    }
    
    protected function _processTree($categoriesTree, $categoriesArray)
    {
        $options = [];
        
        foreach ($categoriesTree as $categoryId => $categorySubData) {
            $optionData = [
                'value' => $categoriesArray[$categoryId]->getId(),
                'label' => $categoriesArray[$categoryId]->getTitle()
            ];
            
            if ($categorySubData){
                $optionData['optgroup'] = $this->_processTree($categorySubData, $categoriesArray);
            }
            
            $options[] = $optionData;
        }
        
        return $options;
    }
    
    public function toOptionArray()
    {
        if ($this->_oprionsArray !== null) {
            return $this->_oprionsArray;
        }
        
        $categoryModel = $this->_categoryFactory->create();
        $categoryCollection = $categoryModel->getCollection();
        
        $categoriesArray = [];
        $categoriesTree = [0=>[]];
        
        foreach($categoryCollection as $itemModel){
            $categoriesArray[$itemModel->getId()] = $itemModel;
        }
        
        foreach($categoriesArray as $categoryItem){
            if (!$categoryItem->getParentId()) {
                $categoryItem->setParentId(0);
            }
            
            if (!isset($categoriesTree[$categoryItem->getParentId()])) {
                $categoriesTree[$categoryItem->getParentId()] = [];
            }
            
            if (!isset($categoriesTree[$categoryItem->getId()])) {
                $categoriesTree[$categoryItem->getId()] = [];
            }
            
            $categoriesTree[$categoryItem->getParentId()][$categoryItem->getId()] = &$categoriesTree[$categoryItem->getId()];
        }
        
        $options = $this->_processTree($categoriesTree[0], $categoriesArray);
        
        return $options;
    }
}