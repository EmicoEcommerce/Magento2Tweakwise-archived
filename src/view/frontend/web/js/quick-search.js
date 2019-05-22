/**
 * Tweakwise & Emico (https://www.tweakwise.com/ & https://www.emico.nl/) - All Rights Reserved
 *
 * @copyright Copyright (c) 2017-2017 Tweakwise.com B.V. (https://www.tweakwise.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

define([
    'jquery',
    'Magento_Search/form-mini'
], function($, quickSearch){
    $.widget('tweakwise.quickSearch', quickSearch, {

        getSelectedProductUrl: function() {
            if (!this.responseList.selected) {
                return null;
            }

            return this.responseList.selected.data('url');
        },

        _create: function() {
            $(this.options.formSelector).on('submit', function(event) {
                if (this.getSelectedProductUrl()) {
                    event.preventDefault();
                }
            }.bind(this));

            var templateId = '#autocomplete-item-template';
            this.options.template = templateId;
            this.options.url = $(templateId).data('url');

            return this._superApply(arguments);
        },

        _onSubmit: function (e) {
            var url = this.getSelectedProductUrl();
            if (!url) {
                return this._superApply(arguments);
            }

            if (url !== null) {
                window.location.href = url;
            }
        },

        _onPropertyChange: function () {
            if (this.searchDelayTimeout) {
                clearTimeout(this.searchDelayTimeout);
            }

            this.searchDelayTimeout = setTimeout(function() {
                quickSearch.prototype._onPropertyChange.apply(this);
            }.bind(this), 200);
        }
    });

    return $.tweakwise.quickSearch;
});