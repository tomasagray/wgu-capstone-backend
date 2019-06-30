<?php
namespace Capstone;


class TermCourse extends Course
{
    private $start_date;
    private $end_date;
    private $status;

    /**
     * @return mixed
     */
    public function getStartDate()
    {
        return $this->start_date;
    }

    /**
     * @param mixed $start_date
     * @return TermCourse
     */
    public function setStartDate($start_date)
    {
        $this->start_date = $start_date;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getEndDate()
    {
        return $this->end_date;
    }

    /**
     * @param mixed $end_date
     * @return TermCourse
     */
    public function setEndDate($end_date)
    {
        $this->end_date = $end_date;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param mixed $status
     * @return TermCourse
     */
    public function setStatus($status)
    {
        $this->status = $status;
        return $this;
    }

    public static function fromJSON($data): Course
    {
        return parent::fromJSON($data);
    }
}