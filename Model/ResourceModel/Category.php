<?php
namespace WSite\Articles\Model\ResourceModel;

class Category extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init('wsite_articles_category', 'entity_id');
    }
    
    /**
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return type
     */
    protected function _beforeDelete(\Magento\Framework\Model\AbstractModel $object) {
        return parent::_beforeDelete($object);
    }
}
