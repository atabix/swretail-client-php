<?php

namespace SWRetail\Models\Report;

use SWRetail\Http\Client;
use SWRetail\Models\Article;
use SWRetail\Models\CashRegister;
use SWRetail\Models\Store;
use SWRetail\Models\Traits\UseDataMap;
use SWRetail\Models\Type\Percentage;
use SWRetail\Models\Type\Price;

class ArticleSale extends DateReport
{
    use UseDataMap;

    // apikey => localkey
    const DATAMAP = [
        'cash_id'        => 'cash_register_id',
        'store_id'       => 'store_id',
        'receipt_number' => 'receipt_number',
        'article_id'     => 'article_id',

        'size_pos'       => 'position',
        'amount'         => 'amount',
        'linetotal'      => 'line_total',
        'discount'       => 'discount',
        'tax_percent'    => 'tax_rate',
        'employee'       => 'employee',
        'relation_code'  => 'relation_code',
        // 'article_link' => 'article_api_href',
    ];

    protected $filterType;
    protected $filterValue;

    public function __construct()
    {
        $this->data = new \stdClass();
    }

    /**
     * Set article as search filter.
     *
     * @param int|Article $article
     *
     * @return self
     */
    public function article($article) : self
    {
        $this->filterType = 'article_id';
        $this->filterValue = $article instanceof Article ? $article->getId() : (int) $article;

        return $this;
    }

    /**
     * Set receipt number as search filter.
     *
     * @param int $receipt
     *
     * @return self
     */
    public function receipt(int $receipt)
    {
        $this->filterType = 'receipt_number';
        $this->filterValue = $receipt;

        return $this;
    }

    /**
     * @api
     *
     * @return array[self]
     */
    public function getAll()
    {
        $this->requireDate();

        $path = 'report_article_sales/' . $this->formatDate();
        if (! empty($this->filterType)) {
            $path .= '/' . $this->filterType . '/' . $this->filterValue;
        }

        $response = Client::requestApi('GET', $path);

        $list = [];
        foreach ($response->json as $itemData) {
            $item = new static();
            $item->parseData($itemData);
            $list[] = $item;
        }

        return $list;
    }

    public function setValue($key, $value)
    {
        switch ($key) {
            case 'cash_register_id':
                $this->cashRegister = new CashRegister($value);
                break;
            case 'store_id':
                $this->store = new Store($value);
                break;
            case 'receipt_number':
            case 'article_id':
            case 'position':
            case 'amount':
                $this->data->$key = (int) $value;
                break;
            case 'line_total':
            case 'discount':
                $this->data->$key = new Price($value);
                break;
            case 'tax_rate':
                $this->data->$key = new Percentage($value);
                break;
            case 'employee':
            case 'relation_code':
            case 'article_api_href':
                $this->data->$key = (string) $value;
                break;
            default:
                // ignore
        }

        return $this;
    }
}
