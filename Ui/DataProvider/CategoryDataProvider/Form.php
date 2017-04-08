<?php
namespace WSite\Articles\Ui\DataProvider\CategoryDataProvider;

use WSite\Articles\Model\ResourceModel\Category\CollectionFactory;

class Form extends \Magento\Ui\DataProvider\AbstractDataProvider
{
    protected $_loadedData;

    protected $_context;
    
    /**
     * Construct
     *
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $collectionFactory
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        \Magento\Framework\View\Element\UiComponent\ContextInterface $context,
        array $meta = [],
        array $data = []
    ) {
        $this->_context = $context;
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->collection = $collectionFactory->create();
    }
    
    public function getData()
    {
        if ($this->_loadedData !== null) {
            return $this->_loadedData;
        }

        $this->_loadedData = [];
        
        foreach (parent::getData()['items'] as $item) {
            $this->_loadedData[$item['entity_id']] = $item;
        }
        
        if (!$this->_loadedData && $parentId = $this->_context->getRequestParam('parent_id')) {
            $this->_loadedData[null] = [];
            $this->_loadedData[null]['parent_id'] = $parentId;
        }
        
        return $this->_loadedData;
    }
}
