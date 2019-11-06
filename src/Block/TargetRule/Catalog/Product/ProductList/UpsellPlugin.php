<?php
/**
 * @author : Edwin Jacobs, email: ejacobs@emico.nl.
 * @copyright : Copyright Emico B.V. 2019.
 */


namespace Emico\Tweakwise\Block\TargetRule\Catalog\Product\ProductList;

/**
 * Magento docs say that you can register a virtualType as plugin, you can't.
 * We need this class to
 *
 * Class UpsellPlugin
 * @package Emico\Tweakwise\Block\TargetRule\Catalog\Product\ProductList
 */
class UpsellPlugin extends Plugin
{
    protected $type = 'upsell';
}
