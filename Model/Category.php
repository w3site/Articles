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

use \MagentoYo\Articles\Helper\Data as HelperData;

class Category extends \Magento\Framework\Model\AbstractModel
{
    /**
     * @var \MagentoYo\Articles\Model\CategoryFactory
     */
    protected $_modelCategoryFactory;
    
    /**
     * @var \Magento\UrlRewrite\Model\UrlRewriteFactory
     */
    protected $_modelUrlRewriteFactory;
    
    /**
     * @var \Magento\Store\Model\System\Store
     */
    protected $_storeModel;
    
    protected $_boundCatalogCollectionFactory;
    
    protected $_modelUrlRewriteCollectionFactory;
    
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\UrlRewrite\Model\UrlRewriteFactory $modelUrlRewriteFactory,
        \MagentoYo\Articles\Model\ResourceModel\Bound\CollectionFactory $boundCatalogCollectionFactory,
        \MagentoYo\Articles\Model\CategoryFactory $modelCategoryFactory,
        \MagentoYo\Articles\Model\ResourceModel\Category\CollectionFactory $modelCategoryCollectionFactory,
        \Magento\UrlRewrite\Model\ResourceModel\UrlRewriteCollectionFactory $modelUrlRewriteCollectionFactory,
        \Magento\Store\Model\System\Store $storeModel,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = array()
    ) {
        $this->_modelCategoryFactory = $modelCategoryFactory;
        $this->_modelCategoryCollectionFactory = $modelCategoryCollectionFactory;
        $this->_modelUrlRewriteFactory = $modelUrlRewriteFactory;
        $this->_storeModel = $storeModel;
        $this->_boundCatalogCollectionFactory = $boundCatalogCollectionFactory;
        $this->_modelUrlRewriteCollectionFactory = $modelUrlRewriteCollectionFactory;
        
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }
    
    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init('MagentoYo\Articles\Model\ResourceModel\Category');
    }
    
    /**
     * @return boolean
     */
    public function isReadonly()
    {
        return false;
    }
    
    /**
     * @return type
     */
    public function getFilterPrentCategoryId()
    {
        $filter = 'parent_id=' . $this->getId();
        return base64_encode($filter);
    }
    
    /**
     * @return $this
     */
    public function generateUrlRewrite()
    {
        $categoryArray = [];
        $categoryPath = [];
        
        if ($categoryPathString = trim($this->getCategoryPath(), '/')) {
            $categoryPath = explode("/", $categoryPathString);
            
            $parentCategoryCollection = $this->_modelCategoryCollectionFactory->create();
            $parentCategoryCollection->addFieldToFilter('entity_id', $categoryPath);

            foreach ($parentCategoryCollection as $parentCategoryModel) {
                $categoryArray[$parentCategoryModel->getId()] = $parentCategoryModel;
            }
        }
        
        $uri = '';
        foreach ($categoryPath as $categoryId) {
            $uri .= '/' . $categoryArray[$categoryId]->getSeoUrlKey();
        }
        
        $uri = trim($uri, '/');
        $uri .= '/' . $this->getSeoUrlKey();
        $uri = trim($uri, '/');
        
        $targetPath = 'articles/category/view/id/' . $this->getId();
        
        // @ TODO Multistore restriction
        $stores = $this->_storeModel->getStoreCollection();
        
        $urlRewriteCollection = $this->_modelUrlRewriteFactory->create()->getCollection();
        $urlRewriteCollection->addFieldToFilter('entity_type', 'articles_category');
        $urlRewriteCollection->addFieldToFilter('entity_id', $this->getId());
        foreach ($urlRewriteCollection as $modelUrl) {
            $modelUrl->delete();
        }
        
        foreach ($stores as $storeModel) {
            $modelUrlRewrite = $this->_modelUrlRewriteFactory->create();
            $modelUrlRewrite->setStoreId($storeModel->getStoreId());
            $modelUrlRewrite->load($targetPath, 'target_path');

            $modelUrlRewrite->setData('entity_type', 'articles_category');
            $modelUrlRewrite->setData('entity_id', $this->getId());
            $modelUrlRewrite->setData('request_path', $uri);
            $modelUrlRewrite->setData('target_path', $targetPath);
            $modelUrlRewrite->setData('store_id', $storeModel->getStoreId());
            $modelUrlRewrite->setData('is_autogenerated', 1);
            $modelUrlRewrite->save();
        }
        
        return $this;
    }
    
    /**
     * @return $this
     */
    public function autoFill()
    {
        if (!$this->getSeoUrlKey()) {
            $this->setSeoUrlKey($this->_getTranformedSeoUrlKey());
        }
        
        if (!$this->getSeoTitle()) {
            $this->setSeoTitle($this->getTitle());
        }
        
        if (!$this->getSeoDescription()) {
            $this->setSeoDescription($this->_getTranformedSeoDescription());
        }
        
        return $this;
    }
    
    /**
     * @return $this
     */
    public function generateCategoryPath()
    {
        $parentCategoryId = $this->getParentId();
        
        $path = [];
        while($parentCategoryId) {
            $path[] = $parentCategoryId;
            $modelCategory = $this->_modelCategoryFactory->create();
            $modelCategory->load($parentCategoryId);
            $parentCategoryId = $modelCategory->getParentId();
        }
        
        $path = array_reverse($path);
        
        $pathString = implode("/", $path);
        if ($pathString) {
            $pathString = '/' . $pathString . '/';
        }
        
        $this->setCategoryPath($pathString);
        
        return $this;
    }
    
    /**
     * @return string
     */
    protected function _getTranformedSeoDescription()
    {
        if(!($description = $this->getDescription())){
            return;
        }
        
        return HelperData::toolTextClear($description, 160);
    }
    
    /**
     * @return string
     */
    protected function _getTranformedSeoUrlKey()
    {
        if(!($title = $this->getTitle())){
            return;
        }
        
        return HelperData::toolUriTransform($title);
    }
    
    public function beforeDelete()
    {
        $id = $this->getId();
        
        $boundCatalogCollection = $this->_boundCatalogCollectionFactory->create();
        $boundCatalogCollection->addFieldToFilter('category_id', $id);
        $boundCatalogCollection->walk('delete');
        
        foreach ($this->_storeModel->getStoreCollection() as $storeModel) {
            $modelUrlRewrite = $this->_modelUrlRewriteCollectionFactory->create();
            $modelUrlRewrite->addStoreFilter($storeModel->getId());
            $modelUrlRewrite->addFieldToFilter('entity_type', 'articles_category');
            $modelUrlRewrite->addFieldToFilter('entity_id', $id);
            
            foreach ($modelUrlRewrite as $rewriteModel) {
                $rewriteModel->delete();
            }
        }

        return parent::beforeDelete();
    }
}
