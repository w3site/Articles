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

class Catalog extends \Magento\Framework\Model\AbstractModel
{
    /**
     * @var \MagentoYo\Articles\Model\Bound
     */
    protected $_boundModel;
    
    /**
     * @var \MagentoYo\Articles\Model\BoundFactory
     */
    protected $_boundModelFactory;
    
    /**
     * @var \MagentoYo\Articles\Model\ResourceModel\Bound\CollectionFactory
     */
    protected $_boundCatalogCollectionFactory;
    
    /**
     * @var \MagentoYo\Articles\Model\ResourceModel\Category\CollectionFactory
     */
    protected $_categoryCollectionFactory;
    
    /**
     * @var \Magento\UrlRewrite\Model\UrlRewriteFactory
     */
    protected $_modelUrlRewriteFactory;
    
    /**
     * @var \Magento\UrlRewrite\Model\ResourceModel\UrlRewriteCollectionFactory
     */
    protected $_modelUrlRewriteCollectionFactory;
    
    /**
     * @var \Magento\Store\Model\System\Store
     */
    protected $_storeModel;

    protected $_storeManager;
    
    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \MagentoYo\Articles\Model\BoundFactory $boundModelFactory
     * @param \MagentoYo\Articles\Model\ResourceModel\Bound\CollectionFactory $boundCatalogCollectionFactory
     * @param \MagentoYo\Articles\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory
     * @param \Magento\UrlRewrite\Model\UrlRewriteFactory $modelUrlRewriteFactory
     * @param \Magento\UrlRewrite\Model\ResourceModel\UrlRewriteCollectionFactory $modelUrlRewriteCollectionFactory
     * @param \Magento\Store\Model\System\Store $storeModel
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \MagentoYo\Articles\Model\BoundFactory $boundModelFactory,
        \MagentoYo\Articles\Model\ResourceModel\Bound\CollectionFactory $boundCatalogCollectionFactory,
        \MagentoYo\Articles\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory,
        \Magento\UrlRewrite\Model\UrlRewriteFactory $modelUrlRewriteFactory,
        \Magento\UrlRewrite\Model\ResourceModel\UrlRewriteCollectionFactory $modelUrlRewriteCollectionFactory,
        \Magento\Store\Model\System\Store $storeModel,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = array()
    ) {
        $this->_boundModelFactory = $boundModelFactory;
        $this->_boundCatalogCollectionFactory = $boundCatalogCollectionFactory;
        $this->_categoryCollectionFactory = $categoryCollectionFactory;
        $this->_modelUrlRewriteFactory = $modelUrlRewriteFactory;
        $this->_modelUrlRewriteCollectionFactory = $modelUrlRewriteCollectionFactory;
        $this->_boundModel = $boundModelFactory->create();
        $this->_storeModel = $storeModel;
        $this->_storeManager = $storeManager;
        
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }
    
    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init('MagentoYo\Articles\Model\ResourceModel\Catalog');
    }
    
    /**
     * @return $this
     */
    public function saveCategories()
    {
        $this->_boundModel->saveBound($this->getId(), $this->getData('category_ids'));
        
        return $this;
    }

    /**
     * @return array
     */
    public function getCategoryIds()
    {
        if ($this->hasData('category_ids')) {
            return $this->getData('category_ids');
        }
        
        $categoryIds = [];
        
        $boundCatalogCollection = $this->_boundCatalogCollectionFactory->create();
        $boundCatalogCollection->addFieldToFilter('article_id', $this->getId());
        foreach ($boundCatalogCollection as $boundModel) {
            $categoryIds[] = $boundModel->getCategoryId();
        }
        
        $this->getData('category_ids', $categoryIds);
        return $this->getData('category_ids');
    }
    
    /**
     * @return $this
     */
    public function generateUrlRewrite()
    {
        if ($this->getSeoUrlKey() == $this->getOrigData('seo_url_key')) {
            return;
        }
        
        if (!$categoryIds = $this->getCategoryIds()) {
            $categoryIds = [];
        }
        
        // Deleting the url rewrites
        $urlRewriteCollection =  $this->_modelUrlRewriteCollectionFactory->create();
        $urlRewriteCollection->addFieldToFilter('entity_type', 'articles_article');
        $urlRewriteCollection->addFieldToFilter('entity_id', $this->getId());
        $urlRewriteCollection->addFieldToFilter('is_autogenerated', 1);
        foreach ($urlRewriteCollection as $modelUrl) {
            $modelUrl->delete();
        }

        // Generate rewrites
        $urlRewriteCollection = $this->_modelUrlRewriteCollectionFactory->create();
        
        $targetPaths = [];
        foreach ($categoryIds as $categoryId) {
            $targetPaths[] = 'articles/category/view/id/' . $categoryId;
        }
        
        foreach ($this->_storeModel->getStoreCollection() as $storeModel) {
            $storesIds[] = $storeModel->getId();
        }

        $articleUris = [];
        foreach ($storesIds as $storeId) {
            if (!isset($articleUriByStore[$storeId])) {
                $articleUriByStore[$storeId] = [];
            }

            $dataObject = new \Magento\Framework\DataObject;
            $dataObject->setData('uri', $this->getSeoUrlKey());
            $dataObject->setData('store_id', $storeId);
            $articleUris[] = $dataObject;
        }

        if ($targetPaths && $storesIds) {
            $urlRewriteCollection->addFieldToFilter('target_path', $targetPaths);
            $urlRewriteCollection->addFieldToFilter('store_id', $storesIds);

            foreach ($urlRewriteCollection as $urlRewriteModel) {
                $dataObject = new \Magento\Framework\DataObject;
                $uri = $urlRewriteModel->getRequestPath();
                $dataObject->setData('uri', $uri . '/' . $this->getSeoUrlKey());
                $dataObject->setData('category_id', $urlRewriteModel->getEntityId());
                $dataObject->setData('store_id', $urlRewriteModel->getStoreId());

                $articleUris[] = $dataObject;
            }
        }
        
        // Create rewrites
        $targetPath = 'articles/article/view/id/' . $this->getId();
        
        foreach ($articleUris as $storeId => $dataObject) {
            if ($dataObject->getData('category_id')) {
                $currentTargetPath = $targetPath . '/category/' . $dataObject->getCategoryId();
                $meta = serialize(['category_id' => $dataObject->getCategoryId()]);
            }
            else {
                $currentTargetPath = $targetPath;
                $meta = null;
            }
            
            $modelUrlRewrite = $this->_modelUrlRewriteFactory->create();
            $modelUrlRewrite->setStoreId($storeId);
            $modelUrlRewrite->load($currentTargetPath, 'target_path');

            $modelUrlRewrite->setData('entity_type', 'articles_article');
            $modelUrlRewrite->setData('entity_id', $this->getId());
            $modelUrlRewrite->setData('request_path', $dataObject->getUri());
            $modelUrlRewrite->setData('target_path', $currentTargetPath);
            $modelUrlRewrite->setData('store_id', $dataObject->getStoreId());
            $modelUrlRewrite->setData('is_autogenerated', 1);
            $modelUrlRewrite->setData('metadata', $meta);
            try {
                $modelUrlRewrite->save();
            }
            catch (\Exception $e) {
                $modelUrlRewrite->setData('request_path', $dataObject->getUri() . '_' . uniqid());
                $modelUrlRewrite->save();
            }
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
     * @return string
     */
    protected function _getTranformedSeoDescription()
    {
        if(!($description = $this->getArticle())){
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
    
    /**
     * @return boolean
     */
    public function isReadonly()
    {
        return false;
    }
    
    public function beforeDelete()
    {
        $id = $this->getId();
        
        $boundCatalogCollection = $this->_boundCatalogCollectionFactory->create();
        $boundCatalogCollection->addFieldToFilter('article_id', $id);
        $boundCatalogCollection->walk('delete');
        
        foreach ($this->_storeModel->getStoreCollection() as $storeModel) {
            $modelUrlRewrite = $this->_modelUrlRewriteCollectionFactory->create();
            $modelUrlRewrite->addStoreFilter($storeModel->getId());
            $modelUrlRewrite->addFieldToFilter('entity_type', 'articles_article');
            $modelUrlRewrite->addFieldToFilter('entity_id', $id);
            
            foreach ($modelUrlRewrite as $rewriteModel) {
                $rewriteModel->delete();
            }
        }

        return parent::beforeDelete();
    }
    
    public function getUrls()
    {
        $return = [];
        if (!$this->getId()) {
            return $return;
        }
        
        $modelUrlRewrite = $this->_modelUrlRewriteCollectionFactory->create();
        $modelUrlRewrite->addFieldToFilter('entity_type', 'articles_article');
        $modelUrlRewrite->addFieldToFilter('entity_id', $this->getId());
        
        foreach($modelUrlRewrite as $modelRewrite) {
            $storeUrl = $this->_storeManager->getStore($modelRewrite->getStoreId())->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_WEB);
            $return[] = $storeUrl . $modelRewrite->getData('request_path');
        }
        
        return implode("\n", $return);
    }
}
