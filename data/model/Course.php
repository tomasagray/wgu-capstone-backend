<?php
namespace Capstone;


use JsonSerializable;

require_once "data/access/PDOable.php";

class Course implements JsonSerializable, PDOable
{
    private $course_id;
    private $title;
    private $course_number;
    private $credits;
    private $start_date;
    private $end_date;
    private $status;


    /**
     * @return mixed
     */
    public function getCourseId()
    {
        return $this->course_id;
    }

    /**
     * @param mixed $course_id
     * @return Course
     */
    public function setCourseId($course_id)
    {
        $this->course_id = $course_id;
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
     * @return Course
     */
    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCourseNumber()
    {
        return $this->course_number;
    }

    /**
     * @param mixed $course_number
     * @return Course
     */
    public function setCourseNumber($course_number)
    {
        $this->course_number = $course_number;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCredits()
    {
        return $this->credits;
    }

    /**
     * @param mixed $credits
     * @return Course
     */
    public function setCredits($credits)
    {
        $this->credits = $credits;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getStartDate()
    {
        return $this->start_date;
    }

    public function getStartDateArray()
    {
        $time_str = strtotime($this->start_date);
        return [
            'year' => intval(date('Y', $time_str)),
            'month' => intval(date('m', $time_str)),
            'day'   => intval(date('d', $time_str))
        ];
    }

    /**
     * @param mixed $start_date
     * @return Course
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
    public function getEndDateArray()
    {
        $time_str = strtotime($this->end_date);
        return [
            'year' => intval(date('Y', $time_str)),
            'month' => intval(date('m', $time_str)),
            'day'   => intval(date('d', $time_str))
        ];
    }

    /**
     * @param mixed $end_date
     * @return Course
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
     * @return Course
     */
    public function setStatus($status)
    {
        $this->status = $status;
        return $this;
    }


    // Helpers
    // ----------------------------------------------------------
    public function __toString()
    {

        return
            "ID: " . $this->course_id . "\n"
                . "Title: " . $this->title . "\n"
                . "Number: " . $this->course_number . "\n"
                . "Credtis: " . $this->credits . "\n"
                . "Start: " . $this->start_date . "\n"
                . "End: " . $this->end_date;
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
            'course_id' => $this->course_id,
            'title' => $this->title,
            'courseNumber' => $this->course_number,
            'credits' => $this->credits,
//            'startDate' => $this->start_date->format(DATE_FORMAT),
            'startDate' => $this->start_date,
//            'startDate' => $this->getStartDateArray(),
//            'endDate' => $this->end_date->format(DATE_FORMAT),
            'endDate' => $this->end_date,
//            'endDate' => $this->getEndDateArray(),
            'status' => $this->status
        ];
    }

    public function as_pdo_array()
    {
        return [
            ':course_id' => $this->course_id,
            ':title' => $this->title,
            ':course_number' => $this->course_number,
            ':credits' => $this->credits,
            ':start_date' => $this->start_date->format(DATE_FORMAT),
            ':end_date' => $this->end_date->format(DATE_FORMAT),
            ':status' => $this->status
        ];
    }
}

class CourseStatus
{
    const IN_PROGRESS = "in_progress";
    const PLANNED = "planned";
    const COMPLETED = "completed";
    const DROPPED = "dropped";

    public static function getAsArray() {
        return [
            self::IN_PROGRESS, self::PLANNED,
            self::COMPLETED, self::DROPPED
        ];
    }
}