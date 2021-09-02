<?php
/**
 * @author : Edwin Jacobs, email: ejacobs@emico.nl.
 * @copyright : Copyright Emico B.V. 2019.
 */

namespace Emico\Tweakwise\Model\Config\Source;

use Emico\Tweakwise\Exception\ApiException;
use Emico\Tweakwise\Model\Client;
use Emico\Tweakwise\Model\Client\RequestFactory;
use Emico\Tweakwise\Model\Client\Response\Catalog\LanguageResponse;
use Exception;
use Magento\Framework\Data\OptionSourceInterface;

class Language implements OptionSourceInterface
{
    /**
     * @var RequestFactory
     */
    protected RequestFactory $requestFactory;

    /**
     * @var Client
     */
    protected Client $client;

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
     * @throws Exception
     */
    public function toOptionArray(): array
    {
        $options = [
            [
                'label' => 'Don\'t use language in search',
                'value' => ''
            ]
        ];

        try {
            $request = $this->requestFactory->create();
            /** @var LanguageResponse $response */
            $response = $this->client->request($request);

            $languages = $response->getLanguages();

            foreach ($languages as $language) {
                $options[] = [
                    'label' => $language['name'],
                    'value' => $language['key']
                ];
            }
        } catch (ApiException $e) { }

        return $options;
    }
}
