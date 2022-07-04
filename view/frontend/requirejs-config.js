var config = {
    map: {
        '*': {
            quickSearch: 'Emico_Tweakwise/js/quick-search',
            tweakwiseNavigationForm: 'Emico_Tweakwise/js/navigation-form',
            tweakwiseNavigationSort: 'Emico_Tweakwise/js/navigation-sort',
            tweakwiseNavigationSlider: 'Emico_Tweakwise/js/navigation-slider',
            tweakwiseNavigationSliderCompat: 'Emico_Tweakwise/js/navigation-slider-compat',
            tweakwisePMPageReload: 'Emico_Tweakwise/js/pm-page-reload',
            productListToolbarForm: 'Emico_Tweakwise/js/toolbar',
            jQueryTouchPunch: 'Emico_Tweakwise/js/lib/jquery.ui.touch-punch.min'
        }
    },
    shim: {
        'jQueryTouchPunch': {
            'deps': ['jquery-ui-modules/widget', 'jquery-ui-modules/mouse']
        }
    }
};
