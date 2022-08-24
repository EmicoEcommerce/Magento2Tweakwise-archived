/**
 * Tweakwise & Emico (https://www.tweakwise.com/ & https://www.emico.nl/) - All Rights Reserved
 *
 * @copyright Copyright (c) (https://www.tweakwise.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

define([
    'jquery',
    'jquery/ui-modules/widgets/slider',
    'jQueryTouchPunch',
    'domReady!'
], function ($) {
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

        /**
         *
         * @returns {*}
         * @private
         */
        _create: function () {
            this._createSlider();
            this._bindInputChangeEvents();
            return this._superApply(arguments);
        },

        /**
         * Register the correct handler depending on configuration
         * @private
         */
        _createSlider: function () {
            $(this.options.container).find('.slider').slider(this._getSliderConfig());
        },

        /**
         *
         * @returns {{min: number, max: number, slide: *, values: [tweakwise.navigationSlider._getSliderConfig.options.currentMin, tweakwise.navigationSlider._getSliderConfig.options.currentMax], change: *, range: boolean}}
         * @private
         */
        _getSliderConfig: function () {
            return {
                range: true,
                min: this.options.min,
                max: this.options.max,
                values: [
                    this.options.currentMin, this.options.currentMax
                ],

                slide: function (event, ui) {
                    var container = $(this.options.container);
                    var minValue = ui.values[0];
                    var maxValue = ui.values[1];
                    container.find('.current-min-value').html(this._labelFormat(minValue));
                    container.find('.current-max-value').html(this._labelFormat(maxValue));
                    container.find('input.slider-min').val(minValue);
                    container.find('input.slider-max').val(maxValue);

                    var sliderUrlValue = minValue + '-' + maxValue;
                    var sliderUrlInput = container.find('input.slider-url-value');
                    sliderUrlInput.val(sliderUrlValue);

                    this._updateSliderDisabledAttribute(sliderUrlInput, sliderUrlValue);
                }.bind(this),

                change: this._getChangeHandler()
            }
        },

        /**
         * Bind handling for manual input
         *
         * @private
         */
        _bindInputChangeEvents: function () {
            var sliderContainer = $(this.options.container);
            sliderContainer.on('change', '.slider-min', this._updateSliderUrlInput.bind(this));
            sliderContainer.on('change', '.slider-max', this._updateSliderUrlInput.bind(this));
        },

        /**
         * Fire slider change event
         *
         * @private
         */
        _updateSliderUrlInput: function () {
            var sliderContainer = $(this.options.container);
            var sliderUrlInput = sliderContainer.find('.slider-url-value');
            var minValue = sliderContainer.find('.slider-min').val();
            var maxValue = sliderContainer.find('.slider-max').val();
            var inputValue = minValue + '-' + maxValue;
            sliderUrlInput.val(inputValue);
            this._updateSliderDisabledAttribute(sliderUrlInput, inputValue)
        },

        /**
         *
         * @param sliderUrlInput
         * @param inputValue
         * @private
         */
        _updateSliderDisabledAttribute: function (sliderUrlInput, inputValue) {
            if (inputValue === sliderUrlInput.data('disabled-input')) {
                sliderUrlInput.attr('disabled', true);
            } else {
                sliderUrlInput.removeAttr('disabled');
            }
        },

        /**
         * This determines the "slide" handler depending on configuration
         *
         * @returns {*}
         * @private
         */
        _getChangeHandler: function () {
            if (this.options.formFilters) {
                return this.formFilterChange.bind(this);
            }

            if (this.options.ajaxFilters) {
                return this.ajaxChange.bind(this);
            }

            return this.defaultChange.bind(this);
        },

        /**
         * Standard navigation, i.e. no ajax or formfilter options
         *
         * @param event
         * @param ui
         */
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

        /**
         * Ajax navigation, no formfilters
         *
         * @param event
         * @param ui
         */
        ajaxChange: function (event, ui) {
            this.formFilterChange(event, ui);
            $(this.element).closest('form').trigger('change');
        },

        /**
         * Used when form filters is set to true, just update the values, navigation is handled by the filter button
         *
         * @param event
         * @param ui
         */
        formFilterChange: function (event, ui) {
            var min = ui.values[0];
            var max = ui.values[1];
            var sliderContainer = $(this.options.container);
            sliderContainer.data('min', min);
            sliderContainer.data('max', max);
        },

        /**
         * Format slider label
         *
         * @param value
         * @returns {string}
         * @private
         */
        _labelFormat: function (value) {
            return this.options.prefix + value + this.options.postfix;
        }
    });

    return $.tweakwise.navigationSlider;
});
