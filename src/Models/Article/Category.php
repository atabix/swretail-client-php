<?php

namespace SWRetail\Models\Article;

use SWRetail\Models\Model;

class Category extends Model
{
    protected $data;

    public function __construct($main = null, $sub = null, $subsub = null)
    {
        $this->data = [];
        foreach ([$main, $sub, $subsub] as $level) {
            $this->data[] = (string) $level;
        }
    }

    public function toApiRequest()
    {
        return \array_filter(\array_combine([
            'article_group',
            'article_subgroup',
            'article_subsubgroup',
        ], $this->data));
    }

    public function __toString()
    {
        return \implode(' | ', $this->get());
    }

    public function get()
    {
        return \array_filter($this->data);
    }
}
