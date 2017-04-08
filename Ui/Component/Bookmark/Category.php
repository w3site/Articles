<?php
namespace WSite\Articles\Ui\Component\Bookmark;

class Category extends \Magento\Ui\Component\Bookmark
{
    public function prepare() {
        parent::prepare();
        
        $filter = [];
        
        if ($categoryId = $this->getContext()->getRequestParam('category_id')) {
            $filter['category_id'] = $categoryId;
        } else {
            $filter['category_id'] = null;
        }

        if ($filter) {
            $config = [
                'current' => [
                    'filters' => [
                        'applied' => $filter
                    ]
                ]
            ];
        }
        
        $config = array_replace_recursive($this->getConfiguration($this), $config);
        
        if ($filter['category_id'] === null) {
            unset($config['current']['filters']['applied']['category_id']);
        }
        
        $this->setData('config', $config);
    }
}
