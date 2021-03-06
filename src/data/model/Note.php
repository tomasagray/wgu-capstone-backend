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

use DateTime;
use Exception;
use JsonSerializable;

require_once "data/access/PDOable.php";

class Note implements JsonSerializable, PDOable
{
    private $note_id;
    private $text;
    private $updateDate;

    /**
     * @return mixed
     */
    public function getNoteId()
    {
        return $this->note_id;
    }

    /**
     * @param mixed $note_id
     * @return Note
     */
    public function setNoteId($note_id)
    {
        $this->note_id = $note_id;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * @param mixed $text
     * @return Note
     */
    public function setText($text)
    {
        $this->text = $text;
        return $this;
    }

    /**
     * @return DateTime Date the note was updated.
     * @throws Exception
     */
    public function getUpdateDate() : DateTime
    {
        if($this->updateDate instanceof DateTime)
            return $this->updateDate;
        else
            return new DateTime($this->updateDate);
    }

    /**
     * @param mixed $date
     * @return Note
     */
    public function setUpdateDate(DateTime $date)
    {
        $this->updateDate = $date;
        return $this;
    }


    // Helpers
    // ---------------------------------------------------------------

    public function __toString()
    {
        return
            "ID: " . $this->getNoteId() . "\n"
            ."Text: " . $this->getText() . "\n"
            ."Last Updated: " . $this->getUpdateDate()->format(DATE_FORMAT);
    }

    /**
     * Specify data which should be serialized to JSON
     * @link https://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     * @since 5.4.0
     * @throws Exception
     */
    public function jsonSerialize()
    {
        return [
            'note_id' => $this->note_id,
            'text'  => $this->text,
            'updateDate' => $this->getUpdateDate()->format(DATE_FORMAT)
        ];
    }

    public function as_pdo_array()
    {
        return [
            ':note_id' => $this->note_id,
            ':text' => $this->text,
            ':update_date' => $this->getUpdateDate()->format(DATE_FORMAT)
        ];
    }
}
