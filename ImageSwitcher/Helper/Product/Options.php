<?php

namespace Orange35\ImageSwitcher\Helper\Product;
use Magento\Catalog\Model\Product\Option;
use Magento\Catalog\Model\Product\OptionFactory;
use Magento\Catalog\Model\ProductFactory;
use Magento\Framework\App\Helper\Context;

/**
 * Options Helper
 *
 * Helper, which is used to manage different product options
 * (custom options, configurable options, etc...) like
 * retrieving them, changing and other actions
 *
 * Class Orange35\ImageSwitcher\Helper\Options
 * @package Orange35\ImageSwitcher\Helper
 * @author Orange35 Magento Team
 */
class Options extends \Magento\Framework\App\Helper\AbstractHelper
{
    private $optionFactory;
    private $productFactory;

    public function __construct(
        Context $context,
        OptionFactory $optionFactory,
        ProductFactory $productFactory
    ) {
        parent::__construct($context);

        $this->optionFactory = $optionFactory;
        $this->productFactory = $productFactory;
    }

    /**
     * Get product custom options
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return \Magento\Catalog\Model\ResourceModel\Product\Option\Collection | array
     */
    public function getProductCustomOptions(\Magento\Catalog\Model\Product $product)
    {
        $filteredOptions = [];
        $options = $product->getOptions() ?: [];
        foreach ($options as $option) {
            if ($this->isSupportedOption($option)) {
                $filteredOptions[] = $option;
            }
        }
        return $filteredOptions;
    }

    public function isSupportedOption(Option $option)
    {
        $type = $option->getType();
        return in_array($type, [
            Option::OPTION_TYPE_DROP_DOWN,
            Option::OPTION_TYPE_RADIO,
            Option::OPTION_TYPE_CHECKBOX,
            Option::OPTION_TYPE_MULTIPLE,
        ]);
    }

    public function isMultiple(Option $option)
    {
        $type = $option->getType();
        return in_array($type, [
            Option::OPTION_TYPE_CHECKBOX,
            Option::OPTION_TYPE_MULTIPLE,
        ]);
    }

    /**
     * Check if product has custom options
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return int
     */
    public function hasProductCustomOptions(\Magento\Catalog\Model\Product $product)
    {
        return count($this->getProductCustomOptions($product));
    }

    /**
     * Get product's options
     *
     * Get product's options where custom field "initial_option_id" is primary
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return array
     */
    public function getOptionsInitialOptionIdPrimary(\Magento\Catalog\Model\Product $product)
    {
        $info = [];
        foreach ($this->getProductCustomOptions($product) as $option) {
            $valuesIds = $this->getOptionValuesIds($option);
            if (in_array($option->getType(), ['drop_down', 'radio', 'checkbox', 'multiple'])) {
                $info[$option->getInitialOptionId()] = [
                    'type'      => $option->getType(),
                    'values'    => $valuesIds,
                ];
            }
        }
        return $info;
    }

    /**
     * Get order of options
     *
     * Get order of options where primary field is "initial_option_id"
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return array|null
     */
    public function getOptionsOrderInitialOptionIdPrimary(\Magento\Catalog\Model\Product $product)
    {
        $options = $this->getOptionsInitialOptionIdPrimary($product);
        if ($options) {
            return array_keys($options);
        }
        return null;
    }

    /**
     * Get ids of values
     *
     * @param \Magento\Catalog\Model\Product\Option $option
     * @return array
     */
    public function getOptionValuesIds(\Magento\Catalog\Model\Product\Option $option)
    {
        $valuesIds = [];
        $values = $option->getValues() ?: [];
        foreach ($values as $value) {
            array_push($valuesIds, $value->getInitialValueId());
        }
        return $valuesIds;
    }

    /**
     * Get options labels
     *
     * Get options labels where primary fields are "initial_option_id" and "initial_value_id"
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return array
     */
    public function getOptionsLablesInitialOptionIdPrimary(\Magento\Catalog\Model\Product $product)
    {
        $options = [];
        $values = [];
        foreach ($this->getProductCustomOptions($product) as $option) {
            $options[$option->getInitialOptionId()] = $option->getData('title');
            foreach ($option->getValues() ?: [] as $value) {
                $values[$value->getInitialValueId()] = $value->getData('title');
            }
        }
        return compact('options', 'values');
    }

    /**
     * Check and save options 'initial_option_id' and values 'initial_value_id'
     *
     * array $initial_option_id is used to not to duplicate 'initial_option_id' when importing options
     *
     * @param \Magento\Catalog\Model\Product $product
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function saveProductOptionsWithInitialIds(\Magento\Catalog\Model\Product $product)
    {
        try {
            $product = $this->productFactory->create()
                ->load($product->getId());

            $options = $product->getOptions();

            foreach ($options as &$option) {
                if (!$option->getInitialOptionId()) {
                    $option->setInitialOptionId($option->getOptionId());
                } else {
                    if (empty($initial_option_id[$option->getInitialOptionId()])) {
                        $initial_option_id[$option->getInitialOptionId()] = 1;
                    } else {
                        $option->setInitialOptionId($option->getOptionId());
                    }
                }
		
				if (!$option->getPriceType()) {
                    $option->setPriceType('fixed');
                }
				
                $values = $option->getValues() ?: [];

                foreach ($values as &$value) {
                    if (!$value->getInitialValueId()) {
                        $value->setInitialValueId($value->getOptionTypeId());
                    }
					
					if (!$value->getPriceType()) {
                        $value->setPriceType('fixed');
					}
                }
                $option->setValues($values);

                $option->save();
            }
        } catch (\Exception $e) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __($e->getMessage())
            );
        }
    }

    /**
     * Saving product from model into database
     *
     * @param \Magento\Catalog\Model\Product $product]
     */
    public function saveProduct(\Magento\Catalog\Model\Product $product)
    {
        $product->save();
    }

    /**
     * Get only certain type of options
     *
     * @param $options
     * @return array
     */
    public function filterCustomOptions($options)
    {
        $filteredOptions = [];
        /* \Magento\Catalog\Model\Product\Option $option */ // @codingStandardsIgnoreLine
        foreach ($options as $option) {
            if (in_array($option->getType(), ["drop_down", "checkbox", "radio", "multiple"])) {
                $filteredOptions[] = $option;
            }
        }
        return $filteredOptions;
    }
}
