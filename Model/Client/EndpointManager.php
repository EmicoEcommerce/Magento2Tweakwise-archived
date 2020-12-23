<?php

/**
 * @author : Edwin Jacobs, email: ejacobs@emico.nl.
 * @copyright : Copyright Emico B.V. 2020.
 */
namespace Emico\Tweakwise\Model\Client;

use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Variable\Model\ResourceModel\Variable as VariableResource;
use Magento\Variable\Model\Variable;
use Magento\Variable\Model\VariableFactory;

class EndpointManager
{
    public const DOWN_PERIOD = 300; // 5 minutes
    public const VARIABLE_NAME = '__tw_primary_last_down';

    public const SERVER_URL = 'https://gateway.tweakwisenavigator.net';
    public const FALLBACK_SERVER_URL = 'https://gateway.tweakwisenavigator.com';

    /**
     * @var VariableFactory
     */
    protected $variableFactory;

    /**
     * @var VariableResource
     */
    protected $variableResource;

    /**
     * @var DateTime
     */
    protected $dateTime;

    /**
     * @var Variable
     */
    protected $variable;

    /**
     * EndpointManager constructor.
     * @param VariableFactory $variableFactory
     * @param VariableResource $variableResource
     * @param DateTime $dateTime
     */
    public function __construct(
        VariableFactory $variableFactory,
        VariableResource $variableResource,
        DateTime $dateTime
    ) {
        $this->variableFactory = $variableFactory;
        $this->variableResource = $variableResource;
        $this->dateTime = $dateTime;
    }

    /**
     * @param bool $useFallback
     * @return string
     */
    public function getServerUrl(bool $useFallback = false): string
    {
        if ($useFallback) {
            return self::FALLBACK_SERVER_URL;
        }

        $downUntil = (int) $this->variable->getValue(Variable::TYPE_TEXT);
        if (!$downUntil) {
            return self::SERVER_URL;
        }

        if ($this->dateTime->gmtTimestamp() < $downUntil) {
            // Primary endpoint is considered "down", use fallback
            return self::FALLBACK_SERVER_URL;
        }

        return self::SERVER_URL;
    }

    /**
     *
     */
    public function handleConnectException(): void
    {
        $twPrimaryLastDown = $this->getVariable();
        $downUntil = (int) $twPrimaryLastDown->getValue(Variable::TYPE_TEXT);
        $fiveMinutesFromNow = $this->dateTime->gmtTimestamp() + self::DOWN_PERIOD;
        if ($downUntil && ($downUntil < $fiveMinutesFromNow)) {
            return;
        }

        $twPrimaryLastDown->setData('plain_value', $fiveMinutesFromNow);
        try {
            $this->variableResource->save($twPrimaryLastDown);
        } catch (AlreadyExistsException $e) {
            // Wont happen in practice, and if it does it is no good reason to halt execution
        }
    }

    /**
     * @return Variable
     */
    protected function getVariable(): Variable
    {
        if ($this->variable) {
            return $this->variable;
        }

        /** @var Variable $twPrimaryLastDown */
        $twPrimaryLastDown = $this->variableFactory->create();
        $this->variableResource->loadByCode(
            $twPrimaryLastDown,
            self::VARIABLE_NAME
        );

        if (!$twPrimaryLastDown->getCode()) {
            $twPrimaryLastDown->setCode(self::VARIABLE_NAME);
            $twPrimaryLastDown->setName('Tweakwise system flag, do not modify');
        }

        $this->variable = $twPrimaryLastDown;
        return $this->variable;
    }
}
