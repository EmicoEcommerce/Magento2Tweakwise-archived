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
            pagerItemSelector: '.pages li.item'
        },

        /** @inheritdoc */
        _create: function () {
            this._bind(this.element.find(this.options.modeControl), this.options.mode, this.options.modeDefault);
            this._bind(this.element.find(this.options.directionControl), this.options.direction, this.options.directionDefault);
            this._bind(this.element.find(this.options.orderControl), this.options.order, this.options.orderDefault);
            this._bind(this.element.find(this.options.limitControl), this.options.limit, this.options.limitDefault);
            if (this.options.ajaxFilters) {
                $(this.element).on('click', this.options.pagerItemSelector, this.handlePagerClick.bind(this));
            }
        },

        handlePagerClick: function (event) {
            event.preventDefault();
            var anchor = $(event.target).closest('a');
            var page = anchor.attr('href') || '';
            var pageValueRegex = '[?&]p=(\\\d?)';
            var pageValue = new RegExp(pageValueRegex).exec(page);
            if (pageValue) {
                pageValue = pageValue[1];
                this.changeUrl('p', pageValue, pageValue)
            }

            return false;
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

            var form = document.getElementById('facet-filter-form');
            var input = document.createElement('input');

            input.name = paramName;
            input.value = paramValue;
            form.appendChild(input);
            $(input).hide();
            $(form).trigger('change');
        }

    });

    return $.tweakwise.productListToolbarForm;
});
