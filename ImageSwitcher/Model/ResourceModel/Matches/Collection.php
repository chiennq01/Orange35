<?php

namespace Orange35\ImageSwitcher\Model\ResourceModel\Matches;

/**
 * Class Collection
 * @package Orange35\ImageSwitcher\Model\ResourceModel\Matches
 * @author dmoisej Orange35 Magento Team
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * Constructor
     */
    protected function _construct() // @codingStandardsIgnoreLine
    {
        $this->_init('Orange35\ImageSwitcher\Model\Matches', 'Orange35\ImageSwitcher\Model\ResourceModel\Matches');
    }
}
