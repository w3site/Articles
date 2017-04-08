<?php

namespace WSite\Articles\Model\ResourceModel\Bound;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * @return void;
     */
    protected function _construct()
    {
        $this->_init(
            'WSite\Articles\Model\Bound',
            'WSite\Articles\Model\ResourceModel\Bound'
        );
    }
}
