<?php
namespace WSite\Articles\Model;

use \WSite\Articles\Helper\Data as HelperData;

class Catalog extends \Magento\Framework\Model\AbstractModel
{
    /**
     * @var \WSite\Articles\Model\Bound
     */
    protected $_boundModel;
    
    /**
     * @var \WSite\Articles\Model\BoundFactory
     */
    protected $_boundModelFactory;
    
    /**
     * @var \WSite\Articles\Model\ResourceModel\Bound\CollectionFactory
     */
    protected $_boundCatalogCollectionFactory;
    
    /**
     * @var \WSite\Articles\Model\ResourceModel\Category\CollectionFactory
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

    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \WSite\Articles\Model\BoundFactory $boundModelFactory
     * @param \WSite\Articles\Model\ResourceModel\Bound\CollectionFactory $boundCatalogCollectionFactory
     * @param \WSite\Articles\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory
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
        \WSite\Articles\Model\BoundFactory $boundModelFactory,
        \WSite\Articles\Model\ResourceModel\Bound\CollectionFactory $boundCatalogCollectionFactory,
        \WSite\Articles\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory,
        \Magento\UrlRewrite\Model\UrlRewriteFactory $modelUrlRewriteFactory,
        \Magento\UrlRewrite\Model\ResourceModel\UrlRewriteCollectionFactory $modelUrlRewriteCollectionFactory,
        \Magento\Store\Model\System\Store $storeModel,
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
        
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }
    
    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init('WSite\Articles\Model\ResourceModel\Catalog');
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
        if (!$categoryIds = $this->getCategoryIds()) {
            $categoryIds = [];
        }
        
        $urlRewriteCollection = $this->_modelUrlRewriteCollectionFactory->create();
        
        $targetPaths = [];
        foreach ($categoryIds as $categoryId) {
            $targetPaths[] = 'articles/category/view/id/' . $categoryId;
        }
        
        $storesIds = [];
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
            $modelUrlRewrite->setStoreId($storeModel->getStoreId());
            $modelUrlRewrite->load($currentTargetPath, 'target_path');

            $modelUrlRewrite->setData('entity_type', 'articles_catalog');
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

        return parent::beforeDelete();
    }
}
