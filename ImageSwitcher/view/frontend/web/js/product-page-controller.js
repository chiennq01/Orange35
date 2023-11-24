define([
  'jquery',
  'underscore',
  //'mage/gallery/gallery',
  'jquery/ui'
], function ($, _) {
  "use strict";

  /**
   * Controller file for image switcher
   */
  $.widget('orange35.productPageController', {

    /**
     * Define additional default option variables
     */
    options: {
      form: '#product_addtocart_form',
      galleryPlaceholderSelector: '[data-gallery-role=gallery-placeholder]',
      matches: [],
      customOptions: [],
      images: []
    },

    timer: null,
    prevImageIds: '',
    images: undefined,
    defaultImages: [],

    /**
     * Constructor
     *
     * @private
     */
    _create: function () {
      window.orange35 = window.orange35 || {updatingGallery: false};
      this.getGallery().on('gallery:loaded', this.galleryOnLoad.bind(this));
      $('[name^="options["]', this.options.form).on('change', _.bind(this.formOnChange, this));
    },

    galleryOnLoad: function () {
      if(this.options.images.length){
        this.images = this.options.images;
      }
      if (typeof this.images === 'undefined') {
        this.images = this.getGalleryApi().returnCurrentImages();
      }
      if (false === window.orange35.updatingGallery) {
        this.defaultImages = this.getGalleryApi().returnCurrentImages();
      }
      this.getGallery().on('fotorama:ready', this.onFotoramaLoad.bind(this));
      this.update();
    },

    onFotoramaLoad: function (event) {
      if (false === this.updating) {
        this.defaultImages = this.getGalleryApi().returnCurrentImages();
      }
    },

    /**
     * Delay workaround for compatibility with 3-rd party modules which changes form values frequently
     */
    formOnChange: function () {
      if (this.timer) {
        clearTimeout(this.timer);
      }
      this.timer = setTimeout(this.update.bind(this), 10);
    },

    update: function () {
      this.changeProductGallery(this.getMatchedImages(this.getSelectedOptions()));
    },

    getSelectedOptions: function () {
      var self = this,
        options = {},
        optionId,
        valueId;

      /*
        set empty values by default to all supported custom options
        since serializeArray doesn't return value for not selected radio/checkbox
        as result "-Not Selected-" feature doesn't work
      */
      for (optionId in this.options.customOptions) {
        var option = this.options.customOptions[optionId];
        options[optionId] = (option.type === 'checkbox' || option.type === 'multiple') ? [] : '';
      }

      _.each($(self.options.form).serializeArray(), function (element) {
        if (element.name.startsWith("options")) {
          optionId = element.name.replace(/\D/g, '');
          self.setSelectedOption(options, optionId, element.value);
        }
      });

      return options;
    },

    /**
     * Set option value pair
     *
     */
    setSelectedOption: function (options, optionId, value) {
      value = parseInt(value, 10) || '';
      if ("undefined" === typeof this.options.customOptions[optionId]) {
        return false;
      } else if (this.options.customOptions.hasOwnProperty(optionId)) {
        if (this.options.customOptions[optionId].type === "drop_down" || this.options.customOptions[optionId].type === "radio") {
          options[optionId] = value;
          return true;
        } else if (this.options.customOptions[optionId].type === "checkbox" || this.options.customOptions[optionId].type === "multiple") {
          if ("undefined" === typeof options[optionId]) {
            var values = [];
            values.push(value);
            options[optionId] = values;
          } else {
            options[optionId].push(value);
          }
          return true;
        }
        return false;
      }
      return false;
    },

    getMatchedImages: function (selectedOptions) {
      if (_.isEmpty(selectedOptions)) {
        return [];
      }
      for (var i = 0; i < this.options.matches.length; i++) {
        if (this.isMatch(selectedOptions, this.options.matches[i].values)) {
          return this.options.matches[i].imageIds;
        }
      }
      return [];
    },

    isMatch: function (object, properties) {
      var property, propertyValue, objectValue, diff;
      for (property in properties) {
        if (!properties.hasOwnProperty(property)) {
          continue;
        }
        propertyValue = properties[property];
        if (!object.hasOwnProperty(property)) {
          return false;
        }
        objectValue = object[property];
        if (_.isArray(propertyValue) !== _.isArray(objectValue)) {
          // type mismatch
          return false;
        }
        if (_.isArray(propertyValue)) {
          diff = _.difference(propertyValue, objectValue);
          if (diff.length !== 0) {
            return false;
          }
          diff = _.difference(objectValue, propertyValue);
          if (diff.length !== 0) {
            return false;
          }
          continue;
        }
        if (propertyValue !== objectValue) {
          return false;
        }
      }
      return true;
    },

    changeProductGallery: function (imagesIds) {
      var currentImageIds = imagesIds.sort().join('-');
      if (this.prevImageIds === currentImageIds) {
        return this;
      }
      this.prevImageIds = currentImageIds;
      if (!imagesIds.length) {
        this.updateGalleryData(this.defaultImages);
      } else {
        //imagesIds = _.map(imagesIds, function(id) {return parseInt(id, 10)});
        var imagesToDisplay = _.filter(this.images, function(image) {
          return ~imagesIds.indexOf(image.id);
        });
        this.updateGalleryData(imagesToDisplay);
      }
    },

    getGallery: function () {
      return $(this.options.galleryPlaceholderSelector);
    },

    getGalleryApi: function () {
      return this.getGallery().data('gallery');
    },

    updateGalleryData: function(data) {
      var api = this.getGalleryApi();
      window.orange35.updatingGallery = true;
      api.updateData(data.slice(0));
      api.first();
      window.orange35.updatingGallery = false;
    }
  });

  return $.orange35.productPageController;
});
