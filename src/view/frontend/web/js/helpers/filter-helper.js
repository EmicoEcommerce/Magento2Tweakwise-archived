/**
 * Tweakwise & Emico (https://www.tweakwise.com/ & https://www.emico.nl/) - All Rights Reserved
 *
 * @copyright Copyright (c) 2018-2018 Tweakwise.com B.V. (https://www.tweakwise.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

define(['jquery'], function($) {

    /**
     * We just need methods
     */
    return {

        getFilterParams: function(formElement) {

            var values = $(formElement).serialize();
            var sliderValues = this.getSliderUrlParameters(formElement);
            var searchValue = this.getSearchParam();

            var url = values;
            if (searchValue) {
                url = url + '&' + searchValue;
            }

            if (sliderValues) {
                url = url + '&' + sliderValues;
            }

            return url;
        },

        getSearchParam: function() {
            var q = this._getQParam();
            var searchParam = {};
            if (q) {
                searchParam['q'] = q;
                return $.param(searchParam);
            }

            return '';
        },

        _getQParam: function() {
            var matches = window.location.search.match(/(\?|&)q\=([^&]*)/);
            if (matches && matches[2]) {
                var trimmedMatch = matches[2].replace(/\+/g, ' '),
                    searchVal    = jQuery('#search').val();

                if (searchVal === trimmedMatch) {
                    return decodeURIComponent(trimmedMatch);
                }

                return decodeURIComponent(matches[2]);
            }

            return '';
        },

        getSliderUrlParameters: function(formElement) {
            var query = {};
            jQuery(formElement).find('.slider-attribute').each(function(i, slider) {
                slider = jQuery(slider);
                var key = slider.data('url-key');
                var min = slider.data('min');
                var max = slider.data('max');
                var rangeMin = slider.data('range-min');
                var rangeMax = slider.data('range-max');
                if ((min && max) && (rangeMin !== min || rangeMax !== max)) {
                    query[key] = min + '-' + max;
                }
            });

            return $.param(query);
        },
    };
});
