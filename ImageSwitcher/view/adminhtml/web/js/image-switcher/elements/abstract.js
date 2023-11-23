/**
 * Orange35 Magento Team
 */
define([
    "jquery",
    "jquery/ui"
], function ($) {
    "use strict";

    $.widget('orange35.customOptionAbstract', {

        /**
         * define options
         */
        options: {
            CustomOptionAbstract: {}
        },

        /**
         * initialize widget
         *
         * @returns {orange35.customOptionAbstract}
         * @private
         */
        _init: function () {
            return this;
        },

        /**
         * Constructor of widget
         *
         * @returns {orange35.customOptionAbstract}
         * @private
         */
        _create: function () {
            //declaring class
            this.createCustomOptionAbstract();
            return this;
        },

        /**
         * Create base abstrat class for options
         */
        createCustomOptionAbstract: function () {

            //constructor
            var CustomOptionAbstract = function (optionId, element, values, multiple) {
                this.root = {};
                this.multiple = multiple;
                this.id = optionId;
                this.element = element;
                this.valuesList = values;
                this.value = false;
                this.init();
                return this;
            };
            CustomOptionAbstract.fn = CustomOptionAbstract.prototype;

            //init method
            CustomOptionAbstract.fn.init = function () {
                this.valuesList.push("%all%");
                this.root = this.element.select('.input-box').first();
                this.attachObservers();
            };

            //attach observers
            CustomOptionAbstract.fn.attachObservers = function () {
                throw 'Method must be overridden';
            };

            //get value
            CustomOptionAbstract.fn.getValue = function () {
                return this.value;
            };

            //get values
            CustomOptionAbstract.fn.getValuesList = function () {
                return this.valuesList;
            };

            //update views
            CustomOptionAbstract.fn.updateView = function () {
                throw 'Method must be overridden';
            };

            //check if object has value
            CustomOptionAbstract.fn.hasValue = function () {
                return this.value !== false;
            };

            //check if option is multiple
            CustomOptionAbstract.fn.isMultiple = function () {
                return this.multiple;
            };

            //get id
            CustomOptionAbstract.fn.getId = function () {
                return this.id;
            };

            //set value
            CustomOptionAbstract.fn.setValue = function (value, updateView) {
                var acceptValues = this.getValuesList();
                var valid, validValue;
                if (!value) {
                    valid = false;
                } else if (this.isMultiple()) {
                    for (var innerValue in value) {
                        if (value.hasOwnProperty(innerValue)) {
                            valid = false;
                            for (validValue in acceptValues) {
                                if (acceptValues.hasOwnProperty(validValue) && value[innerValue] == acceptValues[validValue]
                                ) {
                                    valid = true;
                                    break;
                                }
                            }
                            if (!valid) {
                                if (value != "%all%") {
                                    throw 'Invalid values list given';
                                }
                            }
                        }
                    }
                } else {
                    valid = false;
                    for (validValue in acceptValues) {
                        if (acceptValues.hasOwnProperty(parseInt(validValue)) && value == acceptValues[validValue]
                        ) {
                            valid = true;
                            break;
                        }
                    }
                    if (!valid) {
                        if (value != "%all%") {
                            console.log("here we end");
                            throw 'Invalid value given';
                        }
                    }
                }
                if (value == "%all%") {
                    $("checkbox_allvalue_"+this.id).checked = true;
                    $$("#select_"+this.id+", [id^='checkbox_value_"+this.id+"'], [id^='radio_value_"+this.id+"']").each(
                        function (e) {
                            if (e.type == "checkbox" || e.type == "radio") {
                                e.parentNode.hide();
                            } else {
                                e.hide();
                            }
                        }
                    );
                } else {
                    $("checkbox_allvalue_"+this.id).checked = false;
                    $$("#select_"+this.id+", [id^='checkbox_value_"+this.id+"'], [id^='radio_value_"+this.id+"']").each(
                        function (e) {
                            if (e.type == "checkbox" || e.type == "radio") {
                                e.parentNode.show();
                            } else {
                                e.show();
                            }
                        }
                    );
                    this.value = value;
                }
                if (true === updateView) {
                    this.updateView();
                }
                return this;
            };
            this.option('CustomOptionAbstract', CustomOptionAbstract);
        },

        /**
         * Initializes option with html content and values
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
         * Retrieving option constructor
         *
         * @returns {*}
         */
        getOptionConstruct: function () {
            return this.option('CustomOptionAbstract');
        }
    });
    return $.orange35.customOptionAbstract;
});