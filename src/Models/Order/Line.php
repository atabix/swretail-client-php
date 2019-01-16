<?php

namespace SWRetail\Models\Order;

use SWRetail\Models\Article;
use SWRetail\Models\Article\Barcode;
use SWRetail\Models\Model;
use SWRetail\Models\Traits\UseDataMap;
use function SWRetail\price_or_percentage;

class Line extends Model
{
    use UseDataMap;

    protected $order;
    public $article;
    public $barcode;

    // apikey => localkey
    const DATAMAP = [
        'order_id'      => 'order_id',
        'article_id'    => 'article_id',
        'size_pos'      => 'position',
        'article_price' => 'unit_price',
        'discount'      => 'discount',
        'linetotal'     => 'line_total',
        'taxrate'       => 'tax_rate',
        'non_pickable'  => 'non_pickable',
        'amount'        => 'amount',
        'size'          => 'size',
    ];

    public function __construct()
    {
        $this->data = new \stdClass();
    }

    /**
     * Create a Line based on an Article.
     *
     * @param Article $article
     * @param int     $position
     * @param int     $amount
     *
     * @return self
     */
    public static function fromArticle(Article $article, int $position = 1, int $amount = 1) : self
    {
        $line = new static();
        $line->setArticle($article);
        $line->setAmount($amount);
        $line->setPosition($position);
        $line->setTaxRate($article->priceInfo()->getTaxRate());

        return $line;
    }

    /**
     * Create a Line based on a Barcode. (No article is fetched here).
     *
     * @param Barcode $barcode
     * @param int     $amount
     *
     * @return self
     */
    public static function fromBarcode(Barcode $barcode, int $amount = 1) : self
    {
        $line = new static();
        $line->setAmount($amount);
        $line->setBarcode($barcode);

        return $line;
    }

    /**
     * Create a Line with a custom (non-inventory) Article.
     *
     * @param string $description
     * @param int    $amount
     *
     * @return self
     */
    public static function freeArticle(string $description, int $amount) : self
    {
        $line = new static();
        $line->setAmount($amount);
        $line->setPosition(1);

        $article = new Article(null, null, -1);
        $article->setDescription($description);
        $line->setArticle($article);

        return $line;
    }

    /**
     * Parse data from API get() call.
     *
     * @param [type] $data [description]
     *
     * @return [type] [description]
     */
    public function parseData($data)
    {
        $this->parseArticle($data);

        foreach ($data as $key => $value) {
            switch ($key) {
                default:
                    if (! \array_key_exists($key, self::DATAMAP) || \is_null($value)) {
                        // ignore
                        break;
                    }
                    $this->setValue(self::DATAMAP[$key], $value);
            }
        }
    }

    protected function parseArticle($data)
    {
        $this->article = new Article($data->article_number, $data->article_season, $data->article_id);
        $articleData = (object) \array_filter((array) $data, function ($key) {
            return \substr($key, 0, 8) == 'article_';
        }, \ARRAY_FILTER_USE_KEY);
        $this->article->parseData($articleData);
    }

    public function setValue($key, $value)
    {
        switch ($key) {
            case 'non_pickable':
                $this->data->$key = (bool) $value;
                break;
            case 'order_id':
            case 'article_id':
            case 'amount':
                $this->data->$key = (int) $value;
                break;
            case 'unit_price':
            case 'discount':
            case 'tax_rate':
                $this->data->$key = price_or_percentage($value);
                break;
            case 'line_total':
                $this->data->$key = price_or_percentage($value);
                $this->data->$key->decimals = 4;
                break;
            default:
                $this->data->$key = (string) $value;
        }

        return $this;
    }

    public function setArticle(Article $article)
    {
        $this->article = $article;
        $this->setValue('article_id', $article->getId());

        return $this;
    }

    public function setBarcode(Barcode $barcode)
    {
        $this->barcode = $barcode;

        return $this;
    }

    /**
     * Set the orderline Discount + LineTotalIncludingDiscount.
     *
     * @param Price|Percentage|float $discount
     * @param Price|Percentage|float $lineTotal
     */
    public function setDiscount($discount, $lineTotal)
    {
        return $this->setValue('discount', $discount)
            ->setValue('line_total', $lineTotal);
    }

    protected function getApiValue($key, $value)
    {
        switch ($key) {
            case 'article_id':
            case 'amount':
            case 'position':
            case 'non_pickable':
                return (int) $value;
            default:
                return (string) $value;
        }
    }

    public function article()
    {
        return $this->article;
    }

    public function toApiRequest()
    {
        $data = $this->mapDataToApiRequest();

        if ($this->barcode instanceof Barcode) {
            $data['article_barcode'] = (string) $this->barcode;
        } elseif ($this->article->getId() == -1) {
            $data['article_description'] = $this->article->getDescription();
        }

        return $data;
    }
}
