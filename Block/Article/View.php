<?php
namespace WSite\Articles\Block\Article;

class View extends \Magento\Framework\View\Element\Template
{
    protected $_coreRegistry;
    
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        array $data = array()
    ) {
        $this->_coreRegistry = $coreRegistry;
        
        parent::__construct($context, $data);
    }
    
    protected function _construct()
    {
        $this->setTemplate('WSite_Articles::article/view.phtml');
        parent::_construct();
    }
    
    protected function _getArticle()
    {
        return $this->_coreRegistry->registry('current_article');
    }
    
    public function getTitle()
    {
        return $this->_getArticle()->getTitle();
    }
    
    public function getContent()
    {
        return $this->_getArticle()->getArticle();
    }
}
