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

namespace MagentoYo\Articles\Ui\DataProvider\CatalogDataProvider;

class Grid extends \Magento\Framework\View\Element\UiComponent\DataProvider\DataProvider
{
    protected $_boundCollectionFactory;
    
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        \Magento\Framework\Api\Search\ReportingInterface $reporting,
        \Magento\Framework\Api\Search\SearchCriteriaBuilder $searchCriteriaBuilder,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Framework\Api\FilterBuilder $filterBuilder,
        \MagentoYo\Articles\Model\ResourceModel\Bound\CollectionFactory $boundCollectionFactory,
        array $meta = array(),
        array $data = array()
    ) {
        $this->_boundCollectionFactory = $boundCollectionFactory;
        
        parent::__construct(
            $name,
            $primaryFieldName,
            $requestFieldName,
            $reporting,
            $searchCriteriaBuilder,
            $request,
            $filterBuilder,
            $meta,
            $data
        );
    }
    
    public function getSearchResult()
    {
        $collection = parent::getSearchResult();
        $collection->joinCategoryIds(true);
        
//        $filters = $this->request->getParam('filters');
//        $categoryId = null;
//        if (isset($filters['category_id'])) {
//            $collection->addFieldToFilter('category_id', $filters['category_id']);
//        }
        
        $collection->getSelect()->group('entity_id');

        return $collection;
    }
    
    public function getData()
    {
        $data = parent::getData();
        
        $items = [];
        foreach ($data['items'] as $item) {
            if (!$item['category_id']) {
                continue;
            }
            
            $items[$item['entity_id']] = [];
        }
        
        $ids = array_keys($items);
        if (!$ids) {
            return $data;
        }
        
        $boundCollection = $this->_boundCollectionFactory->create();
        $boundCollection->addFieldToFilter('article_id', $ids);
        
        foreach ($boundCollection as $boundModel) {
            array_push($items[$boundModel->getArticleId()], $boundModel->getCategoryId());
        }
        
        foreach ($data['items'] as &$item) {
            if (!isset($items[$item['entity_id']])) {
                continue;
            }
            $item['category_id'] = implode(', ', $items[$item['entity_id']]);
        }
        
        return $data;
    }
}
