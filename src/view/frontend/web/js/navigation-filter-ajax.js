/**
 * Tweakwise & Emico (https://www.tweakwise.com/ & https://www.emico.nl/) - All Rights Reserved
 *
 * @copyright Copyright (c) 2017-2017 Tweakwise.com B.V. (https://www.tweakwise.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

define(['jquery', 'jquery/ui'], function($) {
    $.widget('tweakwise.navigationFilterAjax', {
        _hookEvents: function() {
            this.element.on('click', '.item input[type="checkbox"]', this._handleCheckboxClick.bind(this));
            this.element.on('click', '.js-swatch-link', this._handleSwatchClick.bind(this));
        },

        _handleCheckboxClick: function(event) {
            var url = this.options.ajaxEndpoint;
            var form = this.element.closest('form');
            var filters = jQuery(form).serialize();
            filters = filters + '&category_id=' + this.options.categoryId;

            this._startLoader();
            jQuery.ajax({
                url: url,
                data: filters,
                success: function(response) {
                    var test = 1;
                },
                error: function(response) {
                    var test = 1;
                },
                complete: function() {
                    this._stopLoader();
                }
            });
        },

        _startLoader: function() {

        },

        _stopLoader: function() {

        },

        _handleSwatchClick: function(event) {
            event.preventDefault();
            this._handleCheckboxClick(event);
        },

        _create: function() {
            this._hookEvents();
            return this._superApply(arguments);
        }
    });

    return $.tweakwise.navigationFilterAjax;
});
