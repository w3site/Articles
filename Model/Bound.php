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

namespace MagentoYo\Articles\Model;

class Bound extends \Magento\Framework\Model\AbstractModel
{
    protected $_boundFactory;
    
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \MagentoYo\Articles\Model\BoundFactory $boundFactory,
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
        $this->_init('MagentoYo\Articles\Model\ResourceModel\Bound');
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
