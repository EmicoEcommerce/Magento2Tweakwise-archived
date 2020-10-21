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

  
