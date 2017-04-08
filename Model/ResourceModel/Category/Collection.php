<?php

namespace WSite\Articles\Model\ResourceModel\Category;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * @return void;
     */
    protected function _construct()
    {
        $this->_init(
            'WSite\Articles\Model\Category',
            'WSite\Articles\Model\ResourceModel\Category'
        );
    }
}
