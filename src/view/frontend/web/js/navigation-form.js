/**
 * Tweakwise & Emico (https://www.tweakwise.com/ & https://www.emico.nl/) - All Rights Reserved
 *
 * @copyright Copyright (c) 2017-2017 Tweakwise.com B.V. (https://www.tweakwise.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

define([
    'jquery',
], function ($) {
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

        currentXhr: null,

        _create: function () {
            this._hookEvents();
            return this._superApply(arguments);
        },

        /**
         * Bind filter events, these are filter click and filter remove
         *
         * @private
         */
        _hookEvents: function () {
            if (this.options.ajaxFilters) {
                this._bindPopChangeHandler()
            }
            this._bindFilterClickEvents();
            this._bindFilterRemoveEvents();
        },

        /**
         * Bind filter click events
         *
         * @private
         */
        _bindFilterClickEvents: function () {
            if (this.options.formFilters) {
                this.element.on('click', '.js-btn-filter', this._getFilterClickHandler().bind(this));
            } else {
                this.element.on('change', this._getFilterClickHandler().bind(this));
            }
        },

        /**
         * Filter remove events are only relevant for ajax filtering. If ajaxFilters is false then we just navigate
         * to the url specified in the a.
         *
         * @private
         */
        _bindFilterRemoveEvents: function () {
            if (this.options.ajaxFilters) {
                this.element.on('click', 'a.remove', this._ajaxClearHandler.bind(this));
            }
        },

        /**
         *
         * @private
         */
        _bindPopChangeHandler: function () {
            window.onpopstate = function (event) {
                if (event.state && event.state.html) {
                    this._updateBlocks(event.state.html);
                }
            }.bind(this);
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

        /**
         * Serialize the form element but skip unwanted inputs
         *
         * @returns {*}
         * @private
         */
        _getFilterParameters: function () {
            return this.element.find(':not(.js-skip-submit)').serialize();
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
            var a = $(event.target).closest('a');
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
        _ajaxHandler: function (event) {
            event.preventDefault();

            if (this.currentXhr) {
                this.currentXhr.abort();
            }

            this._startLoader();
            this.currentXhr = $.ajax({
                url: this.options.ajaxEndpoint,
                data: this._getFilterParameters(),
                cache: true,
                success: function (response) {
                    this._updateBlocks(response.html);
                    this._updateState(response);
                }.bind(this),
                error: function (jqXHR, errorStatus) {
                    if (errorStatus !== 'abort') {
                        // Something went wrong, try to navigate to the selected filter
                        this._defaultHandler(event);
                    }
                }.bind(this),
                complete: function () {
                    this._stopLoader();
                }.bind(this)
            });
        },

        /**
         * Handle filter clear events
         *
         * @param event
         * @private
         */
        _ajaxClearHandler: function (event) {
            event.preventDefault();
            var filterId = '#' + $(event.target).data('js-filter-id');
            var filter = this.element.find(filterId);
            if (filter && filter.length) {
                filter = $(filter);
                // Set filter disabled so that it will not be submitted when change is triggered
                filter.attr('disabled', true);
                if (this.options.formFilters) {
                    // Simulate click so that the form will be submitted
                    this.element.find('.js-btn-filter').first().trigger('click');
                } else {
                    filter.trigger('change');
                }
            }
        },

        /**
         * Update all relevant html with response data, trigger contentUpdated to 'trigger' data-mage-init
         * @param htmlResponse
         * @private
         */
        _updateBlocks: function (htmlResponse) {
            var filterSelector = this.options.filterSelector;
            var productListSelector = this.options.productListSelector;
            var toolbarSelector = this.options.toolbarSelector;
            var toolbar = $(toolbarSelector);

            var wrapper = document.createElement('div');
            wrapper.innerHTML = htmlResponse;
            var parsedHtml = $(wrapper);

            var newFiltersHtml = parsedHtml.find(filterSelector).html();
            var newProductListHtml = parsedHtml.find(productListSelector).html();
            var newToolbarHtml = parsedHtml.find(toolbarSelector);
            // Toolbar is included twice in the response
            var newToolbarFirstHtml = newToolbarHtml.first().html();
            var newToolbarLastHtml = newToolbarHtml.last().html();

            $(filterSelector)
                .html(newFiltersHtml)
                .trigger('contentUpdated');

            /*
            The product list comes after the toolbar.
            We use this construction as there could be more product lists on the page
            and we dont want to replace them all
            */
            toolbar
                .siblings(productListSelector)
                .html(newProductListHtml)
                .trigger('contentUpdated');
            toolbar
                .first()
                .html(newToolbarFirstHtml)
                .trigger('contentUpdated');
            toolbar
                .last()
                .html(newToolbarLastHtml)
                .trigger('contentUpdated');
        },

        _updateState: function (response) {
            window.history.pushState({html: response.html}, '', response.url);
        },

        /**
         * Start loader targeting body relevant for ajax filtering
         * @private
         */
        _startLoader: function () {
            $(this.options.productListSelector).trigger('processStart');
            this.options.isLoading = true;
        },

        /**
         * Stop Loader targeting body relevant for ajax filtering
         * @private
         */
        _stopLoader: function () {
            $(this.options.productListSelector).trigger('processStop');
            this.options.isLoading = false;
        },
        // ------- End of handling for ajax filtering

        // ------- Handling for form filters.
        // ------- Note that is only used when ajax is not enabled and form filters is enabled
        /**
         * This just handles the filter button click
         *
         * @private
         */
        _formFilterHandler: function () {
            var filterUrl = this._getFilterParameters();
            if (filterUrl) {
                window.location = '?' + filterUrl;
            }
        }
        // ------- End of handling for form filters
    });

    return $.tweakwise.navigationForm;
});
