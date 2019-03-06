<?php
/**
 * Tweakwise & Emico (https://www.tweakwise.com/ & https://www.emico.nl/) - All Rights Reserved
 *
 * @copyright Copyright (c) 2017-2017 Tweakwise.com B.V. (https://www.tweakwise.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Emico\Tweakwise\Model\Client\Request;

use Emico\Tweakwise\Model\Client\Request;
use Emico\Tweakwise\Model\Client\Response\AutocompleteResponse;

class AutocompleteRequest extends Request
{
    /**
     * {@inheritDoc}
     */
    protected $path = 'autocomplete';

    /**
     * {@inheritDoc}
     */
    public function getResponseType()
    {
        return AutocompleteResponse::class;
    }

    /**
     * @param string $query
     * @return $this
     */
    public function setSearch($query)
    {
        $this->setParameter('tn_q', $query);
        return $this;
    }

    /**
     * @param bool $getProducts
     * @return $this
     */
    public function setGetProducts($getProducts)
    {
        $this->setParameter('tn_items', $getProducts ? 'true' : 'false');

        return $this;
    }

    /**
     * @param bool $getSuggestions
     * @return $this
     */
    public function setGetSuggestions($getSuggestions)
    {
        $this->setParameter('tn_suggestions', $getSuggestions ? 'true' : 'false');

        return $this;
    }

    /**
     * @param bool $isInstant
     * @return $this
     */
    public function setIsInstant($isInstant)
    {
        $this->setParameter('tn_instant', $isInstant ? 'true' : 'false');

        return $this;
    }

    /**
     * @param string $query
     * @return $this
     */
    public function setQuery($query)
    {
        $this->setParameter('tn_q', (string) $query);

        return $this;
    }

    /**
     * @param int $maxResult
     */
    public function setMaxResult($maxResult)
    {
        $maxResult = (int) $maxResult;
        if ($maxResult == 0) {
            $maxResult = null;
        }
        $this->setParameter('tn_maxresults', $maxResult);
    }
}