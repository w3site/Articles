<?php
namespace WSite\Articles\Model\ResourceModel;

class Catalog extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init('wsite_articles_catalog', 'entity_id');
    }
}
