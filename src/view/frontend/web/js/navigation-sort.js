/**
 * Tweakwise & Emico (https://www.tweakwise.com/ & https://www.emico.nl/) - All Rights Reserved
 *
 * @copyright Copyright (c) 2017-2017 Tweakwise.com B.V. (https://www.tweakwise.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

define(['jquery', 'jquery/ui'], function($) {
    $.widget('tweakwise.navigationSort', {

        options: {
            hasAlternateSort: null,
        },

        _hookEvents: function() {
            this.element.on('click', '.more-items', this._handleMoreItemsLink.bind(this));
            this.element.on('click', '.less-items', this._handleLessItemsLink.bind(this));
        },

        _handleMoreItemsLink: function() {
            this._sortItems('alternate-sort');
            this.element.find('.default-hidden').show();
            this.element.find('.more-items').hide();

            return false;
        },

        _handleLessItemsLink: function() {
            this._sortItems('original-sort');
            this.element.find('.default-hidden').hide();
            this.element.find('.more-items').show();

            return false;
        },

        _sortItems: function (type) {
            if (!this._hasAlternateSort()) {
                return;
            }

            let list = this.element.find('.items');
            list.children('.item').sort(function (a, b) {
                return $(a).data(type) - $(b).data(type);
            }).appendTo(list);
        },

        _hasAlternateSort: function() {
            if (this.options.hasAlternateSort === null) {
                let list = this.element.find('.items');
                let firstItem = $(list).children().first();
                this.options.hasAlternateSort = firstItem.hasOwnProperty('data-alternate-sort');
            }

            return this.options.hasAlternateSort;
        },

        _create: function() {
            this._hookEvents();
            return this._superApply(arguments);
        }
    });

    return $.tweakwise.navigationSort;
});
