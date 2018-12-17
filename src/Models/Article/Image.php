<?php

namespace SWRetail\Models\Article;

use SWRetail\Models\Model;

class Image extends Model
{
    protected $description;
    protected $file;
    protected $order;
    protected $link;

    /**
     * Set values from API response data.
     *
     * @param object|array $values [description]
     *
     * @return self
     */
    public function setMappedValues($values): self
    {
        foreach ($values as $apiKey => $value) {
            $property = \substr($apiKey, 6); // 'image_'
            if (! \property_exists($this, $property)) {
                throw new \InvalidArgumentException('Invalid map key');
            }

            if ($property == 'order') {
                $value = (int) $value;
            }

            $this->$property = $value;
        }

        return $this;
    }

    public function getPosition()
    {
        return $this->order;
    }
}
