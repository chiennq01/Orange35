/**
 * Orange35 Magento Team
 */
define([
    "jquery",
    'Magento_Ui/js/modal/confirm',
    "underscore",
    "jquery/ui"
], function ($, confirmation, _) {
    "use strict";

    $.widget('matchesManager.js', {

        /**
         * Convert combinations into rows
         *
         * @param combinations
         * @param images
         * @param keys
         * @returns {Array}
         */
        prepareMatchesForRender: function (combinations, images, keys) {
            var rows = [];
            var optionsLabels = keys.options;
            for (var i = 0; i < combinations.length; i++) {
                var newMatch = {};
                newMatch = _.clone(combinations[i]);
                var matches = _.clone(newMatch.values);
                newMatch.jsonFormat = JSON.stringify(newMatch);
                for (var option in optionsLabels) {
                    if (option != "Images" && option != "Actions") {
                        if (matches[option] == "%all%") {
                            matches[option] = "%all%";
                        } else if (!matches[option]) {
                            matches[option] = "--Not Selected--";
                        }
                    }
                }
                newMatch.values = matches;
                rows.push(newMatch);
            }
            return rows;
        },

        /**
         * Comparing matches
         *
         * @param leftMatch
         * @param rightMatch
         * @returns {boolean}
         */
        compareMatches: function (leftMatch, rightMatch) {
            if (!leftMatch || !leftMatch.values) {
                throw 'Invalid left argument';
            }
            if (!rightMatch || !rightMatch.values) {
                throw 'Invalid right argument';
            }
            var leftValues = leftMatch.values;
            var rightValues = rightMatch.values;

            if (Object.keys(leftValues).length !== Object.keys(rightValues).length) {
                return false;
            }
            for (var optionId in leftValues) {
                if (leftValues.hasOwnProperty(optionId)) {
                    if (leftValues[optionId] instanceof Array && rightValues[optionId] instanceof Array) {
                        if (leftValues[optionId].length === rightValues[optionId].length) {
                            for (var i = 0, length = leftValues[optionId].length; i < length; i++) {
                                if (leftValues[optionId][i] !== rightValues[optionId][i]) {
                                    return false;
                                }
                            }
                        } else {
                            return false;
                        }
                    } else if (leftValues[optionId] !== rightValues[optionId]) {
                        return false;
                    }
                }
            }
            return true;
        },
    });
    return $.matchesManager.js;
});