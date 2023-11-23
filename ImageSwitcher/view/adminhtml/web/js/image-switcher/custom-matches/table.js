/*
 *  Orange35 Magento Team
 */
define([
    'ko',
    'Magento_Ui/js/form/components/html',
    'jquery',
    'uiRegistry',
    'underscore',
    'Orange35_ImageSwitcher/js/image-switcher/matches-manager',
    'Orange35_ImageSwitcher/js/image-switcher/images-manager',
    'Orange35_ImageSwitcher/js/image-switcher/elements/drop-down',
    'Orange35_ImageSwitcher/js/image-switcher/elements/checkbox',
    'Orange35_ImageSwitcher/js/image-switcher/elements/radio',
    'Orange35_ImageSwitcher/js/image-switcher/elements/multiselect'
], function (ko, Html, $, reg, _, MatchesManager, ImagesManager, DropDown, Checkbox, Radio, Multiselect) {
    'use strict';

    return Html.extend({

        /**
         * default variables
         */
        defaults: {
            template: 'Orange35_ImageSwitcher/image-switcher/custom-matches/table',
            columnsHeader: true,
            matches: [],
            optionsLabels: {},
            optionsCollection: {},
            optionsOrder: {},
            rows: [],
            matchesManager: {},
            imagesManager: {},
            lables: [],
            hasData: false,
            isCHanged: false,
            isOptionInit: false,
            isImageInit: false,
            deleteString: 'Delete',
            editString: 'Edit',
            "links": {
                "productId": "${ $.provider }:data.product.current_product_id",
                "options": "${ $.provider }:data.product.options",
                "images": '${$.ns}.${$.ns}.custom_matches.custom_matches_content.media_gallery_images_list.media_gallery_images_content:images'
            },
            listens: {
                matches: 'prepareMatchesForRender',
                optionsLabels: 'getLabels'
            }
        },

        /**
         * Extends instance with defaults. Invokes parent initialize method.
         * Calls initMatchesManager, setListenerOnImages, setListenerOnTableButtons,
         * setListenerOnSaveButtons, setListenerOnExit, loadMatches methods.
         */
        initialize: function () {
            this._super()
                .initMatchesManager()
                .setListenerOnImages()
                .setListenerOnTableButtons()
                .setListenerOnSaveButtons()
                .setListenerOnExit()
                .loadMatches();
        },

        /**
         * Initializes observable properties.
         *
         * @returns {Element} Chainable.
         */
        initObservable: function () {
            this._super();
            this.observe([
                'matches',
                'optionsLabels',
                'optionsOrder',
                'lables',
                'rows',
                'hasData'
            ]);
            return this;
        },

        /**
         * Initializes matches manager
         *
         * @returns {exports}
         */
        initMatchesManager: function () {
            this.matchesManager = new MatchesManager();
            return this;
        },

        /**
         * Set listeners on images
         *
         * @returns {exports}
         */
        setListenerOnImages: function () {
            var that = this;
            $("body").on("click", '[id^="gallery_image_"]', function () {
                if (!that.isImageInit) {
                    var elementId = this.id;
                    var elementIdPrefixLength = ('gallery_image_').length;
                    var imageId = parseInt(elementId.substr(elementIdPrefixLength));
                    that.initImageManager(imageId);
                    that.isImageInit = true;
                }
            });
            return this;
        },

        /**
         * Set listeners on table buttons.
         *
         * @returns {Element} Chainable.
         */
        setListenerOnTableButtons: function () {
            return this;
        },

        /**
         * Initializes image manager
         *
         * @param elementId
         * @returns {exports}
         */
        initImageManager: function (elementId) {
            this.imagesManager = ImagesManager().initObject($("#gallery_image_selector"));
            if (elementId !== undefined) {
                this.imagesManager.setImageId(elementId, true);
            }
            return this;
        },

        /**
         * Converting matchs into rows and display
         */
        prepareMatchesForRender: function () {
            this.rows([]);
            this.rows(this.matchesManager.prepareMatchesForRender(this.matches(), this.images, this.optionsLabels()));
            if (this.rows().length) {
                this.hasData(true);
            } else {
                this.hasData(false);
            }
        },

        /**
         * Get labels for table
         *
         * @returns {exports}
         */
        getLabels: function () {
            for (var id in this.optionsLabels().options) {
                if (this.optionsLabels().options.hasOwnProperty(id)) {
                    this.lables.push(this.optionsLabels().options[id]);
                }
            }
            this.lables.push('Images');
            this.lables.push('Actions');

            return this;
        },

        /**
         * Load combinations via ajax
         *
         * @returns {exports}
         */
        loadMatches: function () {
            var that = this;
            $.ajax({
                showLoader: false,
                url: that.ajaxUrl,
                data: {
                    'productId': this.productId
                },
                type: "POST",
                dataType: 'json'
            }).done(function (data) {
                data.optionsLabels.options.Images = $.mage.__('Images');
                data.optionsLabels.options.Actions = $.mage.__('Actions');
                data.optionsOrder.push('Images', 'Actions');
                that.optionsLabels(data.optionsLabels);
                that.optionsOrder(data.optionsOrder);
                that.matches(data.matches);
            });
            return this;
        },

        /**
         * Initializes options collection
         *
         * @returns {exports}
         */
        initOptions: function () {
            for (var i = 0; i < this.options.length; i++) {
                if (["drop_down", "radio", "checkbox", "multiple"].indexOf(this.options[i].type) != -1) {
                    var key = this.options[i].initial_option_id;
                    this.optionsCollection[key] = this.runOptionFactory(this.options[i]);
                }
            }
            return this;
        },

        /**
         * Initializes options' objects
         *
         * @param option
         * @returns {*}
         */
        runOptionFactory: function (option) {
            if (!this.isOptionDefinedInProduct(option)) {
                throw 'Option with ID ' + option.option_id + ' not founded in product options';
            }
            var type = option.type;
            var valuesIds = this.getOptionValuesIds(option);
            var optionBoxElement = $("#match_option_" + option.initial_option_id).get(0);
            switch (type) {
                case "drop_down": return DropDown().initOption(option.initial_option_id, optionBoxElement, valuesIds);
                case "radio": return Radio().initOption(option.initial_option_id, optionBoxElement, valuesIds);
                case "checkbox": return Checkbox().initOption(option.initial_option_id, optionBoxElement, valuesIds);
                case "multiple": return Multiselect().initOption(option.initial_option_id, optionBoxElement, valuesIds);
            }
        },

        /**
         * Get initial_value_id of values
         *
         * @param option
         * @returns {Array}
         */
        getOptionValuesIds: function (option) {
            var acceptableValues = [];
            for (var i=0; i<option.values.length; i++) {
                acceptableValues.push(option.values[i].initial_value_id);
            }
            return acceptableValues;
        },

        /**
         * Check if property exists in object
         *
         * @param option
         * @returns {boolean}
         */
        isOptionDefinedInProduct: function (option) {
            var productOptions = this.options;
            for (var i = 0; i < productOptions.length; i++) {
                if (productOptions[i].option_id === option.option_id) {
                    return true;
                }
            }
            return false;
        },

        /**
         * Action when button "Apply" is clicked
         */
        apply: function () {
            if (!this.isOptionInit) {
                this.initOptions();
                this.isOptionInit = false;
            }
            this.processAddMatch();
        },

        /**
         * Process adding match into table
         */
        processAddMatch: function () {
            var that = this;
            var optionsCollection = this.optionsCollection ? this.optionsCollection : [];
            var values = {};
            for (var optionId in optionsCollection) {
                if (optionsCollection.hasOwnProperty(optionId)) {
                    var optionObj = optionsCollection[optionId];
                    if ($("[option_id = "+optionId+"]").is(":checked")) {
                        values[optionId] = $("[option_id = "+optionId+"]").get(0).value;
                    } else {
                        if (optionObj.hasValue()) {
                            values[optionId] = optionObj.getValue();
                        }
                    }
                }
            }
            if (!Object.keys(values).length || !Object.keys(that.imagesManager).length) {
                that.displayErrorMessage();
            } else {
                var match = {
                    productId : this.productId,
                    imageId   : that.imagesManager.getImageId(),
                    imagesId  : that.imagesManager.getImagesId().slice(0),
                    values    : values
                };
                if (that.imagesManager.hasImagesId()) {
                    that.addMatchToTable(match);
                    that.imagesManager.imageId = false;
                    that.imagesManager.imagesId = [];
                    that.imagesManager.updateView();
                } else {
                    that.displayErrorMessage();
                }
            }
        },

        /**
         * Delete match from table
         *
         * @param data
         * @param event
         */
        processDeleteMatch: function (data, event) {
            var element = event.target;
            var matches = this.matches();
            matches.splice(element.readAttribute('value'), 1);
            this.isChanged = true;
            this.matches(matches);
        },

        /**
         * Edit match
         */
        processEditMatch: function (data, event) {
            var that = this;
            if (!Object.keys(that.imagesManager).length) {
                that.initImageManager();
                that.isImageInit = true;
            }
            if (!Object.keys(that.optionsCollection).length) {
                that.initOptions();
            }
            var element = event.target;
            var matchId = element.readAttribute('value');
            var match = this.matches()[matchId];

            if (Object.keys(that.optionsCollection).length && Object.keys(that.imagesManager).length && match) {
                var values = that.matches()[matchId].values;
                var optionsCollection = that.optionsCollection;
                for (var optionId in optionsCollection) {
                    if (optionsCollection.hasOwnProperty(optionId)) {
                        $("[option_id = "+ optionId +"]").get(0).checked = false;
                        //var ui_name = $("[option_id = "+ optionId +"]").get(0).readAttribute('ui_class');
                        //var ui_class = reg.get(ui_name);
                        //ui_class.checked(false);
                        if (optionsCollection.hasOwnProperty(optionId)) {
                            if (values.hasOwnProperty(optionId)) {
                                if (values[optionId] != '%all%') {
                                    optionsCollection[optionId].setValue(values[optionId], true);
                                } else if (values[optionId] == '%all%') {
                                    $("[option_id = "+ optionId +"]").get(0).checked = true;
                                    //ui_name = $("[option_id = "+ optionId +"]").get(0).readAttribute('ui_class');
                                    //ui_class = reg.get(ui_name);
                                    //ui_class.checked(true);
                                }
                            } else {
                                optionsCollection[optionId].setValue(false, true);
                            }
                        }
                    }
                }
                that.imagesManager.imagesId = [];
                match.imagesId.each(function (image_id) {
                    that.imagesManager.setImageId(image_id, true);
                });
                $('html, body').animate({
                    scrollTop: $('[data-index=custom_options_list]').offset().top
                    - $('[data-ui-id=page-actions-toolbar-content-header]').outerHeight()
                }, 300);
            }
        },

        /**
         * Add match to table
         *
         * @param match
         */
        addMatchToTable: function (match) {
            var that = this;
            var elements = that.matches();
            var index;
            that.matches([]);
            var exists = false;
            for (var i = 0; i < elements.length; i++) {
                var ex  = that.matchesManager.compareMatches(match, elements[i]);
                if (ex) {
                    exists = true;
                    index = i;
                    break;
                }
            }
            if (exists) {
                var toChange = confirm($.mage.__("Do you want to change already existing match?"));
                if (toChange === true) {
                    that.isChanged = true;
                    elements.splice(index, 1, match);
                }
            } else {
                that.isChanged = true;
                elements.push(match);
            }
            that.matches(elements);
        },

        /**
         * Display error message
         */
        displayErrorMessage: function () {
            $("#matches-error-message").fadeIn();
            var timeoutId = window.setTimeout(function () {
                $("#matches-error-message").fadeOut();
                timeoutId = 0;
            }, 4000);
        },

        /**
         * Set listener on save buttons in order to show alert when changs are not saved
         * (kostil)
         *
         * @returns {exports}
         */
        setListenerOnSaveButtons: function () {
            var that = this;
            $("[data-ui-id ='save-button']").click(function () {
                that.isChanged = false;
            });
            $("[data-ui-id^='save-button-item']").each(function (item) {
                $(this).click(function () {
                    that.isChanged = false;
                });
            });
            return this;
        },

        /**
         * Display alert when user leaves page without saving changes
         *
         * @returns {exports}
         */
        setListenerOnExit: function () {
            var that = this;
            $(window).on('beforeunload', function () {
                if (that.isChanged) {
                    return "Are you sure you want to leave?";
                }
            });
            return this;
        }
    });
});