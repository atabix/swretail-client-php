<?php

namespace SWRetail\Models\Relation;

use SWRetail\Models\Model;

class Address extends Model
{
    protected $street;
    protected $housenumber;
    protected $zipcode;
    protected $city;
    protected $country;

    public function setValue($key, $value)
    {
        $this->$key = $value;

        return $this;
    }

    public function setStreet($value, $housenumber = null)
    {
        $this->street = $value;
        if (! \is_null($housenumber)) {
            $this->setHousenumber($housenumber);
        }

        return $this;
    }

    public function setHousenumber($value)
    {
        $this->housenumber = $value;

        return $this;
    }

    public function setZipcode($value)
    {
        $this->zipcode = $value;

        return $this;
    }

    public function setCity($value, $country = null)
    {
        $this->city = $value;
        if (! \is_null($country)) {
            $this->setCountry($country);
        }

        return $this;
    }

    public function setCountry($value)
    {
        $this->country = $value;

        return $this;
    }

    public function getStreet()
    {
        return $this->street;
    }

    public function getHousenumber()
    {
        return $this->housenumber;
    }

    public function getZipcode()
    {
        return $this->zipcode;
    }

    public function getCity()
    {
        return $this->city;
    }

    public function getCountry()
    {
        return $this->country;
    }

    public function toApiRequest()
    {
        $fields = ['street', 'housenumber', 'zipcode', 'city', 'country'];
        $data = [];
        foreach ($fields as $property) {
            if (! empty($this->$property)) {
                $data[$property] = (string) $this->$property;
            }
        }

        return $data;
    }
}
