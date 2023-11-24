<?php

namespace Orange35\ImageSwitcher\Helper\Product;

/**
 * Image helper for managing images
 *
 * Class Image
 * @package Orange35\ImageSwitcher\Helper\Product
 */
class Image extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var null
     */
    protected $width = null; // @codingStandardsIgnoreLine

    /**
     * @var null
     */
    protected $height = null; // @codingStandardsIgnoreLine

    /**
     * @var \Magento\Framework\Data\CollectionFactory|null
     */
    protected $collectionFactory = null; // @codingStandardsIgnoreLine

    /**
     * @var \Magento\Catalog\Model\ProductRepository|null
     */
    protected $productRepository = null; // @codingStandardsIgnoreLine

    /**
     * @var \Magento\Framework\Image\AdapterFactory|null
     */
    protected $imageFactory = null; // @codingStandardsIgnoreLine

    /**
     * @var \Magento\Catalog\Helper\Image|null
     */
    protected $imageHelper = null; // @codingStandardsIgnoreLine

    /**
     * @var null
     */
    protected $product = null; // @codingStandardsIgnoreLine

    /**
     * @var \Magento\Framework\Filesystem|null
     */
    protected $filesystem = null; // @codingStandardsIgnoreLine

    protected $imageCachePath = 'orange35/resized';

    /**
     * @var \Magento\Store\Model\StoreManagerInterface|null
     */
    protected $storeManager = null; // @codingStandardsIgnoreLine

    /**
     * Image constructor.
     * @param Context $context
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\Data\CollectionFactory $collectionFactory,
        \Magento\Catalog\Model\ProductRepository $productRepository,
        \Magento\Framework\Image\AdapterFactory $imageFactory,
        \Magento\Catalog\Helper\Image $imageHelper,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->productRepository = $productRepository;
        $this->imageFactory = $imageFactory;
        $this->filesystem = $filesystem;
        $this->imageHelper = $imageHelper;
        $this->storeManager = $storeManager;
        parent::__construct($context);
    }

    /**
     * Getting images with certain size.
     * Function goes into getImagesWithSizeAjax
     *
     * @param $size
     * @param null $isAjax
     * @return array
     */
    public function getImagesWithSize($size, $isAjax = null)
    {
        if ($isAjax) {
            return $this->getImagesWithSizeAjax($size);
        }
    }

    /**
     * Retrives images with setting size
     *
     * @param $size
     * @return array
     */
    public function getImagesWithSizeAjax($size)
    {
        /* array key => image id; value = image url */
        $resizedImages = $this->setSize($size)->take();
        /* array of arrays */
        return $resizedImages;
    }

    /**
     * Get product full media gallery
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return array
     */
    public function getProductImages(\Magento\Catalog\Model\Product $product)
    {
        $mediaGallery = $product->getMediaGallery('images') ?: [];
        return $mediaGallery;
    }

    /**
     * @param $id
     * @return $this
     */
    public function setProductById($id)
    {
        $this->product = $this->getProductById($id);
        return $this;
    }

    /**
     * @return null
     */
    public function getProduct()
    {
        return $this->product;
    }

    /**
     * @param $id
     * @return mixed
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getProductById($id)
    {
        if (!$id) {
            return null;
        }

        $id = (is_string($id)) ? (int)$id : $id;

        return $this->productRepository->getById($id);
    }

    /**
     * @param $size
     * @return $this
     */
    public function setSize($size)
    {
        $dimensions = explode('x', $size);
        $this->width = $dimensions[0];
        $this->height = $dimensions[1];
        return $this;
    }

    /**
     * @return string
     */
    public function getSize()
    {
        return '' . $this->getWidth() . 'x' . $this->getHeight();
    }

    /**
     * @return null
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * @return null
     */
    public function getHeight()
    {
       return $this->height; // @codingStandardsIgnoreLine
    }

    /**
     * retrieving images from media files of project or creating them if they don't exist
     *
     * @return array
     */
    protected function take() // @codingStandardsIgnoreLine
    {
        $product = $this->getProduct();
        $filteredImages = [];
        if ($product) {
            $images = $product->getMediaGallery('images') ?: [];
            foreach ($images as $image) {
                if ($image['media_type'] == "image") {
                    $filteredImages[$image['value_id']] = $this->getPathToResizedImage($image['file']);
                }
            }
        }
        return $filteredImages;
    }

    /**
     * If somebody wants to override this with plugins or something else
     * It is better to create your own method
     * @param $name
     * @return string|void path to new (resized) image
     */
    protected function getPathToResizedImage($name) // @codingStandardsIgnoreLine
    {
        $imagePath = $this->filesystem
                ->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA)
                ->getAbsolutePath($this->getSizePath()) . $name;
        if (file_exists($imagePath)) { // @codingStandardsIgnoreLine
            return $this->getBaseUrl() . $this->getSizePath() . $name; 
        }
        return $this->resizeImage($name);
    }

    /**
     * Setting custom path for caching images
     *
     * @param $path
     * @return mixed
     */
    public function setImageCachePath($path)
    {
        $this->imageCachePath = $path;
        return $this;
    }

    /**
     * Getting cache path
     *
     * @return null
     */
    public function getImageCachePath()
    {
        return $this->imageCachePath;
    }

    /**
     * Get path to images with certain size
     *
     * @param null $size
     * @return string
     */
    public function getSizePath($size = null)
    {
        if (!$size) {
            return $this->imageCachePath . '/' . $this->getSize();
        }
        return $this->imageCachePath . '/' . $size;
    }

    /**
     * Resize images
     *
     * @param $name
     * @return string
     */
    public function resizeImage($name)
    {
        $width = $this->getWidth();
        $height = $this->getHeight();
        $imagePath = $this->filesystem
                ->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA)
                ->getAbsolutePath($this->getSizePath()) . $name;

        $testPath = $this->filesystem
                ->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA)
                ->getAbsolutePath('catalog/product') . $name;

        $imageResize = $this->imageFactory->create();
        $imageResize->open($testPath);
        $imageResize->backgroundColor([255, 255, 255]);
        $imageResize->constrainOnly(true);
        $imageResize->keepTransparency(true);
        $imageResize->keepFrame(true);
        $imageResize->keepAspectRatio(true);
        $imageResize->resize($width, $height);
        $imageResize->save($imagePath);

        return $this->getBaseUrl() . $this->getSizePath() . $name; // @codingStandardsIgnoreLine
    }

    public function getBaseUrl()
    {
        return $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
    }
}
