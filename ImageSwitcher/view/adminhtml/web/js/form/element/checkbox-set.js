/**
 * Orange35 Magento Team
 */
define([
    'Magento_Ui/js/form/element/checkbox-set'
], function (CheckboxSet) {
    'use strict';

    return CheckboxSet.extend({

        /**
         * default variables
         */
        defaults: {
            template: 'Orange35_ImageSwitcher/form/element/checkbox-set',
            multiple: false,
            multipleScopeValue: null
        },

        /**
         * Extends instance with defaults. Invokes parent initialize method.
         * Calls initListeners and pushParams methods.
         */
        initialize: function () {
            this._super();
        }
    });
});
