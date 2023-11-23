<?php

namespace Orange35\ImageSwitcher\Observer;

/**
 * Observer to save product data
 *
 * Class ProductSave
 * @package Orange35\ImageSwitcher\Observer
 */
class ProductSave implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request; // @codingStandardsIgnoreLine

    /**
     * @var \Orange35\ImageSwitcher\Helper\Product\CustomMatches
     */
    protected $matchesHelper; // @codingStandardsIgnoreLine

    /**
     * @var \Orange35\ImageSwitcher\Helper\Product\Options
     */
    protected $optionsHelper; // @codingStandardsIgnoreLine

    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $productFactory; // @codingStandardsIgnoreLine

    /**
     * ProductSave constructor.
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Orange35\ImageSwitcher\Helper\Product\CustomMatches $matchesHelper
     * @param \Orange35\ImageSwitcher\Helper\Product\Options $optionsHelper
     */
    public function __construct(
        \Magento\Framework\App\RequestInterface $request,
        \Orange35\ImageSwitcher\Helper\Product\CustomMatches $matchesHelper,
        \Orange35\ImageSwitcher\Helper\Product\Options $optionsHelper,
        \Magento\Catalog\Model\ProductFactory $productFactory
    ) {
        $this->request = $request;
        $this->matchesHelper = $matchesHelper;
        $this->optionsHelper = $optionsHelper;
        $this->productFactory = $productFactory;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $product = $observer->getEvent()->getProduct();
        $this->optionsHelper->saveProductOptionsWithInitialIds($product);
        $matches = $this->request->getParam("custom_options_matches", []);
        if ($matches) {
            /** json encoded string */
            $this->matchesHelper->saveMatches($product, $matches);
        }
        return $this;
    }
}
