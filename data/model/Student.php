<?php
namespace Capstone;

require_once "data/model/User.php";

class Student extends User
{
    public function as_pdo_array()
    {
        return [
            ':student_id' => $this->getUserId(),
            ':first_name' => $this->getFirstName(),
            ':last_name' => $this->getLastName(),
            ':email' => $this->getEmail(),
            ':phone' => $this->getPhone()
        ];
    }

    public function __toString()
    {
        return
        "ID: " . $this->getUserId() ."\n"
            ."Name: " . $this->getFirstName() . " " . $this->getLastName() ."\n"
            ."Email: " . $this->getEmail() . ", Phone: " . $this->getPhone();
    }
}