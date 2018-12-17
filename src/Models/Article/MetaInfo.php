<?php

namespace SWRetail\Models\Article;

use SWRetail\Models\Model;

class MetaInfo extends Model // ModelInfo
{
    protected $title;
    protected $description;
    protected $keywords;

    public function setValue($key, $value)
    {
        $this->$key = $value;

        return $this;
    }

    public function setTitle($value)
    {
        $this->title = $value;

        return $this;
    }

    public function setDescription($value)
    {
        $this->description = $value;

        return $this;
    }

    public function setKeywords($value)
    {
        $this->keywords = $value;

        return $this;
    }
}
