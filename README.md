[![Build Status](https://travis-ci.org/EmicoEcommerce/Magento2Tweakwise.svg?branch=master)](https://travis-ci.org/EmicoEcommerce/Magento2Tweakwise)
[![Code Climate](https://codeclimate.com/github/EmicoEcommerce/Magento2Tweakwise.png)](https://codeclimate.com/github/EmicoEcommerce/Magento2Tweakwise)
[![Codacy Badge](https://api.codacy.com/project/badge/Grade/a273bc8c5317438c9d18c6f2c2c67c3f)](https://www.codacy.com/app/Fgruntjes/Magento2Tweakwise?utm_source=github.com&amp;utm_medium=referral&amp;utm_content=EmicoEcommerce/Magento2Tweakwise&amp;utm_campaign=Badge_Grade)

## Installation
Install package using composer
```sh
composer require emico/tweakwise
```
This will install emico/tweakwise and emico/tweakwise-export

Enable module(s) and run installers
```sh
php bin/magento module:enable Emico_TweakwiseExport Emico_Tweakwise
php bin/magento setup:upgrade
php bin/magento setup:static-content:deploy
```

## Configuration
When the extension is installed it is disabled by default. There are three different parts which can be enabled separately. Configuration can be found at Stores -> Configuration -> Catalog -> Tweakwise.

### Rundown of all possible settings
Below is a rundown of all configuration options

#### General:
1) Authentication key: This is used to communicate with tweakwise and determines your navigator instance, it should be the same as the key found in the navigator under `Connectivity > End points`.
2) Server url: The url of the tweakwise server.
3) Timeout: If tweakwise fails to respond after this time in seconds the request is aborted.

#### Layered Navigation (All settings depend on Enabled having value yes):
1) Enabled: Use tweakwise results in navigation, if disabled the standard magento navigation is used.
2) Category filters as link
3) Hide facets with only one option: Given a result set from tweakwise in which a filter has only one option show that filter or not?
4) Use default magento filter renderer: Use Magento standard filter templates or use templates bundled by the module.
   If you want to make full use of the features provided by this module then this should be set to No (i.e. make use of tweakwise template files).
5) Filter form: This depends on 'Use default magento filter renderer' having value No. Render all filters in a form with filter buttons so that the user can select a set of filters and then navigate to the result instead of immediately navigating to the results when a filter is clicked.
6) Filter url query parameters: Tweakwise filter urls will have all query parameters of the page in it so also the "cid" and utm_source parameters if present.
   You can determine in which way you want to filter these out (if any).
7) Filter url query arguments: This depends on 'Filter url query parameters' having any value not equal to 'Dont Filter'. This field specifies which parameters should be removed from the tweakwise filter urls.
8) Url strategy: Has two options Query parameters and Seo path slug. If query parameters is selected then the tweakwise filter urls (and thus your navigation urls) will be constructed as
    `www.example.com/example-category?color=red`.
    
    If Seo path slugs is selected the url is constructed as `www.example.com/example-category/color/red`.

#### Seo (All settings depend on Enabled having value yes)
1) Enabled: use Seo options yes or no.
2) Filter whitelist: A list of filters which should be indexable (all filters not selected here are not indexable). If a filter is marked as not indexable then its href attribute will be set to "#" its original url will be set in a data-seo-href attribute which will be used by javascript to navigate.
    Note that the category filter is always marked as indexable. This used to be a multiselect field containing magento attributes however tweakwise facilitates derived properties, these properties are not related to magento attributes and as such these filters would be not indexable.
    The field has changed to a comma separated text field so that these derived properties can be properly whitelisted.
3) Max allowed facets: This combines with the Filter whitelist setting. Filters are indexable if and only if they are in the whitelist and the selected filter count does not go above max_allowed_facets.
    The reason this is an AND check is because otherwise indexation will still happen on the non whitelisted filters and it is unclear which url is present (an arbitrary amount of filters could be selected).
    Suppose max allowed facet is 1 and only "size" is in the whitelist. Then filter "color" with value "red" is not indexable (since "color" is not in the whitelist).
    If we now allow the size filter to still be indexable then url example.com/category/color/red/size/M would be indexable whereas example.com/category/color/red is not which is incorrect.
    This would lead to infinite crawling on filter urls which is undesirable 
    
#### Autocomplete (All settings depend on Enabled having value yes)
1) Enabled: Use tweakwise autocomplete results or not.
2) Show products: Show product suggestions in autocomplete results.
3) Show suggestions: Show search suggestions in autocomplete results.
4) Stay in category: Use the current category when getting autocomplete results.
5) Maximum number of results: At most this many autocomplete results will be show.

#### Search (All settings depend on Enabled having value yes)
1) Enabled: Use tweakwise search of default magento search results
2) Tweakwise search template: The tweakwise template to use for search results (this determines which filters are visible)
3) Search language: This determines the language used by the store and is passed to tweakwise. Tweakwise uses this to determine word conjugations and also correct spelling errors when considering which results should be shown to the user.
    An example: suppose Language is set to 'Dutch' and the user types 'Bed' (which is the same in English, namely the place where one sleeps) then tweakwise might suggest 'Bedden' (this is plural for 'Beds')
    If Language is set to English then in the example above tweakwise might suggest 'Beds'.
    
#### Recommendations
1) Crosssell enabled: Replace magento native related products with tweakwise crosssell & upsell recommendations. Terminology is confusing since this is relevant for magento related products and not for magento crosssell products
2) Default crosssell template: Which tweakwise recommendation template to use for related products. Only relevant when crosssell is enabled
    This can also be configured on a product and on a category. The template used is determined as follows: first check product for a configured template if not then check the product category for a template. If the category does not have a template configured then use the default. 
3) Default crosssell group code: Only visible when Default crosssell template has value '- Group Code -'. Use this to specify the group of recommendations
4) Upsell Enabled: Replace magento native upsell results with tweakwise crosssell & upsell recommendations.
5) Default upsell template: Which template recommendation template to use for upsell products. Only relevant when upsell is enabled.
    This can also be configured on a product and on a category. The template used is determined as follows: first check product for a configured template if not then check the product category for a template. If the category does not have a template configured then use the default.
6) Default upsell group code:  Only visible when Default upsell template has value '- Group Code -'. Use this to specify the group of recommendations
7) Featured products enabled: If yes then tweakwise can show featured products on category pages.
8) Default Featured product template: The default template to use when rendering featured products.
    The template can also be set per category and falls back to this setting if not found on the category.
    
## Support
For in depth support regarding configuration and all options tweakwise has to offer use the following links.
1) Tweakwise support: https://www.tweakwise.com/support/
2) Tweakwise api documentation: http://developers.tweakwise.com/
3) General questions: https://www.tweakwise.com/contact/

For feature requests we refer to the links above.
For technical issues github is used. If you find a technical issue please create an issue on github and notify tweakwise via the links above. If you also happen to have the solution to that issue feel free to create a merge request.

### Compatibility
We strive to remain compatible with all Magento 2.X versions and the latest 2.X-1 version where X is the highest Magento official 'sub' release.
Currently X=3 hence we should be compatible with all 2.3 versions and the latest 2.2 version which is 2.2.10 (at the time of writing).
We do not actively drop support for versions below this range and will implement `minor` changes if that means we can remain compatible with versions below this range.
That being said if we can do a massive simplification of code at the cost of dropping support for version 2.1.Y we will do so.
We also refer to the magento software lifecycle: https://magento.com/sites/default/files/magento-software-lifecycle-policy.pdf.
Note that 2.2 is End Of Life.
