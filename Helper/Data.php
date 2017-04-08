<?php
namespace WSite\Articles\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    const SEO_ROBOTS_NOINDEX_NOFOLLOW = 0;
    const SEO_ROBOTS_INDEX_FOLLOW     = 1;
    const SEO_ROBOTS_INDEX_NOFOLLOW   = 2;
    const SEO_ROBOTS_NOINDEX_FOLLOW   = 3;
    
    /**
     * @var \Magento\UrlRewrite\Model\UrlRewriteFactory
     */
    protected $_modelUrlRewriteFactory;
    
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManagerModelFactory;
    
    /**
     * @param \Magento\UrlRewrite\Model\UrlRewriteFactory $modelUrlRewriteFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManagerModel
     * @param \Magento\Framework\App\Helper\Context $context
     */
    public function __construct(
        \Magento\UrlRewrite\Model\UrlRewriteFactory $modelUrlRewriteFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManagerModel,
        \Magento\Framework\App\Helper\Context $context
    ) {
        $this->_modelUrlRewriteFactory = $modelUrlRewriteFactory;
        $this->_storeManagerModelFactory = $storeManagerModel;
        
        parent::__construct($context);
    }
    
    /**
     * @return type
     */
    protected function _getStore()
    {
        return $this->_storeManagerModelFactory->getStore();
    }
    
    /**
     * @param type $model
     * @return string
     */
    protected function _getUrlCategory($model)
    {
        $storeId = $this->_getStore()->getId();
        $targetPath = 'articles/category/view/id/' . $model->getId();
        
        $urlRewriteModel = $this->_modelUrlRewriteFactory->create();
        $urlRewriteModel->setStoreId($storeId);
        $urlRewriteModel->load($targetPath, 'target_path');
        
        if ($urlRewriteModel->getId()) {
            return $this->_urlBuilder->getUrl('', ['_direct' => $urlRewriteModel->getRequestPath()]);
        }
        
        return $this->_urlBuilder->getUrl('articles/category/view', ['id'=>$model->getId()]);
    }
    
    /**
     * @param type $model
     * @param type $categoryModel
     * @return string
     */
    protected function _getUrlArticle($model, $categoryModel = null)
    {
        $storeId = $this->_getStore()->getId();
        $targetPath = 'articles/article/view/id/' . $model->getId();
        
        if ($categoryModel && $categoryModel->getId()) {
            $targetPath = $targetPath . '/category/' . $categoryModel->getId();
        }
        
        $urlRewriteModel = $this->_modelUrlRewriteFactory->create();
        $urlRewriteModel->setStoreId($storeId);
        $urlRewriteModel->load($targetPath, 'target_path');
        
        if ($urlRewriteModel->getId()) {
            return $this->_urlBuilder->getUrl('', ['_direct' => $urlRewriteModel->getRequestPath()]);
        }
        return $this->_urlBuilder->getUrl('articles/article/view', ['id'=>$model->getId()]);
    }
    
    /**
     * @param type $model
     * @param type $categoryModel
     * @return string
     */
    public function getUrl($model, $categoryModel = null)
    {
        if ($model instanceof \WSite\Articles\Model\Category) {
            return $this->_getUrlCategory($model);
        }
        
        if ($model instanceof \WSite\Articles\Model\Catalog) {
            return $this->_getUrlArticle($model, $categoryModel);
        }
    }
    
    /**
     * @param string $string
     * @return string
     */
    public static function toolUriTransform($string)
    {
        $string = strtolower($string);
        $string = preg_replace('/[^a-z0-9]/', '_', $string);
        while(strpos($string, '__')){
            $string = str_replace('__', '_', $string);
        }
        
        return $string;
    }
    
    /**
     * @param string $string
     * @param integer $length
     * @return string
     */
    public static function toolTextClear($string, $length)
    {
        $string = strip_tags($string);
        $string = html_entity_decode($string, ENT_NOQUOTES);
        
        $string = preg_replace('/[^\w]/', ' ', $string);
        
        while(strpos($string, "  ")) {
            $string = str_replace("  ", " ", $string);
        }
        
        if (mb_strlen(trim($string)) <= $length) {
            return trim($string);
        }
        
        $string = mb_substr($string, 0, $length + 1);
        $last = mb_strrpos($string, " ");
        return trim(mb_substr($string, 0, $last));
    }
}
