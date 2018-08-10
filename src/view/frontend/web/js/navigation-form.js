/**
 * Tweakwise & Emico (https://www.tweakwise.com/ & https://www.emico.nl/) - All Rights Reserved
 *
 * @copyright Copyright (c) 2018-2018 Tweakwise.com B.V. (https://www.tweakwise.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

define(['jquery', 'jquery/ui'], function($) {
    $.widget('tweakwise.navigationForm', {
        _hookEvents: function() {
            if (this.options.hasOwnProperty('formFilters') && this.options.formFilters === "1") {
                this.element.on('submit', this._handleFilterButtonClick.bind(this));
            }
        },

        _handleFilterButtonClick: function(event) {
            event.preventDefault();

            let values = jQuery(this.element).serialize();
            let sliderValues = this._getSliderUrlParameters();
            let url = '?';

            if (values && sliderValues) {
                url = url + values + '&' + sliderValues;
            } else if (values) {
                url = url + values
            } else if (sliderValues) {
                url = url + sliderValues;
            }

            if (url === '?') {
                url = '';
            }
            window.location = url;
        },

        _getSliderUrlParameters: function() {
            let query = {};
            jQuery('.slider-attribute').each(function(i, slider) {
                slider = jQuery(slider);
                let key = slider.data('url-key');
                let from = slider.data('min');
                let to = slider.data('max');
                query[key] = from + '-' + to;
            });

            return jQuery.param(query);
        },

        _create: function() {
            this._hookEvents();
            return this._superApply(arguments);
        }
    });

    return $.tweakwise.navigationForm;
});