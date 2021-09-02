<?php
/**
 * Tweakwise & Emico (https://www.tweakwise.com/ & https://www.emico.nl/) - All Rights Reserved
 *
 * @copyright Copyright (c) 2017-2017 Tweakwise.com B.V. (https://www.tweakwise.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Emico\Tweakwise\Model\Client\Type;

use BadMethodCallException;

class Type
{
    /**
     * Setter/Getter underscore transformation cache
     *
     * @var array
     */
    protected static array $_underscoreCache = [];

    /**
     * Setter/Getter underscore transformation cache
     *
     * @var array
     */
    protected static array $_capitalizeCache = [];

    /**
     * @var array
     */
    protected array $data = [];

    /**
     * Type constructor.
     *
     * @param array $data
     */
    public function __construct(array $data = [])
    {
        $this->setData($data);
    }

    /**
     * Converts field names for setters and getters
     *
     * $this->setMyField($value) === $this->setData('my_field', $value)
     * Uses cache to eliminate unnecessary preg_replace
     *
     * @param string $name
     * @return string
     */
    protected function underscore(string $name): string
    {
        if (isset(self::$_underscoreCache[$name])) {
            return self::$_underscoreCache[$name];
        }
        $result = strtolower(trim(preg_replace('/([A-Z]|[0-9]+)/', "_$1", $name), '_'));
        self::$_underscoreCache[$name] = $result;
        return $result;
    }

    /**
     * @param string $key
     * @return string
     */
    protected function getMethodName(string $key): string
    {
        if (isset(self::$_capitalizeCache[$key])) {
            return self::$_capitalizeCache[$key];
        }
        $result = str_replace(' ', '', ucwords(str_replace('_', ' ', $key)));
        self::$_capitalizeCache[$key] = $result;
        return $result;
    }

    /**
     * @param array $data
     * @return $this
     */
    public function setData(array $data): static
    {
        //$this->data = [];
        foreach ($data as $key => $value) {
            $this->setValue($key, $value);
        }

        return $this;
    }

    /**
     * @param string $key
     * @return mixed
     */
    public function getValue(string $key): mixed
    {
        $method = 'get' . $this->getMethodName($key);
        if (method_exists($this, $method)) {
            return $this->$method();
        }

        return $this->getDataValue($key);
    }

    /**
     * @param string $key
     * @return mixed|null
     */
    protected function getDataValue(string $key): mixed
    {
        return $this->data[$key] ?? null;
    }

    /**
     * @param string $key
     * @return bool
     */
    public function getBoolValue(string $key): bool
    {
        return $this->getDataValue($key) == 'true';
    }

    /**
     * @param string $key
     * @param mixed $value
     * @return $this
     */
    public function setValue(string $key, mixed $value): static
    {
        $method = 'set' . $this->getMethodName($key);
        if (method_exists($this, $method)) {
            $this->$method($value);
        } else {
            $this->data[$key] = $value;
        }

        return $this;
    }

    /**
     * @param string $key
     * @return bool
     */
    public function hasValue(string $key): bool
    {
        return isset($this->data[$key]);
    }

    /**
     * @param string $method
     * @param array $args
     * @return mixed
     */
    public function __call(string $method, array $args)
    {
        if (strlen($method) > 3) {
            $key = $this->underscore(substr($method, 3));
            switch (substr($method, 0, 3)) {
                case 'get':
                    return $this->getValue($key);
                case 'set':
                    $value = $args[0] ?? null;
                    return $this->setValue($key, $value);
                case 'has':
                    return $this->hasValue($key);
            }
        }

        throw new BadMethodCallException(sprintf('Invalid method %s::%s', get_class($this), $method));
    }

    /**
     * @param array $data
     * @param string $key
     * @return array
     */
    protected function normalizeArray(array $data, string $key): array
    {
        if (isset($data[$key])) {
            $data = $data[$key];
        }

        if (empty($data)) {
            return [];
        }

        if (!is_array($data) || !isset($data[0])) {
            $data = [$data];
        }

        return $data;
    }
}
