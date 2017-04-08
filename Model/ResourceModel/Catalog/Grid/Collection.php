<?php

namespace WSite\Articles\Model\ResourceModel\Catalog\Grid;

use Magento\Framework\Api\Search\SearchResultInterface;
use WSite\Articles\Model\ResourceModel\Catalog\Collection as CatalogCollection;

class Collection extends CatalogCollection implements SearchResultInterface
{
    protected $aggregations;

    protected function _construct()
    {
        $this->_init(
            'Magento\Framework\View\Element\UiComponent\DataProvider\Document',
            'WSite\Articles\Model\ResourceModel\Catalog'
        );
    }
    
    public function getSearchCriteria()
    {
        return null;
    }
    
    public function setSearchCriteria(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria = null)
    {
        return $this;
    }
    
    public function getAggregations()
    {
        return $this->aggregations;
    }

    public function setAggregations($aggregations)
    {
        $this->aggregations = $aggregations;
    }
    
    public function setItems(array $items = null)
    {
        return $this;
    }
    
    public function getTotalCount()
    {
        return $this->getSize();
    }

    public function setTotalCount($totalCount)
    {
        return $this;
    }
}
