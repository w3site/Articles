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

namespace MagentoYo\Articles\Block\Category;

class View extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * @var \MagentoYo\Articles\Model\ResourceModel\Category\CollectionFactory
     */
    protected $_categoryFactory;

    /**
     * @var \MagentoYo\Articles\Model\ResourceModel\Category\Collection
     */
    protected $_categoriesCollection;
    
    /**
     * @var type
     */
    protected $_articlesCollection;
    
    /**
     * @var type
     */
    protected $_categoryCollectionFactory;
    
    /**
     * @var type
     */
    protected $_catalogCollectionFactory;
    
    /**
     * @var \MagentoYo\Articles\Helper\Data
     */
    protected $_helper;
    
    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \MagentoYo\Articles\Model\CategoryFactory $categoryFactory
     * @param \MagentoYo\Articles\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory
     * @param \MagentoYo\Articles\Model\ResourceModel\Catalog\CollectionFactory $catalogCollectionFactory
     * @param \MagentoYo\Articles\Helper\Data $helper
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \MagentoYo\Articles\Model\CategoryFactory $categoryFactory,
        \MagentoYo\Articles\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory,
        \MagentoYo\Articles\Model\ResourceModel\Catalog\CollectionFactory $catalogCollectionFactory,
        \MagentoYo\Articles\Helper\Data $helper,
        array $data = array()
    ) {
        $this->_coreRegistry = $coreRegistry;
        $this->_categoryFactory = $categoryFactory;
        $this->_categoryCollectionFactory = $categoryCollectionFactory;
        $this->_catalogCollectionFactory = $catalogCollectionFactory;
        $this->_helper = $helper;
        
        parent::__construct($context, $data);
    }
    
    /**
     * @return null
     */
    protected function _construct()
    {
        $this->setTemplate('MagentoYo_Articles::category/list.phtml');
        parent::_construct();
    }
    
    /**
     * @return \MagentoYo\Articles\Model\Category
     */
    public function getCategoryModel()
    {
        return $this->_coreRegistry->registry('current_category');
    }
    
    /**
     * @return \MagentoYo\Articles\Model\ResourceModel\Category\Collection
     */
    public function getCategoriesCollection()
    {
        if (!is_null($this->_categoriesCollection)) {
            return $this->_categoriesCollection;
        }
        
        $categoryId = $this->getCategoryModel()->getId();
        $categoryCollection = $this->_categoryCollectionFactory->create();
        $categoryCollection->addFieldToFilter('parent_id', $categoryId);
        
        $this->_categoriesCollection = $categoryCollection;
        return $this->_categoriesCollection;
    }
    
    /**
     * @return type
     */
    public function getArticlesCollection()
    {
        if (!is_null($this->_articlesCollection)) {
            return $this->_articlesCollection;
        }
        
        $categoryId = $this->getCategoryModel()->getId();
        $articlesCollection = $this->_catalogCollectionFactory->create();
        $articlesCollection->joinCategoryIds();
        $articlesCollection->addFieldToFilter('category_id', $categoryId);
        
        $this->_articlesCollection = $articlesCollection;
        
        return $this->_articlesCollection;
    }
    
    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->getCategoryModel()->getTitle();
    }
    
    /**
     * @return MagentoYo\Articles\Helper\Data
     */
    public function getHelper()
    {
        return $this->_helper;
    }
}
