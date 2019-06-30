<?php
namespace Capstone;

require_once "data/model/User.php";

class Faculty extends User implements PDOable
{
    public function as_pdo_array()
    {
        return [
            ':faculty_id' => $this->getUserId(),
            ':first_name' => $this->getFirstName(),
            ':last_name' => $this->getLastName(),
            ':email' => $this->getEmail(),
            ':phone' => $this->getPhone()
        ];
    }

    public function __toString()
    {
        return
            "Faculty ID: " . $this->getUserId() .
            ", Name: " . $this->getFullName() .
            ", Email: " . $this->getEmail() .
            ", Phone: " . $this->getPhone();
    }
}