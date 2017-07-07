/**
 * @author Freek Gruntjes <fgruntjes@emico.nl>
 * @copyright (c) Emico B.V. 2017
 */

require([
    'jquery',
    'quickSearch'
], function($, quickSearch){
    $.widget('mage.tweakwiseQuickSearch', quickSearch, {

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

            return this._superApply(arguments);
        },

        _onSubmit: function () {
            var url = this.getSelectedProductUrl();
            if (!url) {
                return this._superApply(arguments);
            }

            window.location.href = url;
        }
    });

    return $.mage.tweakwiseQuickSearch;
});