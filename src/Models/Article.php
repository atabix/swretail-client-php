<?php

namespace SWRetail\Models;

use SWRetail\Http\Client;
use SWRetail\Models\Article\Action;
use SWRetail\Models\Article\ActionArticles;
use SWRetail\Models\Article\ArticleChanged;
use SWRetail\Models\Article\Barcode;
use SWRetail\Models\Article\Category;
use SWRetail\Models\Article\Chunks;
use SWRetail\Models\Article\Image;
use SWRetail\Models\Article\MetaInfo;
use SWRetail\Models\Article\PriceInfo;
use SWRetail\Models\Article\Size;
use SWRetail\Models\Article\Stock;
use SWRetail\Models\Article\StockChanged;
use SWRetail\Models\Traits\UseDataMap;

class Article extends Model
{
    use UseDataMap;

    protected $id;

    protected $metaInfo;
    protected $priceInfo;
    protected $category;

    protected $sizes = [];
    protected $images = [];
    protected $actions = [];
    protected $fields = [];
    protected $relatedArticles = [
        'upsell'  => [],
        'crossell'=> [],
    ];
    private $barcodes = [];

    const DATAMAP = [
        'article_id'              => 'id',
        'article_inwebshop'       => 'in_webshop',
        'article_number'          => 'number',
        'article_season'          => 'season',
        'article_manufacturer'    => 'manufacturer',
        'article_artfabr'         => 'manufacturer_number',
        'article_freefield1'      => 'freefield1',
        'article_freefield2'      => 'freefield2',
        'article_color'           => 'color',
        'article_additional_info' => 'additional_info',
        'article_description'     => 'description',
        'article_memo'            => 'memo',
        'article_supplier'        => 'supplier',
        'article_weight'          => 'weight',
        'article_homepage'        => 'homepage',
        'article_outlet'          => 'outlet',
        'sizeruler'               => 'sizeruler',
    ];

    public function __construct($articleNumber = null, $season = 0, $id = null)
    {
        $this->data = new \stdClass();
        $this->metaInfo = new MetaInfo();
        $this->priceInfo = new PriceInfo();

        $this->setValue('number', $articleNumber);
        $this->setValue('season', $season);
        if ($id != 0) {
            $this->setValue('id', $id);
        }
    }

    /**
     * Get an existing Article from the API.
     *
     * @api
     *
     * @param int $id [description]
     *
     * @return self [description]
     */
    public static function get(int $id) : self
    {
        if ($id < 1) {
            throw new \InvalidArgumentException('Articles must have positive IDs.');
        }
        $path = 'article/' . $id;

        $response = Client::requestApi('GET', $path);
        $data = $response->json;

        $article = new static($data->article_number, $data->article_season);
        $article->parseData($data);

        return $article;
    }

    /**
     * Create a new Artice in the API.
     *
     * @api
     *
     * @return int The new article ID.
     */
    public function create()
    {
        $path = 'article';

        $data = $this->toApiRequest();

        $response = Client::requestApi('POST', $path, null, $data);
        $articleId = $response->json->article_id;

        return $articleId;
    }

    /**
     * Update an existing Article in the API.
     *
     * @api
     *
     * @param int $id SWRetail article ID.
     *
     * @return bool
     */
    public function update(int $id = null)
    {
        $id = $id ?? $this->id ?? $this->data->id;
        if ($id < 1) {
            throw new \InvalidArgumentException('Articles must have positive IDs.');
        }
        $path = 'article/' . $id;

        $data = $this->toApiRequest();

        $response = Client::requestApi('PUT', $path, null, $data);

        return  $response->json->status == 'ok';
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
        foreach ($data as $key => $value) {
            switch ($key) {
                // Meta
                case 'article_metatitle':
                case 'article_metadescription':
                case 'article_metakeywords':
                    $this->metaInfo->setValue(\substr($key, 12), $value);
                    break;
                // Price
                case 'article_price_web':
                case 'article_basepurprice':
                case 'article_baseprice':
                case 'article_discount':
                case 'article_price_web_discount':
                case 'article_price_wholesale':
                case 'article_taxrate':
                    $this->priceInfo->setMappedValue($key, $value);
                    break;
                // Category
                case 'article_group':
                case 'article_subgroup':
                case 'article_subsubgroup':
                    if ($this->category instanceof Category) {
                        break;
                    }
                    $this->setCategory(
                        $data->article_group,
                        $data->article_subgroup,
                        $data->article_subsubgroup ?? null
                    );
                    break;

                // lists of int
                case 'article_crossell':
                    $this->relatedArticles['crossell'] = $value;
                    break;
                case 'article_upsell':
                    $this->relatedArticles['upsell'] = $value;
                    break;

                case 'article_group_extra': // undocumented
                    // TODO []: each = [ "main", "sub", "subsub"] < any no value = empty string.
                    break;

                default:
                    if (! \array_key_exists($key, self::DATAMAP)) {
                        // ignore
                        break;
                    }
                    $this->setValue(self::DATAMAP[$key], $value);
            }
        }

        if (isset($data->barcodes)) {
            $this->parseSizes($data->sizes, $data->barcodes);
        }
        if (isset($data->images)) {
            $this->parseImages($data->images);
        }
        if (isset($data->article_actions)) {
            $this->parseActions($data->article_actions);
        }
        if (isset($data->fields)) {
            $this->parseFields($data->fields);
        }
    }

    public function setValue($key, $value)
    {
        switch ($key) {
            case 'weight':
                $this->data->$key = (float) $value;
                break;
            case 'id':
                $this->id = (int) $value;
                // no break.
            case 'number': // also constructor
            case 'season': // also constructor
            case 'sizeruler': // (doc says string...)
                $this->data->$key = (int) $value;
                break;
            case 'in_webshop':
            case 'outlet': // undocumented
            case 'homepage': // undocumented
                $this->data->$key = (bool) $value;
                break;
            case 'manufacturer_number':
            case 'description':
            case 'memo':
            case 'additional_info':
            case 'freefield1':
            case 'freefield2':
            case 'color':
            case 'supplier':
            case 'manufacturer':
                $this->data->$key = (string) $value;
                break;
            default:
                // ignore.
        }

        return $this;
    }

    private function parseSizes($sizeData, $barcodeData)
    {
        $this->data->barcodes = \array_reduce($barcodeData, function ($carry, $barcode) {
            $carry[$barcode->position] = $barcode->barcode;

            return $carry;
        }, []);

        foreach ($sizeData as $sizeValues) {
            $barcode = $this->data->barcodes[$sizeValues->position];
            $size = Size::barcode($barcode)->setMappedValues($sizeValues);
            $this->addSize($size);
        }
    }

    private function parseImages($imageData)
    {
        foreach ($imageData as $imageValues) {
            $image = (new Image())->setMappedValues($imageValues);
            $this->addImage($image);
        }
    }

    private function parseActions($actionData)
    {
        foreach ($actionData as $actionValues) {
            $action = (new Action())->setMappedValues($actionValues);
            $this->addAction($action);
        }
    }

    private function parseFields($fieldsData)
    {
        foreach ($fieldsData as $name => $value) {
            $this->fields[$name] = $value;
        }
    }

    public function setCategory($main, $sub = null, $subsub = null)
    {
        if ($main instanceof Category) {
            $this->category = $main;

            return $this;
        }
        $this->category = new Category($main, $sub, $subsub);

        return $this;
    }

    public function addSize(Size $size)
    {
        $position = $size->getPosition() ?? \max(\array_keys($this->sizes)) + 1;
        $this->sizes[$position] = $size;

        return $this;
    }

    public function addSizes(array $sizes)
    {
        foreach ($sizes as $size) {
            $this->addSize($size);
        }

        return $this;
    }

    public function addBarcode(Barcode $barcode)
    {
        $position = $barcode->getPosition() ?? \max(\array_keys($this->barcodes)) + 1;
        $this->barcodes[$position] = $barcode;

        return $this;
    }

    public function addImage(Image $image)
    {
        $this->images[] = $image;
    }

    public function addAction(Action $action)
    {
        $this->actions[] = $action;
    }

    public function setField($name, $value)
    {
        $this->fields[$name] = $value;
    }

    // --
    // RETRIEVE VALUES.
    //

    public function priceInfo(): PriceInfo
    {
        return $this->priceInfo;
    }

    public function metaInfo(): MetaInfo
    {
        return $this->metaInfo;
    }

    public function getCategory() : ?Category
    {
        return $this->category;
    }

    public function getSizes(): array
    {
        return $this->sizes;
    }
    
      public function getFields(): array
    {
        return $this->fields;
    }

    public function getImages(): array
    {
        return $this->images;
    }

    public function getActions(): array
    {
        return $this->actions;
    }

    public function toApiRequest()
    {
        $data = $this->mapDataToApiRequest();

        // category
        if ($this->category instanceof Category) {
            $data = $data + $this->category->toApiRequest();
        }

        // prices
        $data = $data + $this->priceInfo()->toApiRequest();

        // sizes/barcodes
        if (\count($this->sizes) > 0) {
            $data['barcodes'] = [];
            $data['sizes'] = [];
            foreach ($this->sizes as $size) {
                $data['barcodes'][] = $size->toApiRequest('barcodes');
                $data['sizes'][] = $size->toApiRequest('sizes');
            }
        } elseif (\count($this->barcodes) > 0) {
            $data['barcodes'] = [];
            foreach ($this->barcodes as $barcode) {
                $data['barcodes'][] = $barcode->toApiRequest();
            }
        }

        // custom fields (readonly!?)
        // if (count($this->fields) > 0) {
        //     $data['fields'] = [];
        //     foreach ($this->fields as $name => $value) {
        //         $data['fields'][$name] = $value;
        //     }
        // }

        return $data;
    }

    // Changed / Stock related.

    public static function allChanged($minutes) : ArticleChanged
    {
        return new ArticleChanged($minutes);
    }

    public static function stockChanged($minutes) : StockChanged
    {
        return new StockChanged($minutes);
    }

    public static function withActionsAt($date) : ActionArticles
    {
        return (new ActionArticles())->atDate($date);
    }

    public function stockAt($position, $warehouse = null) : Stock
    {
        $warehouseId = $warehouse instanceof Warehouse ? $warehouse->getId() : $warehouse;

        return new Stock($this->id, $position, $warehouseId);
    }

    public static function chunks()
    {
        return new Chunks();
    }
}
