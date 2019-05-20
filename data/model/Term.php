<?php
namespace Capstone;

use JsonSerializable;

class Term implements JsonSerializable, PDOable
{
    private $term_id;
    private $title;
    private $start_date;
    private $end_date;

    /**
     * @return mixed
     */
    public function getTermId()
    {
        return $this->term_id;
    }

    /**
     * @param mixed $term_id
     * @return Term
     */
    public function setTermId($term_id)
    {
        $this->term_id = $term_id;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param mixed $title
     * @return Term
     */
    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getStartDate()
    {
        return $this->start_date;
    }

    /**
     * @param mixed $start_date
     * @return Term
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
     * @return Term
     */
    public function setEndDate($end_date)
    {
        $this->end_date = $end_date;
        return $this;
    }


    public function __toString()
    {
        return
            "ID: " . $this->term_id ."\n"
            ."Title: " . $this->title . "\n"
            ."Start: " . $this->start_date . "\n"
            ."End: ". $this->end_date;
    }

    public function as_pdo_array()
    {
        return [
            ':term_id' => $this->term_id,
            ':title' => $this->title,
            ':start_date' => $this->start_date->format('Y-m-d'),
            ':end_date' => $this->end_date->format('Y-m-d')
        ];
    }

    /**
     * Specify data which should be serialized to JSON
     * @link https://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     * @since 5.4.0
     */
    public function jsonSerialize()
    {
        return [
            'termId' => $this->term_id,
            'title' => $this->title,
//            'startDate' => $this->start_date->format(DATE_FORMAT),
            'startDate' => $this->start_date,
//            'startDate' => $this->getDateArray($this->start_date),
//            'endDate' => $this->getDateArray($this->end_date)
//            'endDate' => $this->end_date->format(DATE_FORMAT)
            'endDate' => $this->end_date
        ];
    }


    private function getDateArray($date)
    {
        $time_str = strtotime($date);
        return [
            'year' => intval(date('Y', $time_str)),
            'month' => intval(date('m', $time_str)),
            'day'   => intval(date('d', $time_str))
        ];
    }
}