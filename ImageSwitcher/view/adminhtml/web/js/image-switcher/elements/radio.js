/**
 * Orang35 Magento Team
 */
define([
    "jquery",
    "Orange35_ImageSwitcher/js/image-switcher/elements/abstract",
    "Orange35_ImageSwitcher/js/image-switcher/helper/custom-options",
    "underscore",
    "jquery/ui",
], function ($, CustomOptionAbstract, optionsHelper,  _) {
    "use strict";

    $.widget('orange35.customOptionRadio', {

        /**
         * define default options
         */
        options: {
            CustomOptionRadio: {}
        },

        /**
         * Widget constructor
         *
         * @returns {orange35.customOptionRadio}
         * @private
         */
        _create:  function () {
            this.createCustomOptionRadio();
            return this;
        },

        /**
         * Define radio option object
         */
        createCustomOptionRadio: function () {
            var AbstractOption = CustomOptionAbstract().getOptionConstruct();

            /**
             * Тут використано прийом "функціональне наслідування".
             * AbstractOption.call(this) викликає функцію-конструктор AbstractOption,
             * де у "this" записуються всі методи та атрибути конструктора AbstractOption.
             * @constructor
             */
            var CustomOptionRadio = function (optionId, element, values) {
                AbstractOption.call(this, optionId, element, values, false);
            };

            optionsHelper().extend(CustomOptionRadio, AbstractOption);

            CustomOptionRadio.fn = CustomOptionRadio.prototype;

            CustomOptionRadio.fn.updateView = function () {
                var currentSelectionId = 'radio_value_' + this.id + '_' + (this.hasValue() ? this.value : 'none'),
                    valueId;
                this.root.select('input[type="radio"]').each(function (element) {
                    valueId = element.readAttribute('value_id');
                    element.checked = valueId === currentSelectionId;
                });
            };

            CustomOptionRadio.fn.attachObservers = function () {
                var radioButtonsCount = 0;
                var that = this;
                this.root.select('input[type="radio"]').each(function (element) {
                    radioButtonsCount++;
                    if (element.checked) {
                        if (element.getValue() != "none") {
                            that.setValue(parseInt(element.getValue()), true);
                        }
                    }
                    $(element).on("click", function () {
                        if (this.getValue() != "none") {
                            that.setValue(parseInt(this.getValue()), true);
                        }
                    });
                });
                if (radioButtonsCount !== this.valuesList.length) {
                    throw "Invalid HTML markup";
                }
            };
            this.option('CustomOptionRadio', CustomOptionRadio);
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
            return this.option('CustomOptionRadio');
        }
    });
    return $.orange35.customOptionRadio;
});
