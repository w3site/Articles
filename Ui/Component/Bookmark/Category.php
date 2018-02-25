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

namespace MagentoYo\Articles\Ui\Component\Bookmark;

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
