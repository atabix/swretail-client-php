<?php

namespace SWRetail\Models\Article;

use SWRetail\Http\Client;
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

    public function getDescription()
    {
        return $this->description;
    }

    public function getFilename()
    {
        return $this->file;
    }

    public function getPath()
    {
        return $this->link;
    }

    /**
     * @api
     *
     * @return string Binary data
     */
    public function getBinary()
    {
        $path = 'article_image/' . $this->file;

        $response = Client::requestApi('GET', $path);
        // expected json: { "error": "json_decode error: <...>", "body": "<raw binary data>" }

        return $response->json->body;
    }

    /**
     * @api
     *
     * @return string base64 encoded data
     */
    public function getEncodedData(/* $encoding = 'base64' */)
    {
        $binary = $this->getBinary();

        return \base64_encode($binary);
    }
}
