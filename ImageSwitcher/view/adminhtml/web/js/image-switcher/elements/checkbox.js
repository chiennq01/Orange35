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

    $.widget('orange35.customOptionRadio', {

        /**
         * Set options
         */
        options: {
            CustomOptionCheckbox: {}
        },

        /**
         * Constructor for widget
         *
         * @returns {orange35.customOptionRadio}
         * @private
         */
        _create:  function () {
            this.CustomOptionCheckbox();
            return this;
        },

        /**
         * Define checkbox option object
         *
         * @constructor
         */
        CustomOptionCheckbox: function () {
            var AbstractOption = CustomOptionAbstract().getOptionConstruct();

            /**
             * Тут використано прийом "функціональне наслідування".
             * AbstractOption.call(this) викликає функцію-конструктор AbstractOption,
             * де у "this" записуються всі методи та атрибути конструктора AbstractOption.
             * @constructor
             */
            var CustomOptionCheckbox = function (optionId, element, values) {
                AbstractOption.call(this, optionId, element, values, true);
                return this;
            };

            //inheritancefrom base class
            optionsHelper().extend(CustomOptionCheckbox, AbstractOption);

            CustomOptionCheckbox.fn = CustomOptionCheckbox.prototype;

            //update view
            CustomOptionCheckbox.fn.updateView = function () {
                this.root.select('input[type="checkbox"]').each(function (el) {
                    el.checked = false;
                });
                if (this.hasValue()) {
                    var values = this.getValue();
                    for (var i in values) {
                        if (values.hasOwnProperty(i)) {
                            this.root.select('input[type="checkbox"][value="' + values[i] + '"]').first().checked = true;
                        }
                    }
                }
            };

            //update value
            CustomOptionCheckbox.fn.updateValue = function () {
                var values = [];
                this.root.select('input[type="checkbox"]').each(function (element) {
                    if (element.checked) {
                        values.push(element.value);
                    }
                });
                if (!values.length) {
                    values = false;
                }
                this.setValue(values, true);
            };

            //attach observers
            CustomOptionCheckbox.fn.attachObservers = function () {
                var checkboxesCount = 0;
                var that = this;
                this.root.select('input[type="checkbox"]').each(function (element) {
                    checkboxesCount++;
                    that.updateValue();
                    $(element).on("click", function () {
                        that.updateValue();
                    });
                });
                /**
                 * we set -1 here, because of "%all%" value
                 * не знаю, чи це взагалі це тут потрібно
                 */
                if (checkboxesCount !== this.valuesList.length-1) {
                    throw 'Invalid HTML markup';
                }
            };

            this.option('CustomOptionCheckbox', CustomOptionCheckbox);
        },

        //initialize options
        initOption: function (optionId, element, values) {
            var Option = this.getOptionConstruct();
            return new Option(optionId, element, values);
        },

        //get checkbox construct
        getOptionConstruct: function () {
            return this.option('CustomOptionCheckbox');
        }
    });
    return $.orange35.customOptionRadio;
});
