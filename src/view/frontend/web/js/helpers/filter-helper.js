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

            let values = $(formElement).serialize();
            let sliderValues = this.getSliderUrlParameters(formElement);
            let searchValue = this.getSearchParam();

            let url = values;
            if (searchValue) {
                url = url + '&' + searchValue;
            }

            if (sliderValues) {
                url = url + '&' + sliderValues;
            }

            return url;
        },

        getSearchParam: function() {
            let q = this._getQParam();
            let searchParam = {};
            if (q) {
                searchParam['q'] = q;
                return $.param(searchParam);
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

        getSliderUrlParameters: function(formElement) {
            let query = {};
            jQuery(formElement).find('.slider-attribute').each(function(i, slider) {
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

            return $.param(query);
        },
    };
});
