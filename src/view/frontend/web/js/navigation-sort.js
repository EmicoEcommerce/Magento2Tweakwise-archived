/**
 * Tweakwise & Emico (https://www.tweakwise.com/ & https://www.emico.nl/) - All Rights Reserved
 *
 * @copyright Copyright (c) 2017-2017 Tweakwise.com B.V. (https://www.tweakwise.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

define([
    'jquery'
], function ($) {
    $.widget('tweakwise.navigationSort', {

        options: {
            hasAlternateSort: null,
        },

        _create: function () {
            this._hookEvents();
            return this._superApply(arguments);
        },

        /**
         * Bind more and less items click handlers
         *
         * @private
         */
        _hookEvents: function () {
            this.element.on('click', '.more-items', this._handleMoreItemsLink.bind(this));
            this.element.on('click', '.less-items', this._handleLessItemsLink.bind(this));
        },

        /**
         * Sort items depending on alternate sort (this comes from tweakwise api) and expand filter list
         *
         * @returns {boolean}
         * @private
         */
        _handleMoreItemsLink: function () {
            this._sortItems('alternate-sort');
            this.element.find('.default-hidden').show();
            this.element.find('.more-items').hide();

            return false;
        },

        /**
         * Sort items depending on alternate sort (this comes from tweakwise api) and abbreviate filter list
         *
         * @returns {boolean}
         * @private
         */
        _handleLessItemsLink: function () {
            this._sortItems('original-sort');
            this.element.find('.default-hidden').hide();
            this.element.find('.more-items').show();

            return false;
        },

        /**
         * Sort items based on alternate sort (if available)
         *
         * @param type
         * @private
         */
        _sortItems: function (type) {
            if (!this._hasAlternateSort()) {
                return;
            }

            var list = this.element.find('.items');
            list.children('.item').sort(function (a, b) {
                return $(a).data(type) - $(b).data(type);
            }).appendTo(list);
        },

        /**
         * Check if alternate sort is available as data property on filters
         *
         * @returns {null}
         * @private
         */
        _hasAlternateSort: function () {
            if (this.options.hasAlternateSort === null) {
                var list = this.element.find('.items');
                var firstItem = $(list).children().first();
                this.options.hasAlternateSort = firstItem.hasOwnProperty('data-alternate-sort');
            }

            return this.options.hasAlternateSort;
        }
    });

    return $.tweakwise.navigationSort;
});
