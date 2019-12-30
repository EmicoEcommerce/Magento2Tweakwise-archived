/**
 * Tweakwise & Emico (https://www.tweakwise.com/ & https://www.emico.nl/) - All Rights Reserved
 *
 * @copyright Copyright (c) (https://www.tweakwise.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

define(['jquery', 'jquery/ui', 'domReady!'], function($) {
    $.widget('tweakwise.navigationSlider', {

        options: {
            filterUrl: '',
            prefix: '',
            postfix: '',
            container: '',
            min: 0,
            max: 99999999,
            currentMin: 0,
            currentMax: 99999999,
            formFilters: false,
            ajaxFilters: false,
        },

        _getSliderConfig: function() {
            return {
                range: true,
                min: this.options.min,
                max: this.options.max,
                values: [
                    this.options.currentMin, this.options.currentMax
                ],

                slide: function (event, ui) {
                    var container = $(this.options.container);
                    container.find('.current-min-value').html(this._labelFormat(ui.values[0]));
                    container.find('.current-max-value').html(this._labelFormat(ui.values[1]));
                }.bind(this),

                change: this._getChangeHandler()
            }
        },

        _getChangeHandler: function () {
            if (this.options.formFilters && this.options.ajaxFilters) {
                return this.ajaxFormFilterChange.bind(this);
            }

            if (this.options.formFilters) {
                return this.formFilterChange.bind(this);
            }

            if (this.options.ajaxFilters) {
                return this.ajaxChange.bind(this);
            }

            return this.defaultChange.bind(this);
        },

        defaultChange: function (event, ui) {
            var min = ui.values[0];
            var max = ui.values[1];
            var url = this.options.filterUrl;

            url = url.replace(encodeURI('{{from}}'), min);
            url = url.replace('{{from}}', min);
            url = url.replace(encodeURI('{{to}}'), max);
            url = url.replace('{{to}}', max);
            window.location.href = url;
        },

        ajaxChange: function (event, ui) {
            this.formFilterChange(event, ui);
            $(this.element).closest('form').trigger('change');
            // Do something with values
        },

        formFilterChange: function (event, ui) {
            var min = ui.values[0];
            var max = ui.values[1];
            var slider = jQuery(event.target).closest('.slider-attribute');
            slider.attr('data-min', min);
            slider.attr('data-max', max);
        },

        ajaxFormFilterChange: function (event, ui) {
            var min = ui.values[0];
            var max = ui.values[1];

            // Do something with values
        },

        _createSlider: function() {
            $(this.options.container).find('.slider').slider(this._getSliderConfig());
        },

        _labelFormat: function (value) {
            return this.options.prefix + value + this.options.postfix;
        },

        _create: function() {
            this._createSlider();
            return this._superApply(arguments);
        }
    });

    return $.tweakwise.navigationSlider;
});
