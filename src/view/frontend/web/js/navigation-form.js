/**
 * Tweakwise & Emico (https://www.tweakwise.com/ & https://www.emico.nl/) - All Rights Reserved
 *
 * @copyright Copyright (c) 2018-2018 Tweakwise.com B.V. (https://www.tweakwise.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

define(['jquery',
    'jquery/ui',
    'tweakwiseFilterHelper'
], function($, jQueryUi, filterHelper) {
    $.widget('tweakwise.navigationForm', {

        _hookEvents: function() {
            this.element.on('submit', this._handleFilterButtonClick.bind(this));
        },

        _handleFilterButtonClick: function(event) {
            event.preventDefault();
            var formElement = $(event.target()).closest('form');
            var url = filterHelper.getFilterParams(formElement);

            if (url !== '') {
                window.location = '?' + url;
            }
        },

        _create: function() {
            this._hookEvents();
            return this._superApply(arguments);
        }
    });

    return $.tweakwise.navigationForm;
});
