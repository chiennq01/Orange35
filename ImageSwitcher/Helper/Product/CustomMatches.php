<?php

namespace Orange35\ImageSwitcher\Helper\Product;

class CustomMatches extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var null|\Orange35\ImageSwitcher\Model\MatchesFactory
     */
    protected $matchesFactory = null; // @codingStandardsIgnoreLine

    /**
     * @var null|Options
     */
    protected $optionsHelper = null; // @codingStandardsIgnoreLine

    /**
     * @var null
     */
    protected $product = null; // @codingStandardsIgnoreLine

    /**
     * @var \Magento\Catalog\Model\ProductRepository|null
     */
    protected $productRepository = null; // @codingStandardsIgnoreLine

    /**
     * CustomMatches constructor.
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Orange35\ImageSwitcher\Model\MatchesFactory $matchesFactory
     * @param Options $optionsHelper
     * @param \Magento\Catalog\Model\ProductRepository $productRepository
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Orange35\ImageSwitcher\Model\MatchesFactory $matchesFactory,
        \Orange35\ImageSwitcher\Helper\Product\Options $optionsHelper,
        \Magento\Catalog\Model\ProductRepository $productRepository
    ) {
        $this->matchesFactory = $matchesFactory;
        $this->optionsHelper = $optionsHelper;
        $this->productRepository = $productRepository;
        parent::__construct($context);
    }

    /**
     * @param $id
     * @return $this
     */
    public function getCustomMatchesByProductId($id)
    {
        $this->product = $this->getProductById($id);
        $id = (int)$id;
        $matches = $this->matchesFactory->create()->getCollection()
            ->addFieldToFilter('product_id', ['eq' => $id]);
        return $matches;
    }

    /**
     *
     * @param $id
     * @return array
     */
    public function getCustomMatchesArrayByProductId($id)
    {
        $this->product = $this->getProductById($id);
        $id = (int)$id;
        $preparedMatches = [];
        /* Orange35/ImageSwitcher/Model/Matches/Collection $matches */
        $matches = $this->matchesFactory->create()->getCollection()
            ->addFieldToFilter('product_id', ['eq' => $id]);
        if (!empty($matches)) {
            foreach ($matches as $match) {
                $match = $this->getMatchDataToArray($match);
                $preparedMatches[] = $this->formPreparedMatch($match);
            }
        }
        //here problem
        $preparedMatches = $this->getFilteredMatches($this->product, $preparedMatches);
        return $preparedMatches;
    }

    /**
     * @param \Orange35\ImageSwitcher\Model\Matches $match
     * @return mixed
     */
    public function getMatchDataToArray(\Orange35\ImageSwitcher\Model\Matches $match)
    {
        return $match->getData();
    }

    /**
     * @param $match
     * @return array
     */
    public function formPreparedMatch($match)
    {
        return [
            'matchId' => $match['match_id'],
            'productId' => $match['product_id'],
            'imageId'   => $match['image_id'],
            'imagesId'  => explode(",", $match['images_id']),
            'values'    => json_decode($match['matches'], true)
        ];
    }

    /**
     * @param $id
     * @return \Magento\Catalog\Api\Data\ProductInterface|mixed
     */
    public function getProductById($id)
    {
        $id = (is_string($id)) ? (int)$id : $id;
        return $this->productRepository->getById($id);
    }

    /**
     * @param \Magento\Catalog\Model\Product $product
     * @param $preparedMatches
     * @return array
     */
    public function getFilteredMatches(\Magento\Catalog\Model\Product $product, $preparedMatches)
    {
        return $this->filterMatches($product, $preparedMatches);
    }

    /**
     * @param \Magento\Catalog\Model\Product $product
     * @param $dirtyMatches
     * @return array
     */
    public function filterMatches(\Magento\Catalog\Model\Product $product, $dirtyMatches)  // @codingStandardsIgnoreLine
    {
        $matches = [];
        $existsImages = [];
        $galleryImages = ($product->getMediaGallery("images")) ? $product->getMediaGallery("images") : [];
        $optionsInfo = $this->optionsHelper->getOptionsInitialOptionIdPrimary($product);
        foreach ($galleryImages as $image) {
            $existsImages[] = $image['value_id'];
        }
        foreach ($dirtyMatches as $match) {
            if (!is_array($match)) {
                continue;
            }
            if (!isset($match['productId']) || $product->getId() != (int)$match['productId']) {
                continue;
            }
            if (!isset($match['imageId']) || !in_array($match['imageId'], $existsImages)) {
                continue;
            }
            if (!isset($match['values']) || !is_array($match['values'])) {
                continue;
            }
            foreach ($match['imagesId'] as $imageId) {
                if (!in_array($imageId, $existsImages)) {
                    continue 2;
                }
            }
            foreach ($match['values'] as $optionId => $values) {
                if (!isset($optionsInfo[$optionId])) {
                    continue 2;
                }
                $info = $optionsInfo[$optionId];
                if ('drop_down' == $info['type'] || 'radio' == $info['type']) {
                    if ($values != "%all%") {
                        if ((int)$values != $values || !in_array($values, $info['values'])) {
                            continue 2;
                        }
                    }
                } elseif ('multiple' == $info['type'] || 'checkbox' == $info['type']) {
                    if ($values != "%all%") {
                        if (!is_array($values)) {
                            continue 2;
                        }
                        foreach ($values as $value) {
                            if (!in_array($value, $info['values'])) {
                                continue 3;
                            }
                        }
                    }
                } else {
                    continue;
                }
            }
            $matches[] = $match;
        }
        return $matches;
    }

    /**
     * @param $product
     * @param $match
     * @return array
     */
    public function getOriginalCustomOptionIds($product, $match)  // @codingStandardsIgnoreLine
    {
        $filteredOptions = [];
        $matchesOptions = $match['values'];
        foreach ($product->getOptions() as $option) {
            foreach ($matchesOptions as $customOptionKey => $customOptionValue) {
                if ($customOptionKey == (int)$option->getInitialOptionId()) {
                    if ($customOptionValue == '%all%') {
                        $filteredOptions[$option->getId()] = $customOptionValue;
                    } elseif (is_array($customOptionValue)) {
                        $multiselectValueArray = [];
                        $values = $option->getValues();
                        foreach ($values as $value) {
                            foreach ($customOptionValue as $customValue) {
                                if ($customValue == $value->getInitialValueId()) {
                                    array_push($multiselectValueArray, (int)$value->getId());
                                }
                            }
                        }
                        $filteredOptions[$option->getId()] = $multiselectValueArray;
                    } else {
                        $values = $option->getValues();
                        foreach ($values as $value) {
                            if ($customOptionValue == $value->getInitialValueId()) {
                                $filteredOptions[$option->getId()] = (int)$value->getId();
                            }
                        }
                    }
                }
            }
        }
        return $filteredOptions;
    }

    /**
     * @param \Magento\Catalog\Model\Product $product
     * @param $matches
     * @return $this
     */
    public function saveMatches(\Magento\Catalog\Model\Product $product, $matches)
    {
        if ($existsMatches = $this->getCustomMatchesByProductId($product->getId())) {
            $existsMatches->walk('delete');
        }
        if (!is_array($matches)) {
            return $this;
        }
        foreach ($matches as $match) {
            $match = json_decode($match, true);
            if ($product->getId() == $match['productId']) {
                $this->saveMatch($match);
            }
        }
        return $this;
    }

    /**
     * @param $match
     */
    public function saveMatch($match)
    {
        $matchObj = $this->matchesFactory->create();
        $matchObj->setData([
            'product_id' => $match['productId'],
            'matches' => json_encode($match['values']),
            'image_id' => $match['imageId'],
            'images_id' => implode(',', $match['imagesId']),
        ]);
        $matchObj->save();
    }
}
