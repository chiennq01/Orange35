'use strict';

define([
  'uiClass',
  'underscore',
  'jquery',
  'jquery/colorpicker/js/colorpicker'
], function (Class, _, $) {

  return Class.extend({
    BW_THRESHOLD: 125,

    initialize: function (options, element) {
      this._super();
      this.element = $(element);
      this.element
        .on('click', this.onClick.bind(this))
        .on('change', this.update.bind(this))
        .on('keyup', _.debounce(this.update.bind(this), 500));
      this.update();
    },

    update: function () {
      var color = this.element.val();
      this.element.css('backgroundColor', color);
      this.element.css('color', (this.getLuminance() > this.BW_THRESHOLD) ? 'black' : 'white');
    },

    getLuminance: function () {
      var color = this.getColorAsArray();
      return Math.round(((color[0] * 299) + (color[1] * 587) + (color[2] * 114)) / 1000);
    },

    getColorAsArray: function () {
      var rgb = this.element.css('background-color');
      var color = rgb.match(/^rgb\((\d+),\s*(\d+),\s*(\d+)\)$/);
      return [color[1], color[2], color[3]];
    },

    getHexColor: function () {
      var hex = '#', i, h, color = this.getColorAsArray();
      for (i = 0; i < color.length; i++) {
        h = Number(color[i]).toString(16);
        hex += (h.length === 1 ? '0' : '') + h;
      }
      return hex;
    },

    onClick: function () {
      this.element.ColorPicker({
        onShow: (function () {
          this.element.ColorPickerSetColor(this.getHexColor());
        }).bind(this),
        onSubmit: (function (hsb, hex) {
          this.element.ColorPickerHide().val('#' + hex).trigger('change');
        }).bind(this)
      }).ColorPickerShow();
    }
  });
});
