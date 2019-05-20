<?php
namespace Capstone;


class Address implements PDOable
{
    private $address_id;
    private $building_number;
    private $unit_number;
    private $street;
    private $additional;
    private $city;
    private $state;
    private $country;
    private $post_code;

    public function __construct($id, $building_number, $unit_number, $street, $city, $state, $country, $post_code)
    {
        $this->address_id = $id;
        $this->building_number = $building_number;
        $this->unit_number = $unit_number;
        $this->street = $street;
        $this->city = $city;
        $this->state = $state;
        $this->country = $country;
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
                null, null, null, null,
                null
            );
    }

    public function __toString()
    {
        // TODO: Implement __toString() method.
        return
            implode(' ',
                [
                    $this->building_number,
                    $this->street,
                    $this->unit_number,
                    $this->city,
                    $this->state,
                    $this->post_code,
                    $this->country
            ]);
    }

    /**
     * @return mixed
     */
    public function getAddressId()
    {
        return $this->address_id;
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
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * @return mixed
     */
    public function getPostCode()
    {
        return $this->post_code;
    }

    public function as_pdo_array()
    {
        return
        [
            ':address_id' => $this->address_id,
            ':building_number' => $this->building_number,
            ':street' => $this->street,
            ':unit_number' => $this->unit_number,
            ':city' => $this->city,
            ':state' => $this->state,
            ':country' => $this->country,
            ':post_code' => $this->post_code
        ];
    }
}