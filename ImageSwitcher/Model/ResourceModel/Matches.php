<?php

namespace Orange35\ImageSwitcher\Model\ResourceModel;

/**
 * Class Matches
 * @package Orange35\ImageSwitcher\Model\ResourceModel
 * @author dmoisej Orange35 Magento Team
 */
class Matches extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Constructor
     */
    protected function _construct() // @codingStandardsIgnoreLine
    {
        $this->_init('orange35_imageswitcher_matches', 'match_id');
    }
}
