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

class Image implements JsonSerializable, PDOable
{
    private $image_id;
    private $img_uri;
    private $height;
    private $width;
    private $format;

    public function __construct($id, $uri)
    {
        $this->image_id = $id;
        $this->img_uri = $uri;
    }

    /**
     * @return mixed
     */
    public function getImageId()
    {
        return $this->image_id;
    }

    /**
     * @return mixed
     */
    public function getImgUri()
    {
        return $this->img_uri;
    }

    /**
     * @return mixed
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     * @return mixed
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * @return mixed
     */
    public function getFormat()
    {
        return $this->format;
    }

    /**
     * @param mixed $image_id
     * @return Image
     */
    public function setImageId($image_id)
    {
        $this->image_id = $image_id;
        return $this;
    }

    /**
     * @param mixed $img_uri
     * @return Image
     */
    public function setImgUri($img_uri)
    {
        $this->img_uri = $img_uri;
        return $this;
    }

    /**
     * @param mixed $height
     * @return Image
     */
    public function setHeight($height)
    {
        $this->height = $height;
        return $this;
    }

    /**
     * @param mixed $width
     * @return Image
     */
    public function setWidth($width)
    {
        $this->width = $width;
        return $this;
    }

    /**
     * @param mixed $format
     * @return Image
     */
    public function setFormat($format)
    {
        $this->format = $format;
        return $this;
    }


    public function as_pdo_array()
    {
        return [
            ':image_id' => $this->getImageId(),
            ':image_uri' => $this->getImgUri(),
            ':height' => $this->getHeight(),
            ':width' => $this->getWidth(),
            ':format' => $this->getFormat()
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
        return $this;
    }

    public function __toString()
    {
        return
            "ID: " . $this->getImageId() . "\n"
            ."URI: " . $this->getImgUri() . "\n"
            ."Height: " . $this->getHeight() . ", Width: " . $this->getWidth() ."\n"
            ."Format: " . $this->getFormat();
    }
}
