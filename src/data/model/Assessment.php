<?php
/*
 * Copyright (c) 2020 Tomás Gray
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 */

namespace Capstone;


use JsonSerializable;

require_once "data/access/PDOable.php";

class Assessment implements JsonSerializable, PDOable
{
    private $assessment_id;
    private $course_id;
    private $title;
    private $type;
    private $items = array();

    // Getters & Setters
    // -----------------------------------------------------------------
    public function getAssessmentId() {
        return $this->assessment_id;
    }
    public function setAssessmentId($assessment_id) {
        $this->assessment_id = $assessment_id;
    }
    public function getCourseId() {
        return $this->course_id;
    }
    public function setCourseId($course_id) {
        $this->course_id = $course_id;
    }
    public function getTitle() {
        return $this->title;
    }
    public function setTitle($title) {
        $this->title = $title;
    }
    public function getType() {
        return $this->type;
    }
    public function setType(AssessmentType $type) {
        $this->type = $type;
    }


    // Items
    // -----------------------------------------------
    public function setItems($items) {
        Log::i("SetItems called: " . $items);

        if(is_array($items)) {
            $this->items = $items;
        } else {
            $this->items = json_decode($items, true);
        }
    }
    public function getItems() {
        return $this->items;
    }
    public function addItem(AssessmentItem $item) {
        array_push($this->items, $item);
    }
    public function removeItem($i) {
        // Remove item from list
        unset( $this->items[$i] );
        // Reorder array
        $this->items = array_values($this->items);
    }




    // Helpers
    // -----------------------------------------------------------------
    public function __toString()
    {
        return
            "ID: " . $this->assessment_id ."\n"
                ."Course ID: ". $this->getCourseId() ."\n"
                ."Title: " . $this->title . "\n"
                ."Type: " . $this->type . "\n"
                ."Items: " . count($this->items);
    }

    public function as_pdo_array()
    {
        // Encode items as JSON
        $items = json_encode($this->getItems());

        return [
            ':assessment_id' => $this->assessment_id,
            ':course_id' => $this->getCourseId(),
            ':title' => $this->title,
            ':type' => $this->type,
            ':items' => $items //$this->getAssessmentItems()
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
        // Ensure items not double-serialized
        // TODO: Is this needed?
        if(is_array($this->getItems()))
            $items = $this->getItems();
        else
            $items = json_decode($this->getItems());

        return [
            'assessment_id' => $this->getAssessmentId(),
            'course_id' => $this->getCourseId(),
            'title' => $this->getTitle(),
            'type' => $this->getType(),
            'items' => $items //$this->getAssessmentItems()
        ];
    }

    public static function fromJSON($data) : Assessment
    {
        // Student container
        $assessment = new Assessment();
        // Deserialize JSON
        $data = json_decode($data);

        foreach ($data AS $key => $value) {
            $assessment->{$key} = $value;
        }

        return $assessment;
    }
}

class AssessmentType
{
    const PERFORMANCE = "performance";
    const OBJECTIVE = "objective";

    public $type;

    public static function getAsArray() {
        return [
            self::PERFORMANCE, self::OBJECTIVE
        ];
    }

    public function __construct($type)
    {
        if($type === self::PERFORMANCE)
            $this->type = self::PERFORMANCE;
        else
            $this->type = self::OBJECTIVE;
    }

    public function __toString() {
        return $this->type;
    }
}

class AssessmentItem
{
    public $title;
    public $description;
    public $competence;
    public $approaching;
    public $incompetence;

    public function __toString(){
        try {
            $json = json_encode($this, JSON_THROW_ON_ERROR);
            if ($json !== FALSE) {
                return $json;
            }
        } catch (\JsonException $e) {}
        return "null";
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
     * @return AssessmentItem
     */
    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param mixed $description
     * @return AssessmentItem
     */
    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCompetence()
    {
        return $this->competence;
    }

    /**
     * @param mixed $competence
     * @return AssessmentItem
     */
    public function setCompetence($competence)
    {
        $this->competence = $competence;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getApproaching()
    {
        return $this->approaching;
    }

    /**
     * @param mixed $approaching
     * @return AssessmentItem
     */
    public function setApproaching($approaching)
    {
        $this->approaching = $approaching;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getIncompetence()
    {
        return $this->incompetence;
    }

    /**
     * @param mixed $incompetence
     * @return AssessmentItem
     */
    public function setIncompetence($incompetence)
    {
        $this->incompetence = $incompetence;
        return $this;
    }
}
