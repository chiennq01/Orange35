/**
 * Orange35 Magento Team
 */
define([
    "jquery",
    'Magento_Ui/js/modal/confirm',
    "jquery/ui"
], function ($, confirmation) {
    "use strict";

    $.widget('orange35.imagesManager', {

        /**
         * define default options
         */
        options: {
            ImagesManager: {}
        },

        /**
         * Widget constructor
         *
         * @private
         */
        _create: function () {
            this.createImagesManager();
        },

        /**
         * Define images manager class
         */
        createImagesManager: function () {
            var ImagesManager = function (galleryRoot) {
                if (!galleryRoot || !galleryRoot.select) {
                    throw 'Invalid argument given';
                }
                this.imageId = false;
                this.imagesId = [];
                this.root = galleryRoot;
                this.images = [];
                this.init();
                return this;
            };
            ImagesManager.fn = ImagesManager.prototype;
            ImagesManager.fn.init = function () {
                var that = this;
                var images = [];
                var elementIdPrefixLength = ('gallery_image_').length;
                this.root.children().each(function (index, el) {
                    var elementId = el.id;
                    var imageId = parseInt(elementId.substr(elementIdPrefixLength));
                    if (!imageId) {
                        throw 'Invalid element id';
                    }
                    images.push(imageId);
                    $(el).on('click', function () {
                        that.setImageId(imageId, true);
                    });
                });
                if (!images.length) {
                    throw 'Images not found';
                }
                this.images = images;
            };
            ImagesManager.fn.getImageId = function () {
                return this.imageId;
            };
            ImagesManager.fn.getImagesId = function () {
                return this.imagesId;
            };
            ImagesManager.fn.getImagesList = function () {
                return this.images;
            };

            ImagesManager.fn.hasImageId = function () {
                return false !== this.imageId;
            };
            ImagesManager.fn.hasImagesId = function () {
                if (!this.imagesId.length) {
                    return false;
                } else {
                    return true;
                }
            };
            ImagesManager.fn.setImageId = function (imageId, updateView) {
                if (!imageId) {
                    imageId = false;
                } else {
                    imageId = parseInt(imageId);
                    var images = this.getImagesList();
                    var valid = false;
                    for (var i in images) {
                        if (images.hasOwnProperty(i)) {
                            if (imageId == images[i]) {
                                valid = true;
                                break;
                            }
                        }
                    }
                    if (!valid) {
                        throw 'Invalid image id';
                    }
                }
                if (this.imagesId.indexOf(imageId) >= 0) {
                    this.imagesId.splice(this.imagesId.indexOf(imageId), 1);
                } else {
                    this.imagesId.push(imageId);
                }
                this.imageId = imageId;
                if (true === updateView) {
                    this.updateView();
                }
                return this;
            };

            ImagesManager.fn.updateView = function () {
                this.root.children().each(function (index, el) {
                    $(el).removeClass("selected");
                });
                if (this.imagesId.length) {
                    this.imagesId.each(function (image_id) {
                        $('#gallery_image_' + image_id).addClass("selected");
                    });
                }
                return this;
            };
            this.option('ImagesManager', ImagesManager);
        },

        /**
         * Initialize image manager
         *
         * @param galleryRoot
         * @returns {*}
         */
        initObject: function (galleryRoot) {
            var Constr = this.option('ImagesManager');
            return new Constr(galleryRoot);
        }

    });
    return $.orange35.imagesManager;
});