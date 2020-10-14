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

  
