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


require_once "data/access/PDOable.php";


class Address implements PDOable
{
    private $address_id;
    private $user_id;
    private $building_number;
    private $unit_number;
    private $street;
    private $additional;
    private $city;
    private $state;
    private $post_code;

    public function __construct($id, $building_number, $unit_number, $street, $city, $state, $post_code)
    {
        $this->address_id = $id;
        $this->building_number = $building_number;
        $this->unit_number = $unit_number;
        $this->street = $street;
        $this->city = $city;
        $this->state = $state;
        $this->post_code = $post_code;
    }

    public function setAdditional($data) {
        $this->additional = $data;
    }

    public static function buildEmpty()
    {
        return
            new Address(
                null, null, null,
                null, null, null, null
            );
    }


    /**
     * @return mixed
     */
    public function getAddressId()
    {
        return $this->address_id;
    }
    public function getUserId() {
        return $this->user_id;
    }
    /**
     * @return mixed
     */
    public function getBuildingNumber()
    {
        return $this->building_number;
    }
    /**
     * @return mixed
     */
    public function getUnitNumber()
    {
        return $this->unit_number;
    }
    /**
     * @return mixed
     */
    public function getStreet()
    {
        return $this->street;
    }
    /**
     * @return mixed
     */
    public function getAdditional()
    {
        return $this->additional;
    }
    /**
     * @return mixed
     */
    public function getCity()
    {
        return $this->city;
    }
    /**
     * @return mixed
     */
    public function getState()
    {
        return $this->state;
    }
    /**
     * @return mixed
     */
    public function getPostCode()
    {
        return $this->post_code;
    }

    public function setUserId($user_id) {
        $this->user_id = $user_id;
    }


    public function __toString()
    {
        return
            implode(' ',
                [
                    'id: ', $this->getAddressId(),
                    'userID: ', $this->getUserId(),
                    $this->building_number,
                    $this->street,
                    $this->unit_number,
                    $this->city,
                    $this->state,
                    $this->post_code
                ]);
    }
    public function as_pdo_array()
    {
        return
        [
            ':address_id' => $this->getAddressId(),
            ':user_id' => $this->getUserId(),
            ':building_number' => $this->getBuildingNumber(),
            ':street' => $this->getStreet(),
            ':unit_number' => $this->getUnitNumber(),
            ':city' => $this->getCity(),
            ':state' => $this->getState(),
            ':post_code' => $this->getPostCode()
        ];
    }

    public function jsonSerialize()
    {
        // TODO: Implement jsonSerialize() method.
    }

    public static function fromJSON($data) : Address
    {
        // Address container
        $address = Address::buildEmpty();
        // Deserialize JSON
        if(!is_object($data))
            $data = json_decode($data);

        foreach ($data AS $key => $value) {
            $address->{$key} = $value;
        }

        return $address;
    }
}
