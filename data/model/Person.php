<?php
namespace Capstone;

require_once "data/model/Address.php";
require_once "data/model/Image.php";

class Person
{
    private $id;
    private $first_name;
    private $last_name;
    private $email;
    private $phone;
    private $address;
    private $image;

    public static function create($id, $first_name, $last_name): Person {
        return new self($id, $first_name, $last_name);
    }

    public function __construct($id, $first_name, $last_name)
    {
        $this->id = $id;
        $this->first_name = $first_name;
        $this->last_name = $last_name;

        // Default object holders
        $this->address = Address::buildEmpty();
        $this->image = Image::buildEmpty();
    }

    public function setId($id) {
        $this->id = $id;
    }
    public function setFirstName($name) {
        $this->first_name = $name;
    }
    public function setLastName($name) {
        $this->last_name = $name;
    }
    public function setEmail($email) {
        $this->email = $email;
    }
    public function setPhone($phone) {
        $this->phone = $phone;
    }
    public function setAddress($address) {
        $this->address = $address;
    }
    public function setImage($image) {
        $this->image = $image;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
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

    /**
     * @return mixed
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @return mixed
     */
    public function getImage()
    {
        return $this->image;
    }

    public function __toString()
    {
        // TODO: Implement __toString() method.
        return
            "ID: " . $this->getId() ."\n"
            ."Name: " . $this->getFirstName() . ' ' . $this->getLastName() ."\n"
            ."Email: " . $this->getEmail() . "\n"
            ."Phone: " . $this->getPhone() . "\n"
            ."Address: " . $this->getAddress();
    }

}