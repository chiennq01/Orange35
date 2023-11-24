<?php

namespace Orange35\ImageSwitcher\Setup;

/**
 * Installing and creating additional fields and table
 *
 * Class InstallSchema
 * @package Orange35\ImageSwitcher\Setup
 */
class InstallSchema implements \Magento\Framework\Setup\InstallSchemaInterface // @codingStandardsIgnoreLine
{
    /**
     * @param \Magento\Framework\Setup\SchemaSetupInterface $setup
     * @param \Magento\Framework\Setup\ModuleContextInterface $context
     * @throws \Zend_Db_Exception
     */
    public function install( // @codingStandardsIgnoreLine
        \Magento\Framework\Setup\SchemaSetupInterface $setup,
        \Magento\Framework\Setup\ModuleContextInterface $context
    ) {
        $installer = $setup;
        $installer->startSetup();
        $table = $installer->getConnection()
            ->newTable($installer->getTable('orange35_imageswitcher_matches'))
            ->addColumn(
                'match_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'nullable' => false, 'primary' => true, 'unsigned' => true,],
                'Match ID'
            )->addColumn(
                'product_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false,],
                'Product Entity ID'
            )->addColumn(
                'matches',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                ['nullable' => false,],
                'Matches JSON Description'
            )->addColumn(
                'image_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['nullable' => false,],
                'Product Gallery Image ID'
            )->addColumn(
                'images_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                ['nullable' => false,],
                'Product Gallery Images ID'
            )->setComment('Table with custom matches');
        $installer->getConnection()
            ->createTable($table);

        $installer->getConnection()->addColumn(
            $installer->getTable('catalog_product_option'),
            'initial_option_id',
            [
                'type'     => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                'length'   => 255,
                'nullable' => true,
                'comment'  => 'Custom id for orange35_imageswitcher_matches table'
            ]
        );

        $installer->getConnection()->addColumn(
            $installer->getTable('catalog_product_option_type_value'),
            'initial_value_id',
            [
                'type'     => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                'length'   => 255,
                'nullable' => true,
                'comment'  => 'Custom id for orange35_imageswitcher_matches table'
            ]
        );
        $installer->endSetup();
    }
}
