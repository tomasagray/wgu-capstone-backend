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

class Course implements JsonSerializable, PDOable
{
    private $course_id;
    private $title;
    private $course_number;
    private $credits;
    private $mentor;
    private $assessments;

    /**
     * @return mixed
     */
    public function getMentor()
    {
        return $this->mentor;
    }

    /**
     * @param mixed $mentor
     * @return Course
     */
    public function setMentor($mentor)
    {
        $this->mentor = $mentor;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getAssessments()
    {
        return $this->assessments;
    }

    /**
     * @param mixed $assessments
     * @return Course
     */
    public function setAssessments($assessments)
    {
        $this->assessments = $assessments;
        return $this;
    }

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


    // Helpers
    // ----------------------------------------------------------
    public function __toString()
    {

        return
            "ID: " . $this->course_id . "\n"
                . "Title: " . $this->title . "\n"
                . "Number: " . $this->course_number . "\n"
                . "Credits: " . $this->credits . "\n";
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
            'course_number' => $this->course_number,
            'credits' => $this->credits,
            'mentor' => $this->getMentor(),
            'assessments' => $this->getAssessments()
        ];
    }
    public static function fromJSON($data) : Course
    {
        // Student container
        $course = new Course();
        // Deserialize JSON
        $data = json_decode($data);

        foreach ($data AS $key => $value) {
            if ($key == "assessment") {
                Log::i("parsing assessment: " . json_encode($value));
                $assessment = Assessment::fromJSON($value);
                // Ensure foreign key set
                $assessment->setCourseId( $course->getCourseId() );
                $value = $assessment;
            }
            $course->{$key} = $value;
        }

        return $course;
    }

    public function as_pdo_array()
    {
        return [
            ':course_id' => $this->course_id,
            ':title' => $this->title,
            ':course_number' => $this->course_number,
            ':credits' => $this->credits
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
