<?php

namespace MagentoYo\Articles\Block\Adminhtml\Category;

class Tree extends \MagentoYo\Articles\Block\Adminhtml\Category\Tree\Items {
    
    protected $_backendHelper;
    protected $_categoryFactory;
    protected $_backendSession;
    
    public function __construct(
        \Magento\Backend\Helper\Data $backendHelper,
        \MagentoYo\Articles\Model\Category\Repository $categoryRepository,
        \MagentoYo\Articles\Model\CategoryFactory $categoryFactory,
        \MagentoYo\Articles\Block\Adminhtml\Category\Tree\ItemsFactory $itemsBlockFactory,
        \Magento\Backend\Model\Session $backendSession,
        \Magento\Backend\Block\Template\Context $context,
        array $data = array()
    ) {
        $this->_backendHelper = $backendHelper;
        $this->_categoryRepository = $categoryRepository;
        $this->_categoryFactory = $categoryFactory;
        $this->_backendSession = $backendSession;
        
        parent::__construct($itemsBlockFactory, $context, $data);
    }
    
    protected function _construct()
    {
        $this->setTemplate('articles/category/tree.phtml');
        
        \Magento\Backend\Block\Template::_construct();
        
        $filterEncoded = $this->getRequest()->getParam('filter');
        if (!$filterEncoded) {
            $filterEncoded = $this->_backendSession->getData('entity_idfilter');
        }
        
        
        $filterData = $this->_backendHelper->prepareFilterString($filterEncoded);
        $parentId = isset($filterData['parent_id']) ? $filterData['parent_id'] : null;
        
        $categoryTreePath = [];
        $this->_selectedId = $treeParentId = $parentId;
        if ($treeParentId) {
            do {
                $categoryModel = $this->_categoryRepository->get($treeParentId);

                $categoryCollection = $this->_categoryFactory->create()->getCollection();
                $categoryCollection->addFieldToFilter('parent_id', $categoryModel->getId());
                $categoryModel->setSubCategories($categoryCollection);

                array_push($categoryTreePath, $categoryModel);
            } while($treeParentId = $categoryModel->getParentId());
        }

        $categoryModel = $this->_categoryFactory->create();
        $categoryCollection = $this->_categoryFactory->create()->getCollection();
        $categoryCollection->addFieldToFilter('parent_id', 0);
        $categoryModel->setId(0);
        $categoryModel->setSubCategories($categoryCollection);
        array_push($categoryTreePath, $categoryModel);
        
        $categoryTreePath = array_reverse($categoryTreePath);
        
        $categoryTree = [0=>[]];
        $categoryRegistry = [];
        $categoryTreeLink = &$categoryTree;
        foreach($categoryTreePath as $item){
            $categoryTreeLink = &$categoryTreeLink[$item->getid()];
            $categoryRegistry[$item->getId()] = $item;
                    
            $categories = [];
            foreach($item->getSubCategories() as $categoryModel){
                $categoryRegistry[$categoryModel->getId()] = $categoryModel;
                $categories[$categoryModel->getId()] = [];
            }
            $categoryTreeLink = $categories;
        }
        
        $this->_categoryRegistry = $categoryRegistry;
        
        $this->assign('tree', $categoryTree[0]);
        $this->assign('selectedId', $parentId);
    }
}
