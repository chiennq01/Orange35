/**
 * Orange35 Magento Team
 */
define([
    'Magento_Ui/js/form/element/multiselect'
], function (Multiselect) {
    'use strict';

    return Multiselect.extend({

        /**
         * default variables
         */
        defaults: {
            template: 'Orange35_ImageSwitcher/form/element/multiselect',
            multiple: false,
            multipleScopeValue: null
        }
    });
});
