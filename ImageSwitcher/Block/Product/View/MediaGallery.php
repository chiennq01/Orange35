<?php

namespace Orange35\ImageSwitcher\Block\Product\View;

use Magento\Catalog\Model\Product\Option;
use Magento\Framework\DataObject;
/**
 * Frontend block
 *
 * Class MediaGallery
 * @package Orange35\ImageSwitcher\Block\Product\View
 */
class MediaGallery extends \Magento\Catalog\Block\Product\AbstractProduct
{
    /**
     * @var \Orange35\ImageSwitcher\Helper\Product\CustomMatches
     */
    protected $matchesHelper; // @codingStandardsIgnoreLine

    /**
     * @var \Orange35\ImageSwitcher\Helper\Product\Image
     */
    protected $imageHelper; // @codingStandardsIgnoreLine

    /**
     * @var \Orange35\ImageSwitcher\Helper\Product\Options
     */
    protected $optionsHelper; // @codingStandardsIgnoreLine

    /**
     * @var \Magento\Catalog\Model\Product\Image\UrlBuilder
     */
    private $imageUrlBuilder;

    /**
     * MediaGallery constructor.
     * @param \Magento\Catalog\Block\Product\Context $context
     * @param \Orange35\ImageSwitcher\Helper\Product\CustomMatches $matchesHelper
     * @param \Orange35\ImageSwitcher\Helper\Product\Image $imageHelper
     * @param \Orange35\ImageSwitcher\Helper\Product\Options $optionsHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Catalog\Model\Product\Image\UrlBuilder $imageUrlBuilder,
        \Orange35\ImageSwitcher\Helper\Product\CustomMatches $matchesHelper,
        \Orange35\ImageSwitcher\Helper\Product\Image $imageHelper,
        \Orange35\ImageSwitcher\Helper\Product\Options $optionsHelper,
        array $data
    ) {
        parent::__construct($context, $data);
        $this->matchesHelper = $matchesHelper;
        $this->imageHelper = $imageHelper;
        $this->optionsHelper = $optionsHelper;
        $this->imageUrlBuilder = $imageUrlBuilder;
    }

    /**
     * @return \Magento\Catalog\Model\Product
     */
    public function getCurrentProduct()
    {
        return $this->getProduct();
    }

    /**
     * Get initial data for js-component
     *
     * @return JSON
     */
    public function getConfig()
    {//exit;
        $product = $this->getCurrentProduct();

        $matches = $this->matchesHelper->getCustomMatchesArrayByProductId($product->getId());
        $preparedMatches = $this->prepareMatches($matches);

        /** \Magento\Catalog\Model\Product $product */
        $productCustomOptions = $this->optionsHelper->getProductCustomOptions($product);
        /** array of \Magento\Catalog\Model\Product\Option $productOptions  */
        $preparedCustomOptions = $this->prepareCustomOptions($productCustomOptions);

        $preparedProductImages = $this->prepareImages($product);

        $config['matches'] = $preparedMatches;
        $config['customOptions'] = $preparedCustomOptions;
        $config['images'] = $preparedProductImages;

        return json_encode($config, JSON_PRETTY_PRINT);
    }

    /**
     * Simplify matches construction
     *
     * @param $matches from $database
     * @return array
     */
    public function prepareMatches($matches)
    {
        $product = $this->getCurrentProduct();
        $preparedMatches = [];
        foreach ($matches as $match) {
            $values = $this->matchesHelper->getOriginalCustomOptionIds($product, $match);
            foreach ($product->getOptions() as $option) {
                if (!$this->optionsHelper->isSupportedOption($option)) {
                    continue;
                }
                if (!array_key_exists($option->getId(), $values)) {
                    $values[$option->getId()] = $this->optionsHelper->isMultiple($option) ? [] : '';
                    continue;
                }
                if ($values[$option->getId()] === '%all%') {
                    unset($values[$option->getId()]);
                    continue;
                }
                if (is_array($values[$option->getId()])) {
                    $values[$option->getId()] = array_map('intval', $values[$option->getId()]);
                } else {
                    $values[$option->getId()] = (int) $values[$option->getId()];
                }
            }
            $preparedMatches[] = [
                'productId' => $match['productId'],
                'imageIds'  => $match['imagesId'],
                'values'    => $values,
            ];
        }
        return $preparedMatches;
    }

    /**
     * Prepare product custom options for frontend
     *
     * @param array $options
     * @return array
     */
    public function prepareCustomOptions($options)
    {
        $filteredOptions = [];

        /** \Magento\Catalog\Model\Product\Option $option */
        foreach ($options as $option) {
            $filteredOptions[$option->getOptionId()] = [
                'type' => $option->getType(),
            ];
        }
        return $filteredOptions;
    }

    public function prepareImages($product)
    {
        $images = $product->getMediaGalleryEntries();
        $imagesItems = [];
        foreach ($images as $image) {
            $smallImageUrl = $this->imageUrlBuilder
                ->getUrl($image->getFile(), 'product_page_image_small');
            $image->setData('small_image_url', $smallImageUrl);

            $mediumImageUrl = $this->imageUrlBuilder
                ->getUrl($image->getFile(), 'product_page_image_medium');
            $image->setData('medium_image_url', $mediumImageUrl);

            $largeImageUrl = $this->imageUrlBuilder
                ->getUrl($image->getFile(), 'product_page_image_large');
            $image->setData('large_image_url', $largeImageUrl);
            $imageItem = new DataObject(
                [
                    'thumb' => $image->getData('small_image_url'),
                    'img' => $image->getData('medium_image_url'),
                    'full' => $image->getData('large_image_url'),
                    'id' => $image->getData('id'),
                    'caption' => ($image->getLabel() ?: $product->getName()),
                    'position' => $image->getData('position'),
                    //'isMain'   => $this->isMainImage($image),
                    'type' => str_replace('external-', '', $image->getMediaType()),
                    'videoUrl' => $image->getVideoUrl(),
                ]
            );
            $imagesItems[] = $imageItem->toArray();
        }
        return $imagesItems;
    }
}
