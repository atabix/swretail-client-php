<?php

namespace SWRetail\Models\Traits;

use BadMethodCallException;
use function SWRetail\snake_case;

trait UseDataMap
{
    protected $data;

    /**
     * Get & Set data.
     *
     * @param string $name      Called method name
     * @param array  $arguments
     *
     * @throws BadMethodCallException
     *
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        if (\substr($name, 0, 3) == 'get') {
            $propertyName = snake_case(\substr($name, 3));
            if (\in_array($propertyName, self::DATAMAP)) {
                return $this->data->$propertyName ?? null;
            }
        } elseif (\substr($name, 0, 3) == 'set') {
            $propertyName = snake_case(\substr($name, 3));
            if (\in_array($propertyName, self::DATAMAP)) {
                $value = \reset($arguments);

                return $this->setValue($propertyName, $value);
            }
        }

        throw new BadMethodCallException("Call to undefined method '$name' in " . __FILE__ . ':' . __LINE__);
    }

    /**
     * @return array
     */
    protected function mapDataToApiRequest()
    {
        $map = \array_flip(self::DATAMAP);
        $data = [];
        foreach ($this->data as $key => $value) {
            if (! \array_key_exists($key, $map)) {
                \user_error("Invalid key: $key", \E_USER_NOTICE);
                continue;
            }
            if (\is_null($value)) {
                continue;
            }
            $apiKey = $map[$key];
            $data[$apiKey] = $this->getApiValue($key, $value);
        }

        return $data;
    }

    /**
     * Fallback definition to parse retrieved api data.
     * Override to handle keys for specific types.
     *
     * @param array $data
     */
    public function parseData(array $data)
    {
        foreach ($data as $key => $value) {
            if (! \array_key_exists($key, self::DATAMAP) || \is_null($value)) {
                // ignore
                break;
            }
            $this->setValue(self::DATAMAP[$key], $value);
        }
    }

    /**
     * Fallback definition to set a Data value.
     * Override to map keys to types or make exceptions.
     *
     * @param string $key
     * @param mixed  $value
     *
     * @return self
     */
    public function setValue($key, $value)
    {
        $this->data->$key = $value;
    
        return $this;
    }
    
    /**
     * Fallback definition to get the Api value of a datamap item.
     * Override to map keys from types or make exceptions.
     *
     * @param string $key
     * @param mixed  $value
     *
     * @return mixed
     */
    protected function getApiValue($key, $value)
    {
        return $value;
    }
}
