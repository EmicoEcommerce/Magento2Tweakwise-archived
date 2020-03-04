/**
 * Tweakwise & Emico (https://www.tweakwise.com/ & https://www.emico.nl/) - All Rights Reserved
 *
 * @copyright Copyright (c) 2017-2017 Tweakwise.com B.V. (https://www.tweakwise.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

define([
    'jquery',
    'tweakwiseFilterHelper'
], function($, filterHelper) {
    $.widget('tweakwise.navigationForm', {

        options: {
            ajaxFilters: false,
            formFilters: false,
            seoEnabled: false,
            ajaxEndpoint: '/tweakwise/ajax/navigation',
            filterSelector: '#layered-filter-block',
            productListSelector: '.products.wrapper',
            toolbarSelector: '.toolbar.toolbar-products',
            isLoading: false,
        },

        _create: function() {
            this._hookEvents();
            return this._superApply(arguments);
        },

        /**
         * Bind filter events, these are filter click and filter remove
         *
         * @private
         */
        _hookEvents: function() {
            this._bindFilterClickEvents();
            this._bindFilterRemoveEvents();
        },

        /**
         * Bind filter click events
         *
         * @private
         */
        _bindFilterClickEvents: function() {
            if (this.options.formFilters) {
                this.element.on('click', '.js-btn-filter', this._getFilterClickHandler().bind(this));
            } else {
                this.element.on('click', '.item input[type="checkbox"]', this._getFilterClickHandler().bind(this));
                // The change event is triggered by the slider
                this.element.on('change', this._getFilterClickHandler().bind(this));
            }
        },

        /**
         * Filter remove events are only relevant for ajax filtering. If ajaxFilters is false then we just navigate
         * to the url specified in the a.
         *
         * @private
         */
        _bindFilterRemoveEvents: function() {
            if (this.options.ajaxFilters) {
                this.element.on('click', 'a .remove', this._ajaxClearHandler.bind(this));
            }
        },

        /**
         * Should return the handler for the filter event, depends on config options.
         * Supported options are ajax filtering and form filters and any combination of those options.
         * Note that the ajaxHandler also handles the case ajax enabled AND form filters enabled
         *
         * @returns {tweakwise.navigationFilterAjax._ajaxHandler|tweakwise.navigationFilterAjax._defaultHandler}
         * @private
         */
        _getFilterClickHandler: function () {
            if (this.options.ajaxFilters) {
                return this._ajaxHandler;
            }

            if (this.options.formFilters) {
                return this._formFilterHandler;
            }

            return this._defaultHandler
        },

        // ------- Default filter handling (i.e. no ajax and no filter form)
        /**
         * Navigate to the selected filter url
         *
         * @param event
         * @returns {boolean}
         * @private
         */
        _defaultHandler: function (event) {
            var a = $(event.currentTarget).closest('a');
            var href = this._findHref(a);
            if (href) {
                window.location.href = href;
                return false;
            }
        },

        /**
         * Should return the url to navigate to
         *
         * @param aElement
         * @returns {*}
         * @private
         */
        _findHref: function (aElement) {
            var href = aElement.attr('href');
            if (this.options.seoEnabled) {
                var seoHref = aElement.data('seo-href');
                return seoHref ? seoHref : href;
            }

            return href;
        },
        // ------- End of default filter handling

        // ------- Handling for ajax filtering (i.e. only ajax filtering)
        /**
         * Handle Ajax request for new content
         *
         * @param event
         * @private
         */
        _ajaxHandler: function(event) {
            event.preventDefault();
            this._startLoader();

            jQuery.ajax({
                url: this.options.ajaxEndpoint,
                data: this._getFilterParameters(),
                success: function(response) {
                    this._updateBlocks(response);
                }.bind(this),
                error: function(response) {
                    // Something went wrong, try to navigate to the selected filter
                    this._defaultHandler(event);
                }.bind(this),
                complete: function() {
                    this._stopLoader();
                }.bind(this)
            });
        },

        /**
         * Serialize the form element but skip unwanted inputs
         *
         * @returns {*}
         * @private
         */
        _getFilterParameters: function() {
            return this.element.find(':not(.js-skip-submit)').serialize();
        },

        _ajaxClearHandler: function(event) {

        },

        /**
         * Update all relevant html with response data, trigger contentUpdated to 'trigger' data-mage-init
         * @param htmlResponse
         * @private
         */
        _updateBlocks: function (htmlResponse)
        {
            var filterSelector = this.options.filterSelector;
            var productListSelector = this.options.productListSelector;
            var toolbarSelector = this.options.toolbarSelector;

            var wrapper = document.createElement('div');
            wrapper.innerHTML = htmlResponse;
            var parsedHtml = jQuery(wrapper);

            var newFiltersHtml = parsedHtml.find(filterSelector);
            var newProductListHtml = parsedHtml.find(productListSelector);
            var newToolbarHtml =  parsedHtml.find(toolbarSelector);
            // Toolbar is included twice in the response
            var newToolbarFirstHtml = newToolbarHtml.first();
            var newToolbarLastHtml = newToolbarHtml.last();

            jQuery(filterSelector)
                .html(newFiltersHtml)
                .trigger('contentUpdated');

            /*
            The product list comes after the toolbar.
            We use this construction as there could be more product lists on the page
            and we dont want to replace them all
            */
            jQuery(toolbarSelector)
                .siblings(productListSelector)
                .html(newProductListHtml)
                .trigger('contentUpdated');
            jQuery(toolbarSelector)
                .first()
                .html(newToolbarFirstHtml)
                .trigger('contentUpdated');
            jQuery(toolbarSelector)
                .last()
                .html(newToolbarLastHtml)
                .trigger('contentUpdated');
        },

        /**
         * Start loader targeting body relevant for ajax filtering
         * @private
         */
        _startLoader: function() {
            jQuery('body').trigger('processStart');
            this.options.isLoading = true;
        },

        /**
         * Stop Loader targeting body relevant for ajax filtering
         * @private
         */
        _stopLoader: function() {
            jQuery('body').trigger('processStop');
            this.options.isLoading = false;
        },
        // ------- End of handling for ajax filtering

        // ------- Handling for form filters.
        // ------- Note that is only used when ajax is not enabled and form filters is enabled
        /**
         * This just handles the filter button click
         *
         * @param event
         * @private
         */
        _formFilterHandler: function (event) {
            event.preventDefault();
            var filterUrl = this._getFilterParameters();
            if (filterUrl) {
                window.location = filterUrl;
            }
        }
        // ------- End of handling for form filters
    });

    return $.tweakwise.navigationForm;
});
