/**
 * Tweakwise & Emico (https://www.tweakwise.com/ & https://www.emico.nl/) - All Rights Reserved
 *
 * @copyright Copyright (c) 2017-2017 Tweakwise.com B.V. (https://www.tweakwise.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

define([
    'jquery',
    'mage/cookies'
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
            productsGridSelector: '.products-grid',
            mainColumnSelector: '.column.main',
            emptyInfoMessageSelector: '.message.info.empty',
            noteMessageSelector: '.message.note',
            noticeMessageSelector: '.message.notice',
            isLoading: false,
            ajaxCache: true,
        },

        currentXhr: null,

        _create: function () {
            this._hookEvents();
            this._fixAjaxHistory();
            return this._superApply(arguments);
        },

        /**
         * Fix first page history on page load when using ajax filters
         *
         *  @private
         */
        _fixAjaxHistory: function () {
            if(this.options.ajaxFilters && this.options.ajaxCache && (!window.history.state || !window.history.state.html))
            {
                //if window history is empty, do an ajax request to fill it.
                this.currentXhr = $.ajax({
                    url: this.options.ajaxEndpoint,
                    data: this._getFilterParameters(),
                    cache: this.options.ajaxCache,
                    success: function (response) {
                        window.history.replaceState({html: response.html}, '', response.url);
                    }.bind(this)
                });
            }
        },

        /**
         * Bind filter events, these are filter click and filter remove
         *
         * @private
         */
        _hookEvents: function () {
            this._bindFilterClickEvents();
            this._bindFilterRemoveEvents();

            if (this.options.ajaxFilters) {
                this._bindPopChangeHandler();
            }
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

        /**
         * This provides a means to disable ajax filtering.
         * If you dont want ajax filtering for certain filters add a data-no-ajax attribute.
         *
         * @param event
         * @returns {boolean}
         * @private
         */
        _allowAjax: function (event) {
            var a = $(event.target).closest('a');
            return !a.data('no-ajax');
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

            if (!this._allowAjax(event)) {
                this._defaultHandler(event);
                return;
            }

            this._startLoader();
            this.currentXhr = $.ajax({
                url: this.options.ajaxEndpoint,
                data: this._getFilterParameters(),
                cache: this.options.ajaxCache,
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
            var filterId = '#' + $(event.target).data('js-filter-id');
            var filter = this.element.find(filterId);
            if (filter && filter.length) {
                event.preventDefault();
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

            var wrapper = document.createElement('div');
            wrapper.innerHTML = htmlResponse;
            var parsedHtml = $(wrapper);

            var newFilters = parsedHtml.find(filterSelector);
            var newProductList = parsedHtml.find(productListSelector);
            var newToolbar = parsedHtml.find(toolbarSelector);
            // Toolbar is included twice in the response
            // We use this first().get(0) construction to access outerHTML
            // The reason for this is that we need to replace the entire toolbar because otherwise
            // the data-mage-init attribute is no longer available on the toolbar and hence the toolbar
            // would not be initialized when $('body').trigger('contentUpdated'); is called
            var newToolbarFirst = newToolbar.first().get(0);
            var newToolbarLast = newToolbar.last().get(0);

            if (newFilters.length) {
                $(filterSelector).replaceWith(newFilters);
            }

            var toolbar = $(toolbarSelector);
            const productsGrid = $(this.options.productsGridSelector);
            const mainColumn = $(this.options.mainColumnSelector);
            const emptyInfo = $(this.options.emptyInfoMessageSelector);
            const note = $(this.options.noteMessageSelector);
            const notice = $(this.options.noticeMessageSelector);

            emptyInfo.remove();
            note.remove();
            notice.remove();

            /*
            The product list comes after the toolbar.
            We use this construction as there could be more product lists on the page
            and we dont want to replace them all
            */
            if (newProductList.length) {
                toolbar
                    .siblings(productListSelector)
                    .replaceWith(newProductList);
            } else {
                /*
                It happens that a filter yields no result.
                In that case magento returns a message in a div
                that needs to rendered correctly and also removed if not required
                */
                toolbar.hide();
                productsGrid.hide();
                $('script[data-role=\'msrp-popup-template\']').remove();
                $('script[data-role=\'msrp-info-template\']').remove();
                mainColumn.append(htmlResponse);
            }
            if (newToolbarFirst) {
                toolbar
                    .first()
                    .replaceWith(newToolbarFirst.outerHTML);
            }
            if (newToolbarLast) {
                var scripts = '';
                $(newToolbarLast).siblings('script[type="text/x-magento-init"]').map(
                    function (index, element) {
                        scripts += element.outerHTML;
                    }
                );

                toolbar
                    .last()
                    .replaceWith(newToolbarLast.outerHTML + scripts);
            }

            const primaryActionsDiv = $(".actions-primary")
            primaryActionsDiv.find('form').
            each(function (i, form) {
                $(form).append('<input name="form_key" type="hidden" ' +
                    ' value="' + $.mage.cookies.get('form_key') + '" />');
            });

            $('body').trigger('contentUpdated');
        },

        /**
         *
         * @param response
         * @private
         */
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

