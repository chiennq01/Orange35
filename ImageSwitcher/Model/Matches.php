<?php

namespace Orange35\ImageSwitcher\Model;

/**
 * Class Matches
 * @package Orange35\ImageSwitcher\Model
 * @author dmoisej Orange35 Magento Team
 */
class Matches extends \Magento\Framework\Model\AbstractModel implements \Magento\Framework\DataObject\IdentityInterface
{
    /**
     * Tag for caching data
     */
    const CACHE_TAG = 'orange35_imageswitcher_matches';

    /**
     * Constructor
     */
    protected function _construct() // @codingStandardsIgnoreLine
    {
        $this->_init('Orange35\ImageSwitcher\Model\ResourceModel\Matches');
    }

    /**
     * @return array
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }
}
