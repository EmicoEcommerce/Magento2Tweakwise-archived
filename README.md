## Installation
Install package using composer
```sh
composer config repositories.emico composer https://repository.emico.nl/
composer config minimum-stability dev
composer require emico/tweakwise-export
```

Install package using zip file
```sh
Extract tweakwise-export.zip src folder to app/code/Emico/TweakwiseExport/
```

Run installers
```sh
php bin/magento setup:upgrade
php bin/magento setup:static-content:deploy
```

## Events
Currently there are no events documented, this will be done in the coming version(s).
