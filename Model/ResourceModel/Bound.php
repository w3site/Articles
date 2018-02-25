<?php
/**
 * ╔╗╔╗╔╦══╦══╦══╦════╦═══╗╔══╦═══╦═══╗
 * ║║║║║╠═╗║╔═╩╗╔╩═╗╔═╣╔══╝║╔╗║╔═╗║╔══╝
 * ║║║║║╠═╝║╚═╗║║──║║─║╚══╗║║║║╚═╝║║╔═╗
 * ║║║║║╠═╗╠═╗║║║──║║─║╔══╝║║║║╔╗╔╣║╚╗║
 * ║╚╝╚╝╠═╝╠═╝╠╝╚╗─║║─║╚══╦╣╚╝║║║║║╚═╝║
 * ╚═╝╚═╩══╩══╩══╝─╚╝─╚═══╩╩══╩╝╚╝╚═══╝
 * 
 * Examples and documentation at the: http://w3site.org
 * 
 * @copyright   Copyright (c) 2015-2016 Tereta Alexander. (http://www.w3site.org)
 * @license     http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

namespace MagentoYo\Articles\Model\ResourceModel;

class Bound extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    protected $_boundCollectionFactory;
    
    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        \MagentoYo\Articles\Model\ResourceModel\Bound\CollectionFactory $boundCollectionFactory,
        $connectionName = null
    ) {
        $this->_boundCollectionFactory = $boundCollectionFactory;
        
        parent::__construct($context, $connectionName);
    }
    
    protected function _construct()
    {
        $this->_init('magentoyo_articles_bound', 'row_id');
    }
    
    public function deleteByProduct($id)
    {
        $boundCollection = $this->_boundCollectionFactory->create();
        $boundCollection->addFieldToFilter('article_id', $id);
        $boundCollection->walk('delete');
    }
}
