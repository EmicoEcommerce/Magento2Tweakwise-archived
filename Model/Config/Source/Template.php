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
use Emico\Tweakwise\Model\Client\Response\Catalog\TemplateResponse;
use Magento\Framework\Data\OptionSourceInterface;

class Template implements OptionSourceInterface
{
    /**
     * @var Client
     */
    protected $client;

    /**
     * @var RequestFactory
     */
    protected $requestFactory;

    /**
     * @var array
     */
    protected $options;

    /**
     * Template constructor.
     *
     * @param Client $client
     * @param RequestFactory $requestFactory
     */
    public function __construct(Client $client, RequestFactory $requestFactory)
    {
        $this->client = $client;
        $this->requestFactory = $requestFactory;
    }

    /**
     * @return array
     */
    protected function buildOptions()
    {
        $request = $this->requestFactory->create();
        /** @var TemplateResponse $response */
        $response = $this->client->request($request);
        $result = [
            ['value' => null, 'label' => __('* Default template')],
        ];
        foreach ($response->getTemplates() as $template) {
            $result[] = ['value' => $template->getTemplateId(), 'label' => $template->getName()];
        }
        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
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