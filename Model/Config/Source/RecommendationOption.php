<?php
/**
 * Tweakwise & Emico (https://www.tweakwise.com/ & https://www.emico.nl/) - All Rights Reserved
 *
 * @copyright Copyright (c) 2017-2017 Tweakwise.com B.V. (https://www.tweakwise.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Emico\Tweakwise\Model\Config\Source;

use Emico\Tweakwise\Exception\ApiException;
use Emico\Tweakwise\Model\Client;
use Emico\Tweakwise\Model\Client\RequestFactory;
use Emico\Tweakwise\Model\Client\Response\Catalog\Recommendation\OptionsResponse;
use Exception;
use Magento\Eav\Model\Entity\Attribute\Source\AbstractSource;

class RecommendationOption extends AbstractSource
{
    /**
     * Option to use when expecting a code instead of template id
     */
    public const OPTION_CODE = -1;
    public const OPTION_EMPTY = null;

    /**
     * @var Client
     */
    protected Client $client;

    /**
     * @var RequestFactory
     */
    protected RequestFactory $requestFactory;

    /**
     * @var array
     */
    protected array $options;

    /**
     * @var bool
     */
    protected bool $addCodeOption;

    /**
     * @var bool
     */
    protected bool $addEmpty;

    /**
     * Template constructor.
     *
     * @param Client $client
     * @param RequestFactory $requestFactory
     * @param bool $addCodeOption
     * @param bool $addEmpty
     */
    public function __construct(
        Client         $client,
        RequestFactory $requestFactory,
        bool           $addCodeOption,
        bool $addEmpty
    ) {
        $this->client = $client;
        $this->requestFactory = $requestFactory;
        $this->addCodeOption = $addCodeOption;
        $this->addEmpty = $addEmpty;
    }

    /**
     * @return array
     * @throws Exception
     */
    protected function buildOptions(): array
    {
        $request = $this->requestFactory->create();
        /** @var OptionsResponse $response */
        $response = $this->client->request($request);

        $result = [];

        if ($this->addEmpty) {
            $result[] = [
                'value' => self::OPTION_EMPTY,
                'label' => ' '
            ];
        }

        if ($this->addCodeOption) {
            $result[] = [
                'value' => self::OPTION_CODE,
                'label' => __('- Group code -')
            ];
        }

        foreach ($response->getRecommendations() as $recommendation) {
            $result[] = [
                'value' => $recommendation->getId(),
                'label' => $recommendation->getName()
            ];
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function getAllOptions(): array
    {
        if (!$this->options) {
            try {
                $options = $this->buildOptions();
            } catch (ApiException $e) {
                $options = [];
            }
            $this->options = $options;
        }

        return $this->options;
    }
}
