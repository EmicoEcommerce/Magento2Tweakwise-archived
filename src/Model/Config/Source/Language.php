<?php
/**
 * @author : Edwin Jacobs, email: ejacobs@emico.nl.
 * @copyright : Copyright Emico B.V. 2019.
 */

namespace Emico\Tweakwise\Model\Config\Source;

use Emico\Tweakwise\Model\Client;
use Emico\Tweakwise\Model\Client\RequestFactory;
use Magento\Framework\Data\OptionSourceInterface;

class Language implements OptionSourceInterface
{
    /**
     * @var RequestFactory
     */
    protected $requestFactory;

    /**
     * @var Client
     */
    protected $client;

    /**
     * Language constructor.
     * @param RequestFactory $requestFactory
     * @param Client $client
     */
    public function __construct(
        RequestFactory $requestFactory,
        Client $client
    ) {
        $this->requestFactory = $requestFactory;
        $this->client = $client;
    }

    /**
     * Return array of options as value-label pairs
     *
     * @return array
     */
    public function toOptionArray()
    {
        $request = $this->requestFactory->create();
        $response = $this->client->request($request);

        $language = $response->getValue('language');
        $languageName = $language['name'];
        $languageValue = $language['key'];

        return [
            ['label' => 'Don\'t use language in search', 'value' => ''],
            ['label' => $languageName, 'value' => $languageValue]
        ];
    }
}