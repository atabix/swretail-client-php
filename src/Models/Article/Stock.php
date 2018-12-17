<?php

namespace SWRetail\Models\Article;

use SWRetail\Http\Client;
use SWRetail\Models\Model;

class Stock extends Model
{
    protected $article_id;
    protected $position;
    protected $warehouse_id;

    public function __construct(int $articleId, int $position, int $warehouseId = null)
    {
        $this->article_id = $articleId;
        $this->position = $position;
        $this->warehouse_id = $warehouseId;
    }

    /**
     * @api
     *
     * @return int The stock.
     */
    public function get()
    {
        $path = 'article_stock/' . $this->article_id . '/' . $this->position;
        if ($this->warehouse_id > 0) {
            $path .= '/' . $this->warehouse_id;
        }

        $response = Client::requestApi('GET', $path);

        $stock = $response->json->stock;

        return (int) $stock;
    }

    /**
     * @api
     *
     * @param int $amount The new stock amount.
     *
     * @return bool
     */
    public function set(int $amount)
    {
        if ($this->warehouse_id < 1) {
            throw new \InvalidArgumentException('Warehouse ID must be a positive integer.');
        }
        $path = 'article_stock/' . $this->article_id . '/' . $this->position
            . '/' . $this->warehouse_id . '/' . $amount;

        $response = Client::requestApi('POST', $path, null, null);

        return $response->json->status == 'ok';
    }
}
