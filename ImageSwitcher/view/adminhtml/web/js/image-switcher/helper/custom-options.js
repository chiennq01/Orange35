/**
 * Orange35 Magento Team
 */
define([
    "jquery",
    "underscore",
    "jquery/ui",
], function ($, _) {
    "use strict";

    $.widget('orange35.customOptionHelper', {

        /**
         * Widget constructor
         *
         * @returns {orange35.customOptionHelper}
         * @private
         */
        _create: function () {
            return this;
        },

        /**
         * Extending objects
         *
         * @param child
         * @param parent
         */
        extend: function (child, parent) {
            var F = function () {};
            F.prototype = parent.prototype;
            child.prototype = new F();
            child.prototype.constructor = parent;
        },

    });
    return $.orange35.customOptionHelper;
});