<?php
/**
 * Tweakwise & Emico (https://www.tweakwise.com/ & https://www.emico.nl/) - All Rights Reserved
 *
 * @copyright Copyright (c) 2017-2017 Tweakwise.com B.V. (https://www.tweakwise.com)
 * @license   Proprietary and confidential, Unauthorized copying of this file, via any medium is strictly prohibited
 */

namespace Emico\Tweakwise\Model\Observer;

use Magento\Framework\Event\Observer;

class CatalogSearchRedirect extends AbstractProductNavigationRequestObserver
{
    /**
     * {@inheritdoc}
     */
    protected function _execute(Observer $observer)
    {
        $redirects = $this->context->getResponse()->getRedirects();
        if (!$redirects) {
            return;
        }


        $redirect = current($redirects);
        $url = $redirect->getUrl();
        if (strpos($url, 'http') !== 0) {
            $url = $this->urlBuilder->getUrl($url);
        }

        $this->getHttpResponse()->setRedirect($url);
    }
}
