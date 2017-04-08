<?php
namespace WSite\Articles\Model;

class Bound extends \Magento\Framework\Model\AbstractModel
{
    protected $_boundFactory;
    
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \WSite\Articles\Model\BoundFactory $boundFactory,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = array()
    ) {
        $this->_boundFactory = $boundFactory;
        
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }
    
    protected function _construct()
    {
        parent::_construct();
        $this->_init('WSite\Articles\Model\ResourceModel\Bound');
    }
    
    public function saveBound($articleId, $categoryIds)
    {
        $this->deleteByProduct($articleId);
        
        if (!$categoryIds || !is_array($categoryIds)) {
            return $this;
        }
        
        foreach($categoryIds as $categoryId) {
            $categoryModel = $this->_boundFactory->create();
            $categoryModel->setData('article_id', $articleId);
            $categoryModel->setData('category_id', $categoryId);
            $categoryModel->save();
        }
        
        return $this;
    }
    
    public function deleteByProduct($id)
    {
        $this->getResource()->deleteByProduct($id);
        return $this;
    }
}
