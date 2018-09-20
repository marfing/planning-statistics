<?php

// src/Entity/DateInterval.php - used for form creation
namespace App\Entity;

use Symfony\Component\Validator\Constraints as Assert;

class DateInterval
{
    /**
     * @Assert\NotBlank()
     * @Assert\Type("\DateTime")
     */
    protected $startDate;
    
    /**
     * @Assert\NotBlank()
     * @Assert\Type("\DateTime")
     */
    protected $endDate;

    protected $aggregate=false;
    
    public function getStartDate()
    {
        return $this->startDate;
    }

    public function setStartDate($startDate)
    {
        $this->startDate = $startDate;
    }

    public function getEndDate()
    {
        return $this->endDate;
    }

    public function setEndDate($endDate)
    {
        $this->endDate = $endDate;
    }

    public function getAggregate()
    {
        return $this->aggregate;
    }

    public function setAggregate($aggregate)
    {
        $this->aggregate = $aggregate;
    }
}