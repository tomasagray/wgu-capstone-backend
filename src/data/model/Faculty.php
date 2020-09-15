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
