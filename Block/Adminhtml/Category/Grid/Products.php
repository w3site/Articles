<?php

namespace MagentoYo\Articles\Block\Adminhtml\Category\Grid;

class Products extends \Magento\Backend\Block\Widget\Grid
{
    /**
     * Page and sorting var names
     *
     * @var string
     */
    protected $_varNameLimit = 'product_limit';

    /**
     * @var string
     */
    protected $_varNamePage = 'product_page';

    /**
     * @var string
     */
    protected $_varNameSort = 'product_sort';

    /**
     * @var string
     */
    protected $_varNameDir = 'product_dir';

    /**
     * @var string
     */
    protected $_varNameFilter = 'product_filter';
    
    /**
     * 
     */
    protected function _prepareCollection()
    {
        parent::_prepareCollection();
        $collection = $this->getCollection();
        
        $gridBlock = $this->getLayout()->getBlock('magentoyo_articles_category_grid.grid');
        $filter = $gridBlock->getParam($gridBlock->getVarNameFilter(), null);
        $data = $this->_backendHelper->prepareFilterString($filter);
        
        if (isset($data['parent_id']) && $data['parent_id']) {
            $collection->getSelect()->joinLeft(['bound' => $collection->getConnection()->getTableName('magentoyo_articles_bound')], 'main_table.entity_id = bound.article_id');
            $collection->getSelect()->where('bound.category_id = ?', (integer) $data['parent_id']);
        }
    }
}