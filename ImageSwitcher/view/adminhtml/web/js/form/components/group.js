/**
 * Orange35 Magento Team
 */
define([
    'Magento_Ui/js/form/components/group'
], function (Group) {
    'use strict';

    return Group.extend({

        /**
         * default variables
         */
        defaults: {
            template: 'Orange35_ImageSwitcher/group/group',
            fieldTemplate: 'Orange35_ImageSwitcher/form/field',
        },

        /**
         * Extends this with defaults and config.
         * Then calls initObservable, iniListenes and extractData methods.
         */
        initialize: function () {
            this._super();
        }
    });
});
