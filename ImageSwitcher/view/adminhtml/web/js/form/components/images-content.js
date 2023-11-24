/**
 * Orange35 Magento Team
 */
define([
    'ko',
    'Magento_Ui/js/form/components/html',
    'jquery',
    'mage/translate'
], function (ko, Html, $) {
    'use strict';

    return Html.extend({

        /**
         * default variables
         */
        defaults: {
            template: 'Orange35_ImageSwitcher/form/component/images-content',
            productMediaGallery: '',
            productId: '',
            "links": {
                "productId": "${ $.provider }:data.product.current_product_id"
            },
            images: {}
        },

        /**
         * Extends this with defaults and config.
         * Then calls initObservable, iniListenes and extractData methods.
         */
        initialize: function () {
            this._super()
                .getResizedImages()
                .prepareContent();
        },

        /**
         * Prepare and render html content
         */
        prepareContent: function () {
            var that = this;
            var renderedHtml = 'There are no product\'s images detected';
            var renderContent = ko.computed(function () {
                var images = that.images();
                if (Object.keys(images).length) {
                    var html = '' +
                        '<div class="option-box form-list">' +
                        '   <div id="gallery_image_selector">' +
                        '       %images%' +
                        '   </div>' +
                        '</div>';
                    var imagesHtml = '';
                    for (var id in images) {
                        if (images.hasOwnProperty(id)) {
                            imagesHtml += '' +
                                '<div id="gallery_image_' + id + '">' +
                                '   <img src="'+ images[id] +'">' +
                                '</div>';
                        }
                    }
                    renderedHtml = html.replace("%images%", imagesHtml);
                }
                that.content(renderedHtml);
            });
        },

        /**
         * init observable variables
         *
         * @returns {exports}
         */
        initObservable: function () {
            this._super()
                .observe(['images']);
            return this;
        },

        /**
         * Load images via ajax
         *
         * @returns {exports}
         */
        getResizedImages: function () {
            var that = this;
            $.ajax({
                showLoader: false,
                url: this.ajaxUrl,
                data: {
                    'productId' : this.productId
                },
                type: "POST",
                dataType: 'json'
            }).done(function (data) {
                that.images(data);
            });
            return this;
        }
    });
});

