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
            let searchValue = this._getSearchParam();

            let url = '?';
            if (searchValue) {
                url = url + searchValue;
            }

            if (values && url !== '?') {
                url = url + '&' + values;
            } else if (values) {
                url = url + values;
            }

            if (sliderValues && url !== '?') {
                url = url + '&' + sliderValues;
            } else if (sliderValues) {
                url = url + sliderValues;
            }

            if (url !== '?') {
                window.location = url;
            }
        },

        _getSearchParam: function() {
            let q = this._getQParam();
            let searchParam = {};
            if (q) {
                searchParam['q'] = q;
                return jQuery.param(searchParam);
            }

            return '';
        },

        _getQParam: function() {
            let matches = window.location.search.match(/(\?|&)q\=([^&]*)/);
            if (matches && matches[2]) {
                let trimmedMatch = matches[2].replace(/\+/g, ' '),
                    searchVal    = jQuery('#search').val();

                if (searchVal === trimmedMatch) {
                    return decodeURIComponent(trimmedMatch);
                }

                return decodeURIComponent(matches[2]);
            }

            return '';
        },

        _getSliderUrlParameters: function() {
            let query = {};
            jQuery('.slider-attribute').each(function(i, slider) {
                slider = jQuery(slider);
                let key = slider.data('url-key');
                let min = slider.data('min');
                let max = slider.data('max');
                let rangeMin = slider.data('range-min');
                let rangeMax = slider.data('range-max');
                if ((min && max) && (rangeMin !== min || rangeMax !== max)) {
                    query[key] = min + '-' + max;
                }
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