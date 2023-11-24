<?php

namespace Orange35\ImageSwitcher\Ui\DataProvider\Product\Form\Modifier;

use Magento\Ui\Component\Container;
use Magento\Ui\Component\Form\Fieldset;
use Magento\Ui\Component\Form\Field;
use Magento\Ui\Component\Form\Element\Select;
use Magento\Ui\Component\Form\Element\Checkbox;
use Magento\Ui\Component\Form\Element\CheckboxSet;
use Magento\Ui\Component\Form\Element\MultiSelect;
use Magento\Ui\Component\Form\Element\DataType\Number;

/**
 * Class Matches
 *
 * This is the class, which creates custom fieldset "Image Swithcer Orange35" on product page
 * Here we manage product options, images and combinations
 *
 * @package Orange35\ImageSwitcher\Ui\DataProvider\Product\Form\Modifier
 */
class Matches extends \Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\AbstractModifier
{
    /**
     * Container values
     */
    const CONTAINER_HEADER_NAME = 'container_header';

    /**
     * Custom matches values
     */
    const CUSTOM_MATCHES = 'custom_matches';
    const CUSTOM_MATCHES_HEADER = 'custom_matches_header';
    const CUSTOM_MATCHES_TAB_CONTENT = 'custom_matches_content';
    const CUSTOM_MATCHES_LIST = 'custom_matches_list';

    /**
     * Group custom options values
     */
    const GROUP_CUSTOM_OPTIONS = 'custom_options_group';
    const GROUP_CUSTOM_OPTIONS_DROP_DOWN = 'custom_options_drop_down_group';
    const GROUP_CUSTOM_OPTIONS_RADIO = 'custom_options_radio_group';
    const GROUP_CUSTOM_OPTIONS_CHECKBOX = 'custom_options_checkbox_group';
    const GROUP_CUSTOM_OPTIONS_MULTISELECT = 'custom_options_multiselect_group';

    /**
     * Custom options values
     */
    const CUSTOM_OPTIONS_LIST = 'custom_options_list';
    const CUSTOM_OPTIONS_DROP_DOWN = 'custom_options_drop_down';
    const CUSTOM_OPTIONS_CHECKBOX_ALL_VALUES = 'custom_options_checkbox_all_values';

    /**
     * Media gallery images values
     */
    const MEDIA_GALLERY_IMAGES_LIST = 'media_gallery_images_list';
    const MEDIA_GALLERY_IMAGES_HEADER = 'media_gallery_images_header';
    const MEDIA_GALLERY_IMAGES_CONTENT = 'media_gallery_images_content';

    /**
     * Const values
     */
    const FIELD_SORT_ORDER_NAME = 'sort_order';
    const DATA_PRODUCT = 'data.product';
    const CUSTOM_OPTIONS = 'data.product.options';
    const PRODUCT_MEDIA_GALLERY = 'data.product.media_gallery';
    const BUTTON_APPLY = 'button_apply';
    const ERROR_MESSAGE = 'error_message';

    /**
     * @var \Magento\Catalog\Model\Locator\LocatorInterface
     */
    protected $locator; // @codingStandardsIgnoreLine

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $urlBuilder; // @codingStandardsIgnoreLine

    /**
     * @var \Orange35\ImageSwitcher\Helper\Product\Options
     */
    protected $optionsHelper; // @codingStandardsIgnoreLine

    /**
     * @var array
     */
    protected $meta;

    /**
     * Matches constructor.
     * @param \Magento\Catalog\Model\Locator\LocatorInterface $locator
     * @param UrlInterface $urlBuilder
     * @param Options $optionsHelper
     */
    public function __construct(
        \Magento\Catalog\Model\Locator\LocatorInterface $locator,
        \Magento\Framework\UrlInterface $urlBuilder,
        \Orange35\ImageSwitcher\Helper\Product\Options $optionsHelper
    ) {
        $this->locator = $locator;
        $this->urlBuilder = $urlBuilder;
        $this->optionsHelper = $optionsHelper;
    }

    /**
     * Modifying meta
     *
     * @param array $meta
     * @return array
     */
    public function modifyMeta(array $meta)
    {
        $this->meta = $meta;
        $this->createMatchesPanel();
        return $this->meta;
    }

