## 3.2.0
Moved src folder to root and removed composer json symlink. [#133](https://github.com/EmicoEcommerce/Magento2Tweakwise/issues/133) If you have any patches (or something like that) you need to evaluate those.
This means that the directory structure of the module changed!

Added ext-json and ext-pcre to composer json.

Feature: 

Category filter urls now remember selected filters meaning that any selected filters should be remembered when navigating to a subcategory.

BugFixes:
1) Pager link "1" would not work when ajax filtering is enabled.
2) When category is not an anchor category this could result in a js error.
3) Removed jquery/ui reference in swatches template, this was done to reflect a change introduced in magento 2.3.3.

## 3.1.1
Fixed argument type definition error

## 3.1.0
Added new [suggestion api implementation](https://developers.tweakwise.com/#/Suggestions).
This is disabled by default you can enable it under Stores > Configuration > Catalog > Tweakwise > Autocomplete > Use suggestions autocomplete. Added guzzle client for asynchronous requests since we need multiple requests to get the new autocomplete results. 
The result will be a combination of search suggestions (these will send the customer to the search page), category suggestions (these will send the customer to the category page) and Category + Facet combinations which will send the customer to the category page with a filter preselected. 
For more information on this we refer to tweakwise customer support.

## 3.0.4
Fixed issue [#129](https://github.com/EmicoEcommerce/Magento2Tweakwise/issues/129).

When url path strategy is enabled the url matching is more strict. The case is as follows: With path strategy enabled filter urls are constructed as
https://site.com/category/filterName/filterValue, this will render the category with filter "filterName" selected at the correct value. The url matching is done by looking at the current request path and checking if there is any part of the request path that corresponds to a category url (or landingpage if that module is enabled), if so we render the longest category match and treat the remaining part of the request path as filters. The resulting behaviour is that https://site.com/category/any-nonsense yields a 200 O.K because the path strategy matches this url. This is because we cannot know in advance what the valid filters are (this is in part due to derived filters). This release features a partial fix to this issue, since each filter consists of a filterName and a filterValue there should at least be an even number of path segments in the remaining request path. If this is not the case we dont match the url anymore.

## 3.0.3
Fixed potential warning when matching urls. This warning was emitted when the url request path was very short (4 or less characters).

## 3.0.2
Added swatch resolver for derived color filters in tweakwise, this fixes [#126](https://github.com/EmicoEcommerce/Magento2Tweakwise/issues/126)
Possible issues with this: It is unclear from the navigator response which magento attribute (if any) was used to created the derived property,
as such we cannot know which swatches to load. We do a guess based on the swatch labels but this could lead to missing swatches. In order to find a match
for derived swatch with label "Red" in tweakwise magento needs to have a swatch attribute with an option labeled "Red", case sensitive.

## 3.0.1
BugFix: Release 3.0.0 introduced an error where the magento autocomplete template was loaded instead of the tweakwise autocomplete template.

## 3.0.0
1) Ajax filtering implemented
2) methods and property visibility updated to "protected" (to allow easier preferences).

BC breaks:
1) Template src/view/frontend/templates/product/navigation/view.phtml moved to src/view/frontend/templates/layer/view.phtml
as that is the template it replaces
2) Complete overhaul of js components, if you have any changes in those you will need to redo them.
3) Overhauled slider template: src/view/frontend/templates/product/layered/slider.phtml the javascript part has been moved to a separate js component, namely
src/view/frontend/web/js/navigation-slider.js
4) Removed deprecated methods from src/Block/LayeredNavigation/RenderLayered/SliderRenderer.php
5) All data-mage-init statements now go through model: src/Model/NavigationConfig.php this class will resolve the correct js components based on your configuration
This means yet more bc breaks on your template overrides (if any)
6) References to "Zend\Http\Request as HttpRequest" have been removed, we now depend on the concrete magento request object. This is because of the move from zend to laminas.
7) Added methods
```php
public function getClearUrl(MagentoHttpRequest $request, array $activeFilterItems): string;
public function buildFilterUrl(MagentoHttpRequest $request, array $filters = []): string;
```
To interface UrlInterface to facilitate ajax filtering

Furthermore: various code style fixes, simplifications and general "cleanup".

  
