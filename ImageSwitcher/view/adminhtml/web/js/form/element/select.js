/**
 * Orange35 Magento Team
 */
define([
    'Magento_Ui/js/form/element/select',
], function (Select) {
    'use strict';

    return Select.extend({

        /**
         * default variables
         */
        defaults: {
            elementTmpl: 'Orange35_ImageSwitcher/form/element/select'
        },

        /**
         * Extends instance with defaults, extends config with formatted values
         *     and options, and invokes initialize method of AbstractElement class.
         *     If instance's 'customEntry' property is set to true, calls 'initInput'
         */
        initialize: function () {
            this._super();
        },
    });
});
