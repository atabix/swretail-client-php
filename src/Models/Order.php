<?php

namespace SWRetail\Models;

use Carbon\Carbon;
use SWRetail\Http\Client;
use SWRetail\Http\Response;
use SWRetail\Models\Order\Line;
use SWRetail\Models\Order\OrderChanged;
use SWRetail\Models\Relation\Type;
use SWRetail\Models\Traits\UseDataMap;
use function SWRetail\price_or_percentage;

class Order extends Model
{
    use UseDataMap;

    protected $id;
    protected $orderNumber;

    protected $relationShipping;
    protected $relationInvoice;

    protected $lines;

    // apikey => localkey
    const DATAMAP = [
        'inetnumber'            => 'order_number',
        'web_state'             => 'status',
        'date'                  => 'date',
        'payment_method'        => 'payment_method',
        'relation_code_ship'    => 'ship_to',
        'relation_code_invoice' => 'invoice_to',
        'wholesale'             => 'wholesale',
        'shipper'               => 'shipper',
        'remark'                => 'remark',
        'time'                  => 'time',
        'tracker'               => 'tracker',
        'payment_status'        => 'payment_status',
        'shipping_value'        => 'shipping_cost',

        'order_id'      => 'id',
        'invoicenumber' => 'invoice_number',
        // 'linked_id' => ..,
        // 'swretail_state' => ..,
    ];

    public function __construct($orderNumber)
    {
        $this->orderNumber = $orderNumber;
        $this->data = new \stdClass();
        $this->lines = [];
    }

    public static function get(int $id)
    {
        if ($id < 1) {
            throw new \InvalidArgumentException('Id must be a positive integer.');
        }
        $path = 'order/' . $id;

        $response = Client::requestApi('GET', $path);

        return self::handleFindResponse($response);
    }

    public static function delete(int $id)
    {
        if ($id < 1) {
            throw new \InvalidArgumentException('Id must be a positive integer.');
        }
        $path = 'order/' . $id;

        $response = Client::requestApi('DELETE', $path);

        return $response->json->status == 'ok';
    }

    public static function byWebId($orderNumber)
    {
        if (empty($orderNumber)) {
            throw new \InvalidArgumentException('External Id must not be empty.');
        }
        $path = 'order/web/' . $orderNumber;

        $response = Client::requestApi('GET', $path);

        return self::handleFindResponse($response);
    }

    /**
     * Handle search responses for a single result.
     *
     * @param Response $response
     *
     * @return self
     */
    private static function handleFindResponse(Response $response) : self
    {
        $data = $response->json;

        $order = new static($data->inetnumber);
        $order->parseData($data);

        return $order;
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
                case 'order_lines':
                    break;
                case 'paymethod':
                    $this->setValue('payment_method', $value);
                    break;
                default:
                    if (! \array_key_exists($key, self::DATAMAP)) {
                        // ignore
                        break;
                    }
                    $this->setValue(self::DATAMAP[$key], $value);
            }
        }

        $this->parseLines($data->order_lines);
    }

    protected function parseLines($dataLines)
    {
        foreach ($dataLines as $dataLine) {
            $line = new Line();
            $line->parseData($dataLine);

            if ($this->getShipper() == $line->article()->getDescription()) {
                $this->setShippingCost($line->getLineTotal());
                continue;
            }

            $this->addLine($line);
        }
    }

    public function addLine(Line $line)
    {
        $this->lines[] = $line;
    }

    public function addLines(array $lines)
    {
        foreach ($lines as $line) {
            $this->addLine($line);
        }
    }

    public function setValue($key, $value)
    {
        switch ($key) {
            case 'id':
                $this->id = (int) $value;
                break;
            case 'inetnumber':
                $this->orderNumber = (string) $value;
                break;
            case 'wholesale':
                $this->data->$key = (bool) $value;
                break;
            case 'invoice_number':
                $this->data->$key = (int) $value;
                break;
            case 'date':
            case 'time':
                $this->data->$key = (string) $value;
                break;
            case 'ship_to':
                $this->relationShipping = $value instanceof Relation ? $value : new Relation(Type::CUSTOMER, $value);
                break;
            case 'invoice_to':
                $this->relationInvoice = $value instanceof Relation ? $value : new Relation(Type::CUSTOMER, $value);
                break;
            case 'shipping_cost':
                $this->data->$key = price_or_percentage($value);
                break;
            default:
                $this->data->$key = (string) $value;
        }

        return $this;
    }

    protected function getApiValue($key, $value)
    {
        switch ($key) {
            case 'wholesale':
                return (int) $value;
            // case 'date':
            //     return $this->data->$key->toDateString();
            default:
                return (string) $value;
        }
    }

    /**
     * Create a new Artice in the API.
     *
     * If orderNumber does exist, that Order will be updated instead.
     *
     * @api
     *
     * @return int The SW order ID.
     */
    public function create()
    {
        $path = 'order';

        $data = $this->toApiRequest();

        $response = Client::requestApi('POST', $path, null, $data);

        $orderId = $response->json->additional->swretail_order_id;

        return $orderId;
    }

    /**
     * Update an existing Article in the API.
     *
     * If orderNumber does not exist, an Order will be created instead.
     *
     * @api
     *
     * @return int The SW order ID.
     */
    public function update()
    {
        $path = 'order';

        $data = $this->toApiRequest();
        // Do not send lines with update.
        unset($data['order_lines']);

        $response = Client::requestApi('POST', $path, null, $data);

        $orderId = $response->json->additional->swretail_order_id;

        return $orderId;
    }

    public function toApiRequest()
    {
        $data = $this->mapDataToApiRequest();

        $data['date'] = Carbon::parse($this->data->date)->format('Y-m-d');
        $data['time'] = Carbon::parse($this->data->date)->format('H:i');
        $data['inetnumber'] = $this->orderNumber;
        $data['relation_code_ship'] = $this->relationShipping->getCode();
        $data['relation_code_invoice'] = $this->relationInvoice->getCode();

        $data['order_lines'] = [];
        foreach ($this->lines as $line) {
            $data['order_lines'][] = $line->toApiRequest();
        }

        return $data;
    }

    /**
     * Find changed orders.
     *
     * @param int $minutes
     *
     * @return OrderChanged
     */
    public static function allChanged($minutes) : OrderChanged
    {
        return new OrderChanged($minutes);
    }
}
