/**
 * Orange35 Magento Team
 */
define([
    "jquery",
    "Orange35_ImageSwitcher/js/image-switcher/elements/abstract",
    "Orange35_ImageSwitcher/js/image-switcher/helper/custom-options",
    "underscore",
    "jquery/ui",
], function ($, CustomOptionAbstract, optionsHelper,  _) {
    "use strict";

    $.widget('orange35.customOptionMultiselect', {

        /**
         * define default options
         */
        options: {
            CustomOptionMultiselect: {}
        },

        /**
         * Widget constructor
         *
         * @returns {orange35.customOptionMultiselect}
         * @private
         */
        _create:  function () {
            //declare object for custom option
            this.createCustomOptionMultiselect();
            return this;
        },

        /**
         * Define mltiselect object
         */
        createCustomOptionMultiselect: function () {
            var AbstractOption = CustomOptionAbstract().getOptionConstruct();

            /**
             * Тут використано прийом "функціональне наслідування".
             * AbstractOption.call(this) викликає функцію-конструктор AbstractOption,
             * де у "this" записуються всі методи та атрибути конструктора AbstractOption.
             * @constructor
             */
            var CustomOptionMultiselect = function (optionId, element, values) {
                AbstractOption.call(this, optionId, element, values, true);
                return this;
            };

            optionsHelper().extend(CustomOptionMultiselect, AbstractOption);

            CustomOptionMultiselect.fn = CustomOptionMultiselect.prototype;
            CustomOptionMultiselect.fn.updateView = function () {
                this.root.select('option').each(function (element) {
                    element.selected = false;
                });
                if (this.hasValue()) {
                    var values = this.getValue();
                    for (var i in values) {
                        if (values.hasOwnProperty(i)) {
                            this.root.select('option[value="' + values[i] + '"]').first().selected = true;
                        }
                    }
                }
            };

            CustomOptionMultiselect.fn.updateValue = function () {
                var values = [];
                this.root.select('option').each(function (element) {
                    if (element.selected) {
                        values.push(element.value);
                    }
                });
                if (!values.length) {
                    values = false;
                }
                this.setValue(values, true);
            };

            CustomOptionMultiselect.fn.attachObservers = function () {
                var that = this;
                var element = this.root.select('select').first();
                if (!element) {
                    throw "Invalid HTML markup";
                }
                that.updateValue();
                $(element).on("change", function () {
                    that.updateValue();
                });
            };
            this.option('CustomOptionMultiselect', CustomOptionMultiselect);
        },

        /**
         * Initialize option
         *
         * @param optionId
         * @param element
         * @param values
         */
        initOption: function (optionId, element, values) {
            var Option = this.getOptionConstruct();
            return new Option(optionId, element, values);
        },

        /**
         * Getting multiselect constructor
         *
         * @returns {*}
         */
        getOptionConstruct: function () {
            return this.option('CustomOptionMultiselect');
        }
    });
    return $.orange35.customOptionMultiselect;
});
