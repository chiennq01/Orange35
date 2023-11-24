<?php

namespace Orange35\ImageSwitcher\Setup;

use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

class InstallData implements InstallDataInterface
{
    /**
     * Database Connection
     * @var ResourceConnection
     */
    private $connection;

    /**
     * InstallData constructor.
     *
     * @param ResourceConnection $connectionResource
     */
    public function __construct(ResourceConnection $connectionResource)
    {
        $this->connection = $connectionResource->getConnection();
    }
    
    /**
     * Installing data for image switcher.
     *
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $sql = 'UPDATE ' . $this->connection->getTableName('catalog_product_option') . ' '
            . 'SET initial_option_id = option_id '
            . 'WHERE initial_option_id IS NULL';
        $this->connection->query($sql);

        $sql = 'UPDATE ' . $this->connection->getTableName('catalog_product_option_type_value') . ' '
            . 'SET initial_value_id = option_type_id '
            . 'WHERE initial_value_id IS NULL';
        $this->connection->query($sql);
    }
}
