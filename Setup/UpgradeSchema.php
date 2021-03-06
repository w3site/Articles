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

namespace MagentoYo\Articles\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;
use MagentoYo\Articles\Helper\Data as HelperData;

class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * {@inheritdoc}
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if (version_compare($context->getVersion(), '0.0.2', '<')) {
            $this->upgradeFirst($setup);
        }

        $setup->endSetup();
    }
    
    protected function upgradeFirst($setup)
    {
        $connection = $setup->getConnection();
        
        $column = [
            'type'     => Table::TYPE_TEXT,
            'length'   => 255,
            'nullable' => false,
            'comment'  => 'SEO URL Key',
            'default'  => '',
            'after'    => 'description'
        ];
        $connection->addColumn($setup->getTable('magentoyo_articles_category'), 'seo_url_key', $column);
        
        $column = [
            'type'     => Table::TYPE_TEXT,
            'length'   => 255,
            'nullable' => false,
            'comment'  => 'SEO Title',
            'default'  => '',
            'after'    => 'seo_description'
        ];
        $connection->addColumn($setup->getTable('magentoyo_articles_category'), 'seo_keywords', $column);
        
        $column = [
            'type'     => Table::TYPE_TEXT,
            'length'   => 254,
            'nullable' => false,
            'comment'  => 'Category Path',
            'default'  => '',
            'after'    => 'description'
        ];
        $connection->addColumn($setup->getTable('magentoyo_articles_category'), 'category_path', $column);
        
        $column = [
            'type' => Table::TYPE_SMALLINT,
            'nullable' => false,
            'comment' => 'SEO Robots',
            'default' => HelperData::SEO_ROBOTS_INDEX_FOLLOW,
            'after'    => 'seo_keywords'
        ];
        $connection->addColumn($setup->getTable('magentoyo_articles_category'), 'seo_robots', $column);
        
        $column = [
            'type'     => Table::TYPE_TEXT,
            'length'   => 255,
            'nullable' => false,
            'comment'  => 'SEO URL Key',
            'default'  => '',
            'after'    => 'article'
        ];
        $connection->addColumn($setup->getTable('magentoyo_articles_article'), 'seo_url_key', $column);
        
        $column = [
            'type'     => Table::TYPE_TEXT,
            'length'   => 255,
            'nullable' => false,
            'comment'  => 'SEO Title',
            'default'  => '',
            'after'    => 'seo_description'
        ];
        $connection->addColumn($setup->getTable('magentoyo_articles_article'), 'seo_keywords', $column);
        
        $column = [
            'type' => Table::TYPE_SMALLINT,
            'nullable' => false,
            'comment' => 'SEO Robots',
            'default' => HelperData::SEO_ROBOTS_INDEX_FOLLOW,
            'after'    => 'seo_keywords'
        ];
        $connection->addColumn($setup->getTable('magentoyo_articles_article'), 'seo_robots', $column);
        
    }
}