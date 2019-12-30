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
            seoEnabled: false,
            categoryId: '2',
            ajaxEndpoint: '/tweakwise/ajax/navigation',
            filterSelector: '#layered-filter-block',
            productListSelector: '.products.wrapper',
            toolbarSelector: '.toolbar.toolbar-products',
            isLoading: false,
        },

        _hookEvents: function() {
            this.element.on('click', '.item input[type="checkbox"]', this._handleCheckboxClick.bind(this));
            this.element.on('click', '.js-swatch-link', this._handleSwatchClick.bind(this));
            this.element.on('change', this._handleCheckboxClick.bind(this));
        },

        /**
         * Handle Ajax request for new content
         *
         * @param event
         * @private
         */
        _handleCheckboxClick: function(event) {
            // TODO Add check if this is a proper url, otherwise navigate to filter link
            event.preventDefault();
            if (this.options.isLoading) {
                return;
            }
            var url = this.options.ajaxEndpoint;
            var filters = filterHelper.getFilterParams(this.element);
            // Add category id
            filters = filters + '&category_id=' + this.options.categoryId;

            this._startLoader();
            jQuery.ajax({
                url: url,
                data: filters,
                success: function(response) {
                    this._updateBlocks(response);
                }.bind(this),
                error: function(response) {
                    // TODO Error handling
                }.bind(this),
                complete: function() {
                    this._stopLoader();
                }.bind(this)
            });
        },

        /**
         *
         * @param event
         * @private
         */
        _handleSwatchClick: function(event) {
            event.preventDefault();
            this._handleCheckboxClick(event);
        },

        /**
         *
         * @param event
         * @private
         */
        _handleSliderEvent: function(event) {
            event.preventDefault();
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

            var parsedHtml = jQuery(jQuery.parseHTML(htmlResponse));
            var newFiltersHtml = parsedHtml.filter(filterSelector);
            var newProductListHtml = parsedHtml.filter(productListSelector);
            var newToolbarHtml =  parsedHtml.filter(toolbarSelector);
            // Toolbar is included twice in the response
            var newToolbarFirstHtml = newToolbarHtml.first();
            var newToolbarLastHtml = newToolbarHtml.last();

            jQuery(filterSelector)
                .html(newFiltersHtml)
                .trigger('contentUpdated');
            jQuery(productListSelector)
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
         * Start loader targeting body
         * @private
         */
        _startLoader: function() {
            jQuery('body').trigger('processStart');
            this.options.isLoading = true;
        },

        /**
         * Stop Loader targeting body
         * @private
         */
        _stopLoader: function() {
            jQuery('body').trigger('processStop');
            this.options.isLoading = false;
        },

        _create: function() {
            this._hookEvents();
            return this._superApply(arguments);
        }
    });

    return $.tweakwise.navigationFilterAjax;
});