    /**
     * Creating fieldset
     *
     * @return $this
     */
    protected function createMatchesPanel() // @codingStandardsIgnoreLine
    {
        $this->meta = array_replace_recursive(
            $this->meta,
            [
                static::CUSTOM_MATCHES => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'component' => 'Orange35_ImageSwitcher/js/form/components/fieldset',
                                'template' => 'Orange35_ImageSwitcher/form/fieldset',
                                'componentType' => Fieldset::NAME,
                                'label' => __('Image Switcher Custom Combinations'),
                                'collapsible' => true,
                                'sortOrder' => '100',
                            ],
                        ],
                    ],
                    'children' => [
                        static::CUSTOM_MATCHES_HEADER => $this->getPanelHeader(10),
                        static::CUSTOM_MATCHES_TAB_CONTENT => $this->getPanelContent(20)
                    ]
                ]
            ]
        );
        return $this;
    }

    /**
     * Fieldset header
     * Here we define which messages to display
     *
     * @param $sortOrder
     * @return array
     */
    protected function getPanelHeader($sortOrder)  // @codingStandardsIgnoreLine
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'label' => null,
                        'componentType' => Container::NAME,
                        'component' => 'Orange35_ImageSwitcher/js/form/components/header',
                        'template' => 'Orange35_ImageSwitcher/form/component/header',
                        'additionalClasses' => 'admin__fieldset-note',
                        'sortOrder' => $sortOrder,
                        'imports' => [
                            //here to insert only custom options and images
                            'productData' => '${$.provider}' . ':' . static::DATA_PRODUCT,
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * Panel content
     *
     * @param $sortOrder
     * @return array
     */
    protected function getPanelContent($sortOrder) // @codingStandardsIgnoreLine
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        //here we define depending on media gallery and options if we should display content
                        'component' => 'Orange35_ImageSwitcher/js/form/components/panel-content',
                        'template' => 'Orange35_ImageSwitcher/form/component/panel-content',
                        'componentType' => Fieldset::NAME,
                        'label' => null,
                        'sortOrder' => $sortOrder,
                        'opened' => true,
                        'imports' => [
                            'customOptions' => '${$.provider}' . ':' . static::CUSTOM_OPTIONS,
                            'mediaGalleryImages' => '${$.provider}' . ':' . static::PRODUCT_MEDIA_GALLERY,
                        ],
                    ]
                ]
            ],
            'children' => [
                static::CUSTOM_OPTIONS_LIST => $this->getCustomOptionsListConfig(30),
                static::MEDIA_GALLERY_IMAGES_LIST => $this->getMediaGalleryImagesListConfig(40),
                static::CUSTOM_MATCHES_LIST => $this->getCustomMatchesListConfig(50)
            ]
        ];
    }

    /**
     * Get custom options
     *
     * @param $sortOrder
     * @return array
     */
    protected function getCustomOptionsListConfig($sortOrder) // @codingStandardsIgnoreLine
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'componentType' => Fieldset::NAME,
                        'label' => __('Custom Options'),
                        'disabled' => false,
                        'opened' => true,
                        'sortOrder' => $sortOrder,
                    ]
                ]
            ],
            //Here we loop through poduct custom options and define type
            //MAGE-787#comment=92-50182
            //MAGE-787#comment=92-50200
            'children' => $this->getCustomOptionsConfig()
        ];
    }

    /**
     * Render config for each option
     *
     * @return array
     */
    protected function getCustomOptionsConfig() // @codingStandardsIgnoreLine
    {
        $options = $this->locator->getProduct()->getOptions() ?: [];
        $options = $this->optionsHelper->filterCustomOptions($options);
        $config = [];

        foreach ($options as $option) {
            $config[$this->getCustomOptionGroupConfigName($option)]
                = $this->getCustomOptionConfig($option);
        }
        return $config;
    }

    /**
     * Render config for option
     *
     * @param \Magento\Catalog\Model\Product\Option $option
     * @return array
     */
    protected function getCustomOptionConfig(\Magento\Catalog\Model\Product\Option $option) // @codingStandardsIgnoreLine
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'component' => 'Orange35_ImageSwitcher/js/form/components/group',
                        'fieldTemplate' => 'Orange35_ImageSwitcher/form/field',
                        'componentType' => Container::NAME,
                        'formElement' => Container::NAME,
                        'breakLine' => false,
                        'optionMatchId' => $this->getOptionMatchId($option),
                        'sortOrder' => $this->getCustomOptionSortOrder($option),
                        //MAGE-794
                        'additionalClasses' => 'orange35_admin__two-fields-equal',
                        'visible' => true,
                        'label' => $this->getCustomOptionStoreTitle($option)
                    ]
                ]
            ],
            'children' => [
                $this->getCustomOptionConfigName($option) => $this->getCustomOptionChildConfig($option),
                $this->getCustomOptionAllValuesName($option) => $this->getCheckboxAllValuesConfig($option)
            ]
        ];
    }

    /**
     * Render images list config
     *
     * @param $sortOrder
     * @return array
     */
    protected function getMediaGalleryImagesListConfig($sortOrder) // @codingStandardsIgnoreLine
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'component' => 'Orange35_ImageSwitcher/js/form/components/fieldset',
                        'template' => 'Orange35_ImageSwitcher/form/fieldset',
                        'componentType' => Fieldset::NAME,
                        'label' => __('Product Images'),
                        'collapsible' => true,
                        'opened' => true
                    ],
                ],
            ],
            'children' => [
                static::MEDIA_GALLERY_IMAGES_HEADER => $this->getMediaGalleryImagesHeaderConfig(10),
                static::MEDIA_GALLERY_IMAGES_CONTENT => $this->getMediaGalleryImagesContentConfig(20),
                static::BUTTON_APPLY => $this->getApplyButtonConfig(30),
                static::ERROR_MESSAGE => $this->getErrorMessageConfig(40)
            ]
        ];
    }

    /**
     * Get config for images content header
     *
     * @param $sortOrder
     * @return array
     */
    protected function getMediaGalleryImagesHeaderConfig($sortOrder) // @codingStandardsIgnoreLine
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'label' => null,
                        'formElement' => Container::NAME,
                        'componentType' => Container::NAME,
                        'template' => 'ui/form/components/complex',
                        'sortOrder' => $sortOrder,
                        'content' => __(
                            'Choose multiple images to have them shown in Image Gallery upon option selection.'
                        ),
                    ],
                ],
            ]
        ];
    }

    /**
     * Get config for images content
     *
     * @param $sortOrder
     * @return array
     */
    protected function getMediaGalleryImagesContentConfig($sortOrder) // @codingStandardsIgnoreLine
    {
        //MAGE-793
        //MAGE-793#comment=92-50279
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'label' => null,
                        'componentType' => Container::NAME,
                        'component' => 'Orange35_ImageSwitcher/js/form/components/images-content',
                        'template' => 'Orange35_ImageSwitcher/form/component/images-content',
                        'sortOrder' => $sortOrder,
                        'ajaxUrl' => $this->urlBuilder->getUrl('im_switcher/product/getResizedImages'),
                        'imports' => [
                            'productId' => '${$.provider}' . ':' . static::DATA_PRODUCT . '.current_product_id',
                        ]
                    ],
                ],
            ]
        ];
    }

    /**
     * Get config for apply button
     *
     * @param $sortOrder
     * @return array
     */
    protected function getApplyButtonConfig($sortOrder) // @codingStandardsIgnoreLine
    {
        //MAGE-797
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'title' => __('Apply'),
                        'formElement' => Container::NAME,
                        'componentType' => Container::NAME,
                        'component' => 'Magento_Ui/js/form/components/button',
                        'sortOrder' => $sortOrder,
                        'actions' => [
                            [
                                'targetName' => 'ns=product_form, index=matches',
                                'actionName' => 'apply',
                            ]
                        ]
                    ]
                ],
            ]
        ];
    }

    protected function getErrorMessageConfig($sortOrder) // @codingStandardsIgnoreLine
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'label' => null,
                        'formElement' => Container::NAME,
                        'componentType' => Container::NAME,
                        'component' => 'Magento_Ui/js/form/components/html',
                        'sortOrder' => $sortOrder,
                        'content' =>
                            '<div id="matches-error-message" class="matches-error-message">'
                            . __("At least one custom option should be matched with an image to create a combination") .
                            '</div>',
                    ],
                ],
            ],
        ];
    }

    /**
     * Get config for matches list
     *
     * @param $sortOrder
     * @return array
     */
    protected function getCustomMatchesListConfig($sortOrder) // @codingStandardsIgnoreLine
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'component' => 'Orange35_ImageSwitcher/js/form/components/fieldset',
                        'template' => 'Orange35_ImageSwitcher/form/fieldset',
                        'componentType' => Fieldset::NAME,
                        'label' => __('Custom Matches'),
                        'collapsible' => true,
                        'opened' => true,
                        'needAdditionalDataToLoad' => true,

                    ],
                ],
            ],
            'children' => [
                'matches' => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'componentType' => Container::NAME,
                                //here we are laoding our custom matches
                                'component' => 'Orange35_ImageSwitcher/js/image-switcher/custom-matches/table',
                                'ajaxUrl' => $this->urlBuilder->getUrl('im_switcher/product/matches'),
                                'additionalClasses' => 'admin__field-wide',
                                'sortOrder' => $sortOrder,
                                'noMatchesMessage' => __('Empty List'),
                                'imports' => [
                                    'productId' => '${$.provider}' . ':' . static::DATA_PRODUCT . '.current_product_id',
                                    'images' => '${$.ns}.${$.ns}.' .
                                        static::CUSTOM_MATCHES .
                                        '.' .
                                        static::CUSTOM_MATCHES_TAB_CONTENT .
                                        '.' .
                                        static::MEDIA_GALLERY_IMAGES_LIST .
                                        '.' .
                                        static::MEDIA_GALLERY_IMAGES_CONTENT .
                                        ':images',
                                    'options' => '${$.provider}:' . static::CUSTOM_OPTIONS
                                ],
                            ],
                        ],
                    ]
                ]
            ]
        ];
    }

    /**
     * Define group name for custom option
     *
     * @param \Magento\Catalog\Model\Product\Option $option
     * @return string
     */
    protected function getCustomOptionGroupConfigName(\Magento\Catalog\Model\Product\Option $option) // @codingStandardsIgnoreLine
    {
        return static::GROUP_CUSTOM_OPTIONS . '_' . strtoupper($option->getType()) . '_' . $option->getOptionId();
    }

    /**
     * Get sort order of custom option
     *
     * @param \Magento\Catalog\Model\Product\Option $option
     * @return int
     */
    protected function getCustomOptionSortOrder(\Magento\Catalog\Model\Product\Option $option) // @codingStandardsIgnoreLine
    {
        return $option->getSortOrder();
    }

    /**
     * Get store title of option
     *
     * @param \Magento\Catalog\Model\Product\Option $option
     * @return string
     */
    protected function getCustomOptionStoreTitle(\Magento\Catalog\Model\Product\Option $option) // @codingStandardsIgnoreLine
    {
        return $option->getStoreTitle() ?: $option->getTitle()?: '';
    }

    /**
     * Define name for custom option
     *
     * @param \Magento\Catalog\Model\Product\Option $option
     * @return string
     */
    protected function getCustomOptionConfigName(\Magento\Catalog\Model\Product\Option $option) // @codingStandardsIgnoreLine
    {
        return strtoupper($option->getType()) . '_' . $option->getOptionId();
    }

    /**
     * Define name for all values of option
     *
     * @param \Magento\Catalog\Model\Product\Option $option
     * @return string
     */
    protected function getCustomOptionAllValuesName(\Magento\Catalog\Model\Product\Option $option) // @codingStandardsIgnoreLine
    {
        return strtoupper($option->getType()) . '_' . $option->getOptionId() . '_' . 'ALL_VALUES';
    }

    /**
     * Get match id of option
     *
     * @param \Magento\Catalog\Model\Product\Option $option
     * @return string
     */
    protected function getOptionMatchId(\Magento\Catalog\Model\Product\Option $option) // @codingStandardsIgnoreLine
    {
        return 'match_option_' . $option->getInitialOptionId();
    }

    /**
     * Getting detailed config for option
     *
     * @param \Magento\Catalog\Model\Product\Option $option
     * @return array
     */
    protected function getCustomOptionChildConfig(\Magento\Catalog\Model\Product\Option $option) // @codingStandardsIgnoreLine
    {
        $formElement = $this->getCustomOptionFormElement($option);
        $component = $this->getCustomOptionComponent($option);
        $elementTmpl = $this->getElementTemplate($option);
        $label = $this->getCustomOptionLabel($option);
        $options = $this->getCustomOptionValuesToArray($option);
        $config = [
            'arguments' => [
                'data' => [
                    'config' => [
                        'componentType' => Field::NAME,
                        'formElement' => $formElement,
                        'component' => $component,
                        'elementTmpl' => $elementTmpl,
                        'label' => $label,
                        'options' => $options,
                        'links' => [
                            'disabled' => '${$.parentName}.' . $this->getCustomOptionAllValuesName($option) . ':checked'
                        ]
                    ]
                ]
            ]
        ];
        if ($option->getType() == "checkbox" || $option->getType() == "radio") {
            $flag = ($option->getType() == "checkbox") ? true : false;
            $ar = [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'multiple' => $flag
                        ]
                    ]
                ]
            ];
            $config = array_merge_recursive($config, $ar);
        }
        return $config;
    }

    /**
     * Get config for all values
     *
     * @param \Magento\Catalog\Model\Product\Option $option
     * @return array
     */
    protected function  getCheckboxAllValuesConfig(\Magento\Catalog\Model\Product\Option $option) // @codingStandardsIgnoreLine
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'dataType' => Number::NAME,
                        'componentType' => Field::NAME,
                        'formElement' => Checkbox::NAME,
                        'elementTmpl' => 'Orange35_ImageSwitcher/form/element/checkbox',
                        'description' => __('ALL VALUES'),
                        'label' => 'ALL VALUES',
                        'option_id' => $option->getInitialOptionId(),
                        'valueMap' => [
                            'true' => '%all%',
                            'false' => '0',
                        ],
                        'ui_class' => '${$.name}',
                        'value' => '0',
                    ]
                ]
            ]
        ];
    }

    /**
     * Define form element for option
     *
     * @param \Magento\Catalog\Model\Product\Option $option
     * @return string
     */
    protected function getCustomOptionFormElement(\Magento\Catalog\Model\Product\Option $option) // @codingStandardsIgnoreLine
    {
        if ($option->getType() == "drop_down") {
            return Select::NAME;
        } elseif ($option->getType() == "checkbox" || $option->getType() == "radio") {
            return CheckboxSet::NAME;
        } else {
            return MultiSelect::NAME;
        }
    }

    /**
     * Define component for option
     *
     * @param \Magento\Catalog\Model\Product\Option $option
     * @return string
     */
    protected function getCustomOptionComponent(\Magento\Catalog\Model\Product\Option $option) // @codingStandardsIgnoreLine
    {
        if ($option->getType() == "drop_down") {
            return 'Orange35_ImageSwitcher/js/form/element/select';
        } elseif ($option->getType() == "checkbox" || $option->getType() == "radio") {
            return 'Orange35_ImageSwitcher/js/form/element/checkbox-set';
        } else {
            return 'Orange35_ImageSwitcher/js/form/element/multiselect';
        }
    }

    /**
     * Define template for option
     *
     * @param \Magento\Catalog\Model\Product\Option $option
     * @return string
     */
    protected function getElementTemplate(\Magento\Catalog\Model\Product\Option $option) // @codingStandardsIgnoreLine
    {
        if ($option->getType() == "drop_down") {
            return 'Orange35_ImageSwitcher/form/element/select';
        } elseif ($option->getType() == "checkbox" || $option->getType() == "radio") {
            return 'Orange35_ImageSwitcher/form/element/checkbox-set';
        } else {
            return 'Orange35_ImageSwitcher/form/element/multiselect';
        }
    }

    /**
     * Get label for custom option
     *
     * @param \Magento\Catalog\Model\Product\Option $option
     * @return string
     */
    protected function getCustomOptionLabel(\Magento\Catalog\Model\Product\Option $option) // @codingStandardsIgnoreLine
    {
        return $option->getStoreTitle() ?: $option->getTitle() ?: '';
    }

    /**
     * Get values of custom option
     *
     * @param \Magento\Catalog\Model\Product\Option $option
     * @return array
     */
    protected function getCustomOptionValuesToArray(\Magento\Catalog\Model\Product\Option $option) // @codingStandardsIgnoreLine
    {
        $valuesConfig = [];
        $values = $option->getValues();
        if (empty($values)) {
            $values = $option['values'];
            if (empty($values)) {
                $values = [];
            }
        }
        if (in_array($option->getType(), ['drop_down', 'radio'])) {
            $valuesConfig[] = [
                'value_id' => $option->getType() . '_value_' . $option->getInitialOptionId() . '_none',
                'label' => "--Not Selected--",
                'value' => 'none'
            ];
        }
        foreach ($values as $value) {
            $valuesConfig[] = [
                'value_id' => $option->getType() .
                    '_value_' .
                    $option->getInitialOptionId() .
                    '_' .
                    $value['initial_value_id'],
                'label' => $value['store_title'] ?: __($value['title']),
                'value' => $value['initial_value_id']
            ];
        }
        return $valuesConfig;
    }

    /**
     * Modify data
     *
     * @param array $data
     * @return array
     */
    public function modifyData(array $data)
    {
        return $data;
    }
}
