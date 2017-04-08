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

namespace WSite\Articles\Block\Adminhtml\Category\Grid\Column;

use Magento\Backend\Block\Widget\Grid\Column;

class Action extends Column
{
    protected $_categoryFactory;
    protected static $_parentModel = [];
    
    public function getActions()
    {
        $actions = parent::getActions();
        if (isset($actions['childs']['url'])) {
            $childsAction = &$actions['childs']['url'];
            $filter = 'parent_id=' . $this->getCurrentRow()->getId();
            $childsAction = $this->getUrl(
                $childsAction, 
                ['filter'=> base64_encode($filter)]
            );
        }
        
        if (isset($actions['articles']['url'])) {
            $childsAction = &$actions['articles']['url'];
            $childsAction = $this->getUrl(
                $childsAction, 
                ['category_id'=> $this->getCurrentRow()->getId()]
            );
        }
        
        if (isset($actions['create_child']['url'])) {
            $createChildAction = &$actions['create_child']['url'];
            $createChildAction = $this->getUrl(
                $createChildAction, 
                ['parent_id'=> $this->getCurrentRow()->getId()]
            );
        }
        
        if (isset($actions['create_article']['url'])) {
            $createArticleAction = &$actions['create_article']['url'];
            $createArticleAction = $this->getUrl(
                $createArticleAction, 
                ['category_ids'=> $this->getCurrentRow()->getId()]
            );
        }
        
        return $actions;
    }
    
    public function getRowField(\Magento\Framework\DataObject $row) {
        $this->setCurrentRow($row);
        
        return parent::getRowField($row);
    }
}