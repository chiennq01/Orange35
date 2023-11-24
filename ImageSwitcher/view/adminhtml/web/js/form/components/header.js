/**
 * Orange35 Magento Team
 */
define([
    'Magento_Ui/js/form/components/html',
    'uiRegistry',
    'uiLayout',
    'mageUtils',
    'mage/translate'
], function (htmlElement, registry, layout, utils, $t) {
    'use strict';

    return htmlElement.extend({

        /**
         * default variables
         */
        defaults: {
            additionalClasses: {},
            content: '',
            visible: true,
            messages: {},
            productData: {},
            "links": {
                "productData": "${ $.provider }:data.product"
            },
            //DO NOT touch this (html) variable. It lives it's own life
            html: ''
        },

        /**
         * Initializes component.
         *
         * @returns {Object} Chainable.
         */
        initialize: function () {
            return this._super()
                .setMessages()
                .renderContent();
        },

        /**
         * Set messages to display
         */
        setMessages: function () {
            var messagesToSetup = {};
            if (this.isObject(this.messages)) {
                Object.assign(
                    messagesToSetup,
                    this.orange35CoreMessages(),
                    this.messages
                );
            } else {
                messagesToSetup = this.orange35CoreMessages();
            }
            this.messages = messagesToSetup;
            return this;
        },

        /**
         * Render html content
         */
        renderContent: function () {
            var contentToRender = this.initContent();
            this.content(contentToRender);
            return this;
        },

        /**
         * Prepare content to render
         */
        initContent: function () {
            this.initHtmlTemplate()
                .renderMessages();
            return this.html;
        },

        /**
         * Create template for our content
         */
        initHtmlTemplate: function () {
            var html = '' +
                '<div>' +
                '   <div>' +
                '       %messages%' +
                '   </div>' +
                '</div>';
            this.html = html;
            return this;
        },

        /**
         * Put messages into this html template
         */
        renderMessages: function () {
            //array of messages names
            var mss = this.defineMessages();
            var htmlMessages = this.messagesToHtml(mss);
            this.html = this.html.replace("%messages%", htmlMessages);
            return this;
        },

        /**
         * Define messages to display
         */
        defineMessages: function () {
            var mss = [];
            var product = this.productData;
            if (!product.options.length) {
                mss = ["no-custom-options", "no-custom-options-save-info"];
            } else if (product.options.length && !this.hasProductImages()) {
                mss = ["no-product-images"];
            } else {
                mss = ["general-info"];
            }
            //you can use mss.push() to add custom messages keys
            return mss;
        },

        /**
         * Wrap messages with html
         *
         * @param mss
         */
        messagesToHtml: function (mss) {
            var that = this;
            var htmlMessages = '';
            mss.forEach(function (element, index, array) {
                htmlMessages += '<div class="general-info-messages">' + that.messages[element] + '</div>';
            });
            return htmlMessages;
        },

        /**
         * Check if product has images
         */
        hasProductImages: function () {
            var product = this.productData;
            var mediaGallery = product.media_gallery.images;
            var hasImage = 0;
            for (var media in mediaGallery) {
                if (mediaGallery.hasOwnProperty(media)) {
                    if (mediaGallery[media].media_type === "image") {
                        hasImage = 1;
                        break;
                    }
                }
            }
            return (hasImage == 1) ? true : false;
        },

        /**
         * Check if it is object
         *
         * @param obj
         */
        isObject: function (obj) {
            return obj === Object(obj);
        },

        /**
         * Custom core messages to display
         */
        orange35CoreMessages: function () {
            var coreMessages = {
                "no-custom-options": "No Custom Options detected. Please set Custom Options to have Image Combinations management available.",
                "no-custom-options-save-info": "Please save changes to Custom Options to have Image Combinations management available.",
                "no-product-images" : "No product images detected. Please upload product image(s) to have Image Combinations management available.",
                "general-info": "Match Custom Options with a Product Image"
            };
            return coreMessages;
        }
    });
});
