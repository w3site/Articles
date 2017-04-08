<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace WSite\Articles\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * @codeCoverageIgnore
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;

        $installer->startSetup();

        /**
         * Create table 'wsite_articles_category'
         */
        $table = $installer->getConnection()
            ->newTable($installer->getTable('wsite_articles_category'))
            ->addColumn(
                'entity_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Entity ID'
            )
            ->addColumn(
                'parent_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => true],
                'Parent entity ID'
            )
            ->addColumn(
                'title',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                64,
                [],
                'Title'
            )
            ->addColumn(
                'description',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                [],
                'Description'
            )
            ->addColumn(
                'seo_title',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                64,
                [],
                'SEO Title'
            )
            ->addColumn(
                'seo_description',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                160,
                [],
                'SEO Description'
            )
            ->addColumn(
                'sorting',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => false, 'nullable' => false, 'default' => 0],
                'Sorting'
            )
            ->addColumn(
                'status',
                \Magento\Framework\DB\Ddl\Table::TYPE_BOOLEAN,
                null,
                [],
                'Status'
            )
            ->setComment('Categories of Articles');
        $installer->getConnection()->createTable($table);

        /**
         * Create table 'wsite_articles_catalog'
         */
        $table = $installer->getConnection()
            ->newTable($installer->getTable('wsite_articles_catalog'))
            ->addColumn(
                'entity_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Entity ID'
            )
            ->addColumn(
                'title',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                64,
                [],
                'Title'
            )
            ->addColumn(
                'article',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                [],
                'Description'
            )
            ->addColumn(
                'seo_title',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                64,
                [],
                'SEO Title'
            )
            ->addColumn(
                'seo_description',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                160,
                [],
                'SEO Description'
            )
            ->addColumn(
                'sorting',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => false, 'nullable' => false, 'default' => 0],
                'Sorting'
            )
            ->addColumn(
                'status',
                \Magento\Framework\DB\Ddl\Table::TYPE_BOOLEAN,
                null,
                [],
                'Status'
            )
            ->setComment('Articles');
        $installer->getConnection()->createTable($table);

        /**
         * Create table 'wsite_articles_bound'
         */
        $table = $installer->getConnection()
            ->newTable($installer->getTable('wsite_articles_bound'))
            ->addColumn(
                'row_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Article ID'
            )
            ->addColumn(
                'article_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Article ID'
            )
            ->addColumn(
                'category_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Category ID'
            )
            ->addIndex(
                $installer->getIdxName('wsite_articles_bound', ['category_id']),
                ['category_id']
            )
            ->setComment('Bound');
        $installer->getConnection()->createTable($table);
        
        $installer->endSetup();
    }
}
