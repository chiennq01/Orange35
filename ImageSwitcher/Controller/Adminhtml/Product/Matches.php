<?php

namespace Orange35\ImageSwitcher\Controller\Adminhtml\Product;

/**
 * Class Matches
 * @package Orange35\ImageSwitcher\Controller\Adminhtml\Product
 * @method \Magento\Framework\App\Request\Http getRequest()
 */
class Matches extends \Magento\Backend\App\Action
{
    /**
     * @var null|\Orange35\ImageSwitcher\Helper\Product\CustomMatches
     */
    protected $matchesHelper = null; // @codingStandardsIgnoreLine

    /**
     * @var null|\Orange35\ImageSwitcher\Helper\Product\Image
     */
    protected $imageHelper = null; // @codingStandardsIgnoreLine

    /**
     * @var null|\Orange35\ImageSwitcher\Helper\Product\Options
     */
    protected $optionsHelper = null; // @codingStandardsIgnoreLine

    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory|null
     */
    protected $resultJsonFactory = null; // @codingStandardsIgnoreLine

    /**
     * Matches constructor.
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Orange35\ImageSwitcher\Helper\Product\Image $_imageHelper
     * @param \Orange35\ImageSwitcher\Helper\Product\CustomMatches $_matchesHelper
     * @param \Orange35\ImageSwitcher\Helper\Product\Options $optionsHelper
     * @param \Magento\Framework\Controller\Result\JsonFactory $_resultJsonFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Orange35\ImageSwitcher\Helper\Product\Image $_imageHelper,
        \Orange35\ImageSwitcher\Helper\Product\CustomMatches $_matchesHelper,
        \Orange35\ImageSwitcher\Helper\Product\Options $optionsHelper,
        \Magento\Framework\Controller\Result\JsonFactory $_resultJsonFactory
    ) {
        $this->imageHelper = $_imageHelper;
        $this->matchesHelper = $_matchesHelper;
        $this->optionsHelper = $optionsHelper;
        $this->resultJsonFactory = $_resultJsonFactory;
        parent::__construct($context);
    }

    public function execute()
    {
        if ($this->getRequest()->isAjax()) {
            $productId = $this->getRequest()->getPostValue('productId');
            $product = $this->matchesHelper->getProductById($productId);
            $matches = $this->matchesHelper->getCustomMatchesArrayByProductId($productId);
            $optionsInfo = $this->optionsHelper->getOptionsInitialOptionIdPrimary($product);
            $optionsLabels = $this->optionsHelper->getOptionsLablesInitialOptionIdPrimary($product);
            $optionsOrder = $this->optionsHelper->getOptionsOrderInitialOptionIdPrimary($product);
            return $this->resultJsonFactory->create()->setData([
                'matches' => $matches,
                'optionsInfo' => $optionsInfo,
                'optionsLabels' => $optionsLabels,
                'optionsOrder' => $optionsOrder
            ]);
        }
        return null;
    }
}
