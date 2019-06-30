<?php
namespace Capstone;

use DateTime;
use Exception;
use JsonSerializable;

class Term implements JsonSerializable, PDOable
{
    private $term_id;
    private $student_id;
    private $title;
    private $start_date;
    private $end_date;
    private $courses;

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

    public function getStudentId() {
        return $this->student_id;
    }

    public function setStudentId($id) {
        $this->student_id = $id;
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
        try {
            $this->start_date = new DateTime($start_date);
        } catch (Exception $e) {
            $this->start_date = null;
        }

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
            ."Start: " . $this->getStartDate()->format(DATE_FORMAT) . "\n"
            ."End: ". $this->getEndDate()->format(DATE_FORMAT);
    }

    public function as_pdo_array()
    {
        return [
            ':term_id' => $this->getTermId(),
            ':student_id' => $this->getStudentId(),
            ':title' => $this->getTitle(),
            ':start_date' => date('Y-m-d', strtotime($this->getStartDate())),
            ':end_date' => date('Y-m-d', strtotime($this->getEndDate()))
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
            'term_id' => $this->getTermId(),
            'student_id' => $this->getStudentId(),
            'title' => $this->getTitle(),
            'start_date' => $this->getStartDate(),
            'end_date' => $this->getEndDate()
        ];
    }

    public static function fromJSON($data) : Term {
        // Term container
        $term = new Term();
        $data = json_decode($data);
        foreach ($data AS $key => $value) {
            try {
                if($key == "start_date") {
                    $term->start_date = date('Y-m-d', strtotime($value));
                } else if( $key == "end_date"){
                    $term->end_date = date('Y-m-d', strtotime($value));
                } else {
                    $term->{$key} = $value;
                }

            } catch (Exception $e) {
                Log::e(
                    "Caught exception parsing date"
                            ."\n\tMessage: " . $e->getMessage()
                );
            }
        }

        return $term;
    }
}