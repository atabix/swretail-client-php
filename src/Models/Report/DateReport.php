<?php

namespace SWRetail\Models\Report;

use Carbon\Carbon;
use SWRetail\Models\Model;

abstract class DateReport extends Model
{
    protected $filterDate;

    protected $cashRegister;
    protected $store;
    
    /**
     * Date filter (required).
     *
     * @param string|\DateTime $date
     *
     * @return self
     */
    public function date($date)
    {
        if (empty($date)) {
            throw new \InvalidArgumentException('Invalid date');
        }
        $this->filterDate = Carbon::parse($date);

        return $this;
    }

    protected function hasDate()
    {
        return ! empty($this->filterDate);
    }

    protected function requireDate()
    {
        if (! $this->hasDate()) {
            throw new \UnexpectedValueException('Report date is missing');
        }
    }

    protected function formatDate($format = 'Ymd')
    {
        return $this->filterDate->format($format);
    }

    public function cashRegister()
    {
        return $this->cashRegister;
    }

    public function store()
    {
        return $this->store;
    }
    

}
