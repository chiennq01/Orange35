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

    $.widget('orange35.customOptionDropDown', {

        /**
         * define options
         */
        options: {
            CustomOptionDropDown: {}
        },

        /**
         * Widget constructor
         *
         * @returns {orange35.customOptionDropDown}
         * @private
         */
        _create:  function () {
            this.createCustomOptionDropDown();
            return this;
        },

        /**
         * Define DropDown Object class
         */
        createCustomOptionDropDown: function () {
            var AbstractOption = CustomOptionAbstract().getOptionConstruct();

            /**
             * Тут використано прийом "функціональне наслідування".
             * AbstractOption.call(this) викликає функцію-конструктор AbstractOption,
             * де у "this" записуються всі методи та атрибути конструктора AbstractOption.
             * @constructor
             */
            var CustomOptionDropDown = function (optionId, element, values) {
                AbstractOption.call(this, optionId, element, values, false);
                return this;
            };

            optionsHelper().extend(CustomOptionDropDown, AbstractOption);

            CustomOptionDropDown.fn = CustomOptionDropDown.prototype;

            CustomOptionDropDown.fn.updateView = function () {
                this.root.select('select').first().value = this.hasValue() ? this.getValue() : 'none';
            };

            CustomOptionDropDown.fn.attachObservers = function () {
                var that = this;
                var element = this.root.select('select').first();
                if (!element) {
                    throw 'Invalid HTML markup';
                } else {
                    //check if some value of element is already selected before initializing options
                    if (element.value != 'none') {
                        this.setValue(parseInt(element.value));
                    }
                }
                $(element).on("change", function () {
                    that.setValue(parseInt(this.value));
                });
            };
            this.option('CustomOptionDropDown', CustomOptionDropDown);
        },

        /**
         * Initialize option with html and values
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
         * Get option constructor
         *
         * @returns {*}
         */
        getOptionConstruct: function () {
            return this.option('CustomOptionDropDown');
        }
    });
    return $.orange35.customOptionDropDown;
});
