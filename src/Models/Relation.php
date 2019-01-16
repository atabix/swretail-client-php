<?php

namespace SWRetail\Models;

use Carbon\Carbon;
use SWRetail\Http\Client;
use SWRetail\Http\Response;
use SWRetail\Models\Relation\Address;
use SWRetail\Models\Traits\UseDataMap;

class Relation extends Model
{
    use UseDataMap;

    protected $type;
    protected $code;

    protected $address;
    protected $orders;

    // apikey => localkey
    const DATAMAP = [
        'relationtype'  => 'type',
        'relationcode'  => 'code',
        'external_id'   => 'external_id',
        'lastname'      => 'last_name',
        'firstname'     => 'first_name',
        'sex'           => 'title',
        'birthdate'     => 'birthdate',
        'phone1'        => 'phone1',
        'phone2'        => 'phone2',
        'contact'       => 'contact',
        'loyaltypoints' => 'loyalty_points',
        'icp'           => 'icp',
        'relationgroup' => 'group',
        'email'         => 'email', // undocumented
        'newsletter'    => 'newsletter', // undocumented
    ];

    public function __construct($type, $code = null)
    {
        $this->data = new \stdClass();
        $this->setValue('type', $type);
        if (! \is_null($code)) {
            $this->setValue('code', $code);
        }

        $this->address = new Address();
        $this->orders = [];
    }

    /**
     * Get an existing Relation by Code from the API.
     *
     * @api
     *
     * @param string $code
     *
     * @return self
     */
    public static function byCode($code) : self
    {
        if (empty($code)) {
            throw new \InvalidArgumentException('Relations must have a code.');
        }
        $path = 'relation/relationcode/' . $code;

        $response = Client::requestApi('GET', $path);

        return self::handleFindResponse($response);
    }

    /**
     * Get an existing Relation by External ID from th API.
     *
     * @api
     *
     * @param mixed $externalId
     *
     * @return self
     */
    public static function byExternalId($externalId) : self
    {
        if (empty($externalId)) {
            throw new \InvalidArgumentException('External Id must not be empty.');
        }
        $path = 'relation/external_id/' . $externalId;

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

        $relation = new static($data->relationtype, $data->relationcode);
        $relation->parseData($data);

        return $relation;
    }

    /**
     * "Unified Zip" is a Dutch zipcode + housenumber combination.
     *
     * @param string $unifiedzip
     *
     * @return array[self]
     */
    public static function searchByUnifiedZip($unifiedzip) : array
    {
        if (! \preg_match('/^[1-9][0-9]{3}[A-Z]{2}[0-9]+/', $unifiedzip)) {
            throw new \InvalidArgumentException('Unified Zip must have a correct format (like 1234AB16).');
        }
        $path = 'relation/unifiedzip/' . $unifiedzip;

        $response = Client::requestApi('GET', $path);

        return self::handleSearchResponse($response);
    }

    /**
     * Find relations with this email address.
     *
     * @param string $email
     *
     * @return array[self]
     */
    public static function searchByEmail($email) : array
    {
        if (\strlen($email) < 3) {
            throw new \InvalidArgumentException('Email address is not valid.');
        }
        $path = 'relation/email/' . (string) $email;

        $response = Client::requestApi('GET', $path);

        return self::handleSearchResponse($response);
    }

    /**
     * Find relations with this email address.
     *
     * @param int $minutes
     *
     * @return array[self]
     */
    public static function searchChanged($minutes) : array
    {
        if (\intval($minutes) < 1) {
            throw new \InvalidArgumentException('Changed must be a positive integer in minutes.');
        }
        $path = 'relation/lastmodify/' . (int) $minutes;

        $response = Client::requestApi('GET', $path);

        return self::handleSearchResponse($response);
    }

    /**
     * Handle search responses for a multiple results.
     *
     * @param Response $response
     *
     * @return array[self]
     */
    private static function handleSearchResponse(Response $response) : array
    {
        // errorcode == 0 means "empty result" (not an exception).
        if (isset($response->json->errorcode)) {
            return [];
        }

        $alldata = \is_array($response->json) ? $response->json : [$response->json];

        $list = [];
        foreach ($alldata as $data) {
            $relation = new static($data->relationtype, $data->relationcode);
            $relation->parseData($data);
            $list[] = $relation;
        }

        return $list;
    }

    /**
     * Create a new Artice in the API.
     *
     * @api
     *
     * @return string The new relation code.
     */
    public function create()
    {
        $path = 'relation';

        $data = $this->toApiRequest();

        $response = Client::requestApi('POST', $path, null, $data);

        $relationCode = $response->json->relationcode;

        return $relationCode;
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
    public function update()
    {
        $type = $this->type ?? $this->data->type;
        $code = $this->code ?? $this->data->code;

        if (empty($type) || empty($code)) {
            throw new \InvalidArgumentException('Relations must have a Type and a Code.');
        }

        $path = 'relation';

        $data = $this->toApiRequest();

        // `PUT` won't work. Have to use `POST` including `relationcode` in the body data.
        $response = Client::requestApi('POST', $path, null, $data);

        return $code == $response->json->relationcode;
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
                case 'street':
                case 'housenumber':
                case 'zipcode':
                case 'city':
                case 'country':
                    $this->address->setValue($key, $value);
                    break;
                default:
                    if (! \array_key_exists($key, self::DATAMAP)) {
                        // ignore
                        break;
                    }
                    $this->setValue(self::DATAMAP[$key], $value);
            }
        }

        // $this->parseOrders($data->orders);
    }

    public function setValue($key, $value)
    {
        switch ($key) {
            case 'type':
                $this->type = $value;
                $this->data->$key = $value;
                break;
            case 'code':
                $this->code = $value;
                $this->data->$key = $value;
                break;
            case 'birthdate':
                $this->data->$key = Carbon::parse($value);
                break;
            case 'newsletter':
                $this->data->$key = (bool) $value;
                break;
            case 'type':
            case 'icp':
            case 'loyalty_points':
                $this->data->$key = (int) $value;
                break;
            case 'external_id':
            case 'group':
            default:
                $this->data->$key = (string) $value;
        }

        return $this;
    }

    protected function getApiValue($key, $value)
    {
        switch ($key) {
            case 'birthdate':
                return Carbon::parse($this->data->$key)->toDateString();
            case 'newsletter':
                return (int) $this->data->$key;
            default:
                return $value;
        }
    }

    public function setName($lastName, $firstName)
    {
        return $this
            ->setLastName($lastName)
            ->setFirstName($firstName);
    }

    public function setPhone($phone1, $phone2 = null)
    {
        if (! \is_null($phone2)) {
            $this->setPhone2($phone2);
        }

        return $this->setPhone1($phone1);
    }

    public function address()
    {
        return $this->address;
    }

    public function toApiRequest()
    {
        $data = $this->mapDataToApiRequest();

        $data = $data + $this->address()->toApiRequest();

        return $data;
    }
}
