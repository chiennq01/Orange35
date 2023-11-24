/**
 * Orange35 Magento Team
 */
define([
    'Magento_Ui/js/form/components/fieldset',
    'underscore'
], function (Collapsible, _) {
    'use strict';

    return Collapsible.extend({

        /**
         * default variables
         */
        defaults: {
            hasCustomOptions: false,
            hasMediaGalleryImages: false,
            customOptions: {},
            mediaGalleryImages: {},
            "links": {
                "productData": "${ $.provider }:data.product",
            },
            allowedTypes: {},
        },

        /**
         * Extends instance with defaults. Invokes parent initialize method.
         * Calls initListeners and pushParams methods.
         */
        initialize: function () {
            return this._super()
                .defineAllowedTypes()
                .filterCustomOptions()
                .filterMediaGallery()
                .setRenderConditions();
        },

        /**
         * init observable variables
         *
         * @returns {exports}
         */
        initObservable: function () {
            this._super()
                .observe(['hasCustomOptions', 'hasMediaGalleryImages']);
            return this;
        },

        /**
         * Define types of options which could be displayed in fieldset
         *
         * @returns {exports}
         */
        defineAllowedTypes: function () {
            this.customOptions = this.productData.options;
            this.mediaGalleryImages = this.productData.media_gallery;
            if (this.isObject(this.allowedTypes)) {
                //merge two objects
                Object.assign(
                    this.allowedTypes,
                    this.orange35CoreTypes()
                );
            } else {
                //if parameters in config JSON are set not correctly
                console.log('allowedTypes should be an object');
                this.allowedTypes = this.orange35CoreTypes();
            }
            return this;
        },

        /**
         * Define which default core types to use in ImageSwitcher
         * Only for Orange35 Team
         * inherit or customize method 'defineAllowedTypes' when some additional customization is needed
         *
         * @returns {{drop_down: string, checkbox: string, radio: string, multiple: string}}
         */
        orange35CoreTypes: function () {
            return {
                'drop_down' : 'drop_down',
                'checkbox' : 'checkbox',
                'radio' : 'radio',
                'multiple' : 'multiple'
            };
        },

        /**
         * Filter custom options of product with allowed options' types of our module
         *
         * @returns {exports}
         */
        filterCustomOptions: function () {
            var customOptions = this.customOptions;
            var filteredOptions = {};
            var allowedTypes = this.allowedTypes;
            for (var key in customOptions) {
                if (customOptions.hasOwnProperty(key)) {
                    var optionType = customOptions[key].type;
                    //this could be changed in the future cause 'hasOwnProperty' doesn't work with inheritance
                    if (allowedTypes.hasOwnProperty(optionType)) {
                        filteredOptions[key] = customOptions[key];
                    } else {
                        if (optionType !== undefined) {
                            console.log('Error: ' + optionType + ' is not allowed to use in ImageSwitcher');
                        }
                    }
                }
            }
            this.customOptions = filteredOptions;
            return this;
        },

        /**
         * Getting images for our extension
         *
         * @returns {exports}
         */
        filterMediaGallery: function () {
            var mediaGallery = this.mediaGalleryImages.images;
            var filteredGallery = {};
            for (var key in mediaGallery) {
                if (mediaGallery[key].media_type == "image") {
                    filteredGallery[key] = mediaGallery[key];
                }
            }
            this.mediaGalleryImages = filteredGallery;
            return this;
        },

        /**
         * Set render conditions
         *
         * @returns {exports}
         */
        setRenderConditions: function () {
            var hasCO = (_.isEmpty(this.customOptions)) ? false : true;
            var hasI = (_.isEmpty(this.mediaGalleryImages)) ? false : true;
            this.hasCustomOptions(hasCO);
            this.hasMediaGalleryImages(hasI);
            return this;
        },

        /**
         * Check if element is object
         * @param obj
         * @returns {boolean}
         */
        isObject: function (obj) {
            return obj === Object(obj);
        }
    });
});
