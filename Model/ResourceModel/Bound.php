<?php
namespace WSite\Articles\Model\ResourceModel;

class Bound extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    protected $_boundCollectionFactory;
    
    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        \WSite\Articles\Model\ResourceModel\Bound\CollectionFactory $boundCollectionFactory,
        $connectionName = null
    ) {
        $this->_boundCollectionFactory = $boundCollectionFactory;
        
        parent::__construct($context, $connectionName);
    }
    
    protected function _construct()
    {
        $this->_init('wsite_articles_bound', 'row_id');
    }
    
    public function deleteByProduct($id)
    {
        $boundCollection = $this->_boundCollectionFactory->create();
        $boundCollection->addFieldToFilter('article_id', $id);
        $boundCollection->walk('delete');
    }
}
