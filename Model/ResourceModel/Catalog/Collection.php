<?php

namespace WSite\Articles\Model\ResourceModel\Catalog;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * @var \WSite\Articles\Model\ResourceModel\Bound\CollectionFactory
     */
    protected $_boundCollectionFactory;
    
    /**
     * @param \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \WSite\Articles\Model\ResourceModel\Bound\CollectionFactory $boundCollectionFactory
     * @param \Magento\Framework\DB\Adapter\AdapterInterface $connection
     * @param \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource
     * @return type
     */
    public function __construct(
        \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \WSite\Articles\Model\ResourceModel\Bound\CollectionFactory $boundCollectionFactory,
        \Magento\Framework\DB\Adapter\AdapterInterface $connection = null,
        \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource = null
    ) {
        $this->_boundCollectionFactory = $boundCollectionFactory;
        
        return parent::__construct(
            $entityFactory,
            $logger,
            $fetchStrategy,
            $eventManager,
            $connection,
            $resource
        );
    }
    
    /**
     * @return void;
     */
    protected function _construct()
    {
        $this->_init(
            'WSite\Articles\Model\Catalog',
            'WSite\Articles\Model\ResourceModel\Catalog'
        );
    }
    
    /**
     * @return $this
     */
    public function joinCategoryIds($leftJoin = false)
    {
        if ($leftJoin) {
            $this->getSelect()->joinLeft(
                ['bound' => $this->getTable('wsite_articles_bound')],
                'main_table.entity_id = bound.article_id',
                ['category_id'=>'bound.category_id']
            );
        } else {
            $this->join(
                ['bound' => $this->getTable('wsite_articles_bound')],
                'main_table.entity_id = bound.article_id',
                ['category_id'=>'bound.category_id']
            );
        }
        
        return $this;
    }
    
    /**
     * @return $this
     */
    public function addCategories()
    {
        if (!$this->getAllIds()) {
            return $this;
        }
        $boundCollection = $this->_boundCollectionFactory->create();
        $boundCollection->addFieldToFilter('article_id', $this->getAllIds());
        
        $boundArticlesData = [];
        foreach($boundCollection as $boundModel) {
            if (!isset($boundArticlesData[$boundModel->getArticleId()])) {
                $boundArticlesData[$boundModel->getArticleId()] = [];
            }
            
            $boundArticlesData[$boundModel->getArticleId()][] = $boundModel->getCategoryId();
        }
        
        foreach($this as $catalogModel) {
            if (!isset($boundArticlesData[$catalogModel->getId()])) {
                continue;
            }
            
            $catalogModel->setData('category_ids', $boundArticlesData[$catalogModel->getId()]);
        }
        
        return $this;
    }
}