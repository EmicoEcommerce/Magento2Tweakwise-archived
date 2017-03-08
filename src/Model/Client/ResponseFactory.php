<?php
/**
 * Tweakwise & Emico (https://www.tweakwise.com/ & https://www.emico.nl/) - All Rights Reserved
 *
 * @copyright Copyright (c) 2017-2017 Tweakwise.com B.V. (https://www.tweakwise.com)
 * @license   Proprietary and confidential, Unauthorized copying of this file, via any medium is strictly prohibited
 */

namespace Emico\Tweakwise\Model\Client;

use Emico\Tweakwise\Exception\UnexpectedValueException;
use Magento\Framework\ObjectManagerInterface;

class ResponseFactory
{
    /**
     * Object Manager instance
     *
     * @var ObjectManagerInterface
     */
    protected $objectManager = null;

    /**
     * Factory constructor
     *
     * @param ObjectManagerInterface $objectManager
     */
    public function __construct(ObjectManagerInterface $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    /**
     * Create class instance with specified parameters
     *
     * @param Request $request
     * @param array $data
     * @return Response
     */
    public function create(Request $request, array $data)
    {
        $responseType = $request->getResponseType();
        $response = $this->objectManager->create($responseType, ['request' => $request, 'data' => $data]);
        if (!$response instanceof Response) {
            throw new UnexpectedValueException(sprintf('%s is not an instanceof %s', $responseType, Response::class));
        }

        return $response;
    }
}