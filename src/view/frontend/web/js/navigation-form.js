/**
 * Tweakwise & Emico (https://www.tweakwise.com/ & https://www.emico.nl/) - All Rights Reserved
 *
 * @copyright Copyright (c) 2017-2017 Tweakwise.com B.V. (https://www.tweakwise.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

define([
    'jquery',
    'jquery/ui',
    'tweakwiseFilterHelper'
], function($, jQueryUi, filterHelper) {
    $.widget('tweakwise.navigationFilterAjax', {

        options: {
            ajaxEnabled: false,
            seoEnabled: false,
            categoryId: null,
            originalUrl: null,
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
         * Bind filter click events
         *
         * @private
         */
        _hookEvents: function() {
            this.element.on('click', '.item input[type="checkbox"]', this._getFilterHandler().bind(this));
            this.element.on('click', '.js-swatch-link', this._getFilterHandler().bind(this));
            // The change event is triggered by the slider
            this.element.on('change', this._handleCheckboxClick.bind(this));
        },

        /**
         * Should return the handler for the filter event, depends on config options.
         * Supported options are ajax filtering and form filters and any combination of those options
         *
         * @returns {tweakwise.navigationFilterAjax._ajaxHandler|tweakwise.navigationFilterAjax._defaultHandler}
         * @private
         */
        _getFilterHandler: function () {
            if (this.options.ajaxEnabled) {
                return this._ajaxHandler;
            }

            return this._defaultHandler
        },

        // ------- Default filter handling (i.e. navigation)
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
        // ------- End of default filter handling (i.e. navigation)

        // ------- Handling for ajax filtering
        /**
         * Handle Ajax request for new content
         *
         * @param event
         * @private
         */
        _ajaxHandler: function(event) {
            // TODO Add check if this is a proper url, otherwise navigate to filter link
            event.preventDefault();

            var url = this.options.ajaxEndpoint;
            var filters = filterHelper.getFilterParams(this.element);
            // Add category id
            if (this.options.categoryId) {
                filters = filters + '&__tw_category_id=' + this.options.categoryId;
            }
            // Add original url, this will be used to construct the new filter urls
            if (this.options.originalUrl) {
                filters = filters + '&__tw_original_url=' + this.options.originalUrl;
            }

            this._startLoader();
            jQuery.ajax({
                url: url,
                data: filters,
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
            The product list comes after the toolbar
            We use this construction as there could be more product lists on the page
            and we dont want to replace them all
            */
            jQuery(toolbarSelector)
                .next(productListSelector)
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
        // ------- End of handling for ajax filtering

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
        }
    });

    return $.tweakwise.navigationFilterAjax;
});
