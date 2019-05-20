<?php
namespace Capstone;


use JsonSerializable;

require_once "data/access/PDOable.php";

class Assessment implements JsonSerializable, PDOable
{
    private $assessment_id;
    private $title;
    private $type;
    private $start_date;
    private $end_date;


    public function setStartDate($date) {
        $this->start_date = $date;
    }
    public function setEndDate($date) {
        $this->end_date = $date;
    }

    // Helpers
    // -----------------------------------------------------------------
    public function __toString()
    {
        // TODO: Implement __toString() method.
        return
            "ID: " . $this->assessment_id ."\n"
                ."Title: " . $this->title . "\n"
                ."Type: " . $this->type . "\n"
                ."Start: " . $this->start_date->format(DATE_FORMAT) ."\n"
                ."End: " . $this->end_date->format(DATE_FORMAT) . "\n";
    }

    public function as_pdo_array()
    {
        return [
            ':assessment_id' => $this->assessment_id,
            ':title' => $this->title,
            ':type' => $this->type,
            ':start_date' => $this->start_date->format(DATE_FORMAT),
            ':end_date' => $this->end_date->format(DATE_FORMAT)
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
            'assessment_id' => $this->assessment_id,
            'title' => $this->title,
            'type' => $this->type,
            'startDate' => $this->start_date,
            'endDate' => $this->end_date
        ];
    }
}

class AssessmentType
{
    const PERFORMANCE = "performance";
    const OBJECTIVE = "objective";

    public static function getAsArray() {
        return [
            self::PERFORMANCE, self::OBJECTIVE
        ];
    }
}