/**
 * Orange35 Magento Team
 */
define([
    'Magento_Ui/js/form/components/fieldset'
], function (Fieldset) {
    'use strict';

    return Fieldset.extend({

        /**
         * default variables
         */
        defaults: {
            template: 'Orange35_ImageSwitcher/form/fieldset',
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