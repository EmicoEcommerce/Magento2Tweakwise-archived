<?php
/**
 * @author Bram Gerritsen <bgerritsen@emico.nl>
 * @copyright (c) Emico B.V. 2019
 */

namespace Emico\Tweakwise\Api\Data;

interface AttributeSlugInterface
{
    public const ATTRIBUTE = 'attribute';
    public const SLUG = 'slug';

    /**
     * @return string
     */
    public function getAttribute(): string;

    /**
     * @return string
     */
    public function getSlug(): string;
}