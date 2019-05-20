<?php
namespace Capstone;


class Student extends Person implements PDOable
{

    public function as_pdo_array()
    {
        // TODO: Implement as_pdo_array() method.
        return [
            ':student_id' => $this->getId(),
            ':first_name' => $this->getFirstName(),
            ':last_name' => $this->getLastName(),
            ':email' => $this->getEmail(),
            ':phone' => $this->getPhone(),
            ':address_id' => $this->getAddress()->getAddressId(),
            ':image_id' => $this->getImage()->getImageId()
        ];
    }
}