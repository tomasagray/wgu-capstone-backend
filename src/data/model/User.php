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

require_once "data/model/Address.php";
require_once "data/model/Image.php";
require_once "data/access/PDOable.php";

class User implements PDOable
{
    private $user_id;
    private $user_type;
    private $first_name;
    private $last_name;
    private $email;
    private $password;
    private $phone;
    private $address;

    public function setUserId($user_id) {
        $this->user_id = $user_id;
        return $this;
    }
    public function setUserType($user_type) {
        $this->user_type = $user_type;
        return $this;
    }
    public function setFirstName($name) {
        $this->first_name = $name;
        return $this;
    }
    public function setLastName($name) {
        $this->last_name = $name;
        return $this;
    }
    public function setEmail($email) {
        $this->email = $email;
        return $this;
    }
    public function setPhone($phone) {
        $this->phone = $phone;
        return $this;
    }
    public function setPassword($pass) {
        $this->password = $pass;
        return $this;
    }
    public function setAddress(Address $address) {
        $this->address = $address;
    }

    /**
     * @return mixed
     */
    public function getUserId()
    {
        return $this->user_id;
    }
    /**
     * @return mixed
     */
    public function getUserType(){
        return $this->user_type;
    }
    /**
     * @return mixed
     */
    public function getFirstName()
    {
        return $this->first_name;
    }
    /**
     * @return mixed
     */
    public function getLastName()
    {
        return $this->last_name;
    }
    public function getFullName() {
        return
            $this->getFirstName() . " " . $this->getLastName();
    }
    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }
    /**
     * @return mixed
     */
    public function getPhone()
    {
        return $this->phone;
    }
    public function getPassword() {
        return $this->password;
    }
    public function getAddress() : Address {
        return $this->address;
    }

    public function hasAddress() {
        return ($this->address != null) ? true : false;
    }

    public function __toString()
    {
        $str =
            "ID: " . $this->getUserId() ."\n"
            ."Type: " . $this->getUserType() ."\n"
            ."Name: " . $this->getFirstName() . ' ' . $this->getLastName() ."\n"
            ."Email: " . $this->getEmail() . "\n"
            ."Phone: " . $this->getPhone() . "\n";
        if($this->hasAddress()) {
            $str .= "Address: " . $this->getAddress();
        }

        return $str;
    }

    public function as_pdo_array()
    {
        return [
            ':user_id'  => $this->getUserId(),
            ':user_type' => $this->getUserType(),
            ':first_name' => $this->getFirstName(),
            ':last_name' => $this->getLastName(),
            ':email' => $this->getEmail(),
            ':password' => $this->getPassword(),
            ':phone' => $this->getPhone()
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
        // TODO: Implement jsonSerialize() method.
    }

    public static function fromJSON($data) : User
    {
        // Student container
        $user = new User();
        // Deserialize JSON
        $data = json_decode($data);

        foreach ($data AS $key => $value) {
            if ($key == "address") {
                Log::i("parsing address: " . json_encode($value));
                $address = Address::fromJSON($value);
                // Ensure foreign key set
                $address->setUserId( $user->getUserId() );
                $value = $address;
            }
            $user->{$key} = $value;
        }

        return $user;
    }
}
