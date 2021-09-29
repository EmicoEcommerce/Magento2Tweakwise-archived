/**
 * Tweakwise & Emico (https://www.tweakwise.com/ & https://www.emico.nl/) - All Rights Reserved
 *
 * @copyright Copyright (c) 2017-2017 Tweakwise.com B.V. (https://www.tweakwise.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

define([
    'jquery',
    'Magento_Catalog/js/product/list/toolbar'
], function ($, productListToolbarForm) {
    $.widget('tweakwise.productListToolbarForm', productListToolbarForm, {

        options: {
            ajaxFilters: false,
            pagerItemSelector: '.pages li.item',
            filterFormSelector: '#facet-filter-form'
        },

        /** @inheritdoc */
        _create: function () {
            var options = this.options;
            var element = this.element;
            // Dont assume that the form is available at all times
            var hasForm = $(this.options.filterFormSelector).length > 0;
            this.options.ajaxFilters = this.options.ajaxFilters && hasForm;

            this._bind($(options.modeControl), options.mode, options.modeDefault);
            this._bind($(options.directionControl), options.direction, options.directionDefault);
            this._bind($(options.orderControl), options.order, options.orderDefault);
            this._bind($(options.limitControl), options.limit, options.limitDefault);
            if (options.ajaxFilters) {
                $(element).on('click', options.pagerItemSelector, this.handlePagerClick.bind(this));
            }
        },

        handlePagerClick: function (event) {
            event.preventDefault();
            var anchor = $(event.target).closest('a');
            var page = anchor.attr('href') || '';
            var pageMatch = new RegExp('[?&]p=(\\\d+)').exec(page);
            var pageValue = 1;
            if (pageMatch) {
                pageValue = pageMatch[1];
            }
            return this.changeUrl('p', pageValue, pageValue);
        },

        /**
         * @param {String} paramName
         * @param {*} paramValue
         * @param {*} defaultValue
         */
        changeUrl: function (paramName, paramValue, defaultValue) {
            if (!this.options.ajaxFilters) {
                return this._super(paramName, paramValue, defaultValue);
            }

            var form = $(this.options.filterFormSelector);
            var input = form.find('input[name=' + paramName + ']');
            if (!input.length) {
                input = document.createElement('input');
                input.name = paramName;
                input = $(input);
                form.append(input);
                input.hide();
            }

            input.attr('value', paramValue);
            form.trigger('change');
            $('html, body').animate({scrollTop: 0}, 0);
        }

    });

    return $.tweakwise.productListToolbarForm;
});
