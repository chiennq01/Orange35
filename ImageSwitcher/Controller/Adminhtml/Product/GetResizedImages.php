<?php

namespace Orange35\ImageSwitcher\Controller\Adminhtml\Product;

/**
 * Class GetResizedImages
 * @package Orange35\ImageSwitcher\Controller\Adminhtml\Product
 * @method \Magento\Framework\App\Request\Http getRequest()
 */
class GetResizedImages extends \Magento\Backend\App\Action
{
    /**
     * @var null|\Orange35\ImageSwitcher\Helper\Product\Image
     */
    protected $imageHelper = null;

    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory|null
     */
    protected $resultJsonFactory = null;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Orange35\ImageSwitcher\Helper\Product\Image $_imageHelper,
        \Magento\Framework\Controller\Result\JsonFactory $_resultJsonFactory
    ) {
        $this->imageHelper = $_imageHelper;
        $this->resultJsonFactory = $_resultJsonFactory;
        parent::__construct($context);
    }

    public function execute()
    {
        $images = [];
        if ($this->getRequest()->isAjax()) {
            $productId = $this->getRequest()->getPostValue('productId');
            $images = $this->imageHelper->setProductById($productId)->getImagesWithSize('150x150', true);
        }
        return $this->resultJsonFactory->create()->setData($images);
    }
}
