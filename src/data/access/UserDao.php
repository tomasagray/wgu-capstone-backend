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


use PDOException;

require_once "sql/write_operations.php";
require_once "sql/read_operations.php";
require_once "data/access/Database.php";
require_once "data/access/AddressDao.php";
require_once "data/model/User.php";

class UserDao
{
    private $db;
    private $address_dao;

    public function __construct() {
        $this->db = Database::getInstance();
        $this->address_dao = new AddressDao();
    }

    // CRUD
    // -------------------------------------------
    function add(User $user)
    {
        global $_SQL_W;

        try {
            // Prepare
            $query = $this->db->prepare( $_SQL_W['add_user'] );
            // Execute
            $query->execute( $user->as_pdo_array() );

            // If address supplied, add to db
            if($user->hasAddress()) {
                Log::i("Saving address to DB: " . $user->getAddress()->getAddressId() );
                $this->address_dao->save( $user->getAddress() );
            }

            return true;
        } catch (PDOException $e) {
            Log::e(
                "Error saving user to database: {$user->getFullName()}"
                        ."\n\tMessage: " . $e->getMessage()
            );

            return false;
        }
    }

    function update(User $user)
    {
        global $_SQL_W;

        try {
            // Prepare
            $query = $this->db->prepare($_SQL_W['update_user']);
            // Execute
            $query->execute( $user->as_pdo_array() );

            if($user->hasAddress()) {
                Log::i("Updating user address");
                $update_success = $this->address_dao->update( $user->getAddress() );

                if($update_success === false) {
                    throw new PDOException("Failed updating address");
                }
            }

            return true;

        } catch (PDOException $e) {
            Log::e(
                "Error updating user!"
                        ."\n\tMessage: " . $e->getMessage()
            );
        }

        return false;
    }

    function delete($user_id)
    {
        global $_SQL_W;

        try {
            // Prepare
            $query = $this->db->prepare( $_SQL_W['delete_user'] );
            // Execute
            $query->execute( [':user_id' => $user_id] );

            if($query->rowCount() == 1) {
                return true;
            }
        } catch (PDOException $e) {
            Log::e(
                "Could not delete user from database!"
                    ."\n\tMessage: " . $e->getMessage()
            );
        }

        return false;
    }

    // Students
    // -------------------------------------------
    function loadAllStudents()
    {
        global $_SQL_R;
        $students = array();

        try {
            // Prepare
            $query = $this->db->prepare($_SQL_R['get_all_students']);
            // Execute
            $query->execute();
            // Fetch results
            while($student = $query->fetch() ) {
                Log::i("Got student: " . json_encode($student));
                array_push($students, $student);
            }

            return $students;
        } catch (PDOException $e) {
            Log::e(
                "Could not load all student data:"
                    ."\n\tMessage: " . $e->getMessage()
            );

            return null;
        }
    }

    function loadStudent($user_id)
    {
        global $_SQL_R;

        try {
            // Prepare
            $query = $this->db->prepare($_SQL_R['get_student_data']);
            // Execute
            $query->execute( [':user_id' => $user_id] );
            // Fetch result
            $result = $query->fetch();
            // Clear buffer
            $query->nextRowset();

            return $result;

        }  catch (PDOException $e) {
            Log::e(
                "Could not load data for user ID: {$user_id}"
                ."\n\tMessage: " . $e->getMessage()
            );
        }

        // Student not found
        return null;
    }

    // Faculty
    // -----------------------------------------
    function loadAllFaculty()
    {
        global $_SQL_R;
        $faculty = array();

        try {
            // Prepare
            $query = $this->db->prepare($_SQL_R['get_all_faculty']);
            // Execute
            $query->execute();
            // Fetch results
            while($user = $query->fetch() ) {
                Log::i("Got faculty: " . json_encode($user));
                array_push($faculty, $user);
            }

            return $faculty;
        } catch (PDOException $e) {
            Log::e(
                "Could not load all faculty data:"
                ."\n\tMessage: " . $e->getMessage()
            );

            return null;
        }

    }

    function loadFaculty($user_id)
    {
        global $_SQL_R;

        try {
            // Prepare
            $query = $this->db->prepare($_SQL_R['get_faculty_data']);
            // Execute
            $query->execute( [':user_id' => $user_id] );
            // Fetch result
            $result = $query->fetch();
            // Clear buffer
            $query->nextRowset();

            return $result;

        }  catch (PDOException $e) {
            Log::e(
                "Could not load data for user ID: {$user_id}"
                ."\n\tMessage: " . $e->getMessage()
            );
        }

        // Faculty not found
        return null;
    }

    // Login
    // -----------------------------------------
    function loadLoginDataByEmail($email)
    {
        Log::s("Loading user data for email: " . $email);

        global $_SQL_R;

        try {
            // Prepare
            $query = $this->db->prepare( $_SQL_R['get_user_data_from_email'] );
            // Execute
            $query->execute([ ':email' => $email ]);
            // Fetch
            $result = $query->fetch();
            // Clear
            $query->nextRowset();

            return $result;

        } catch (PDOException $e) {
            Log::s(
                "[ERROR]: Error loading user data from email: " . $email
                ."\n\tMessage: " . $e->getMessage()
            );
        }

        return null;
    }

    function loadLoginDataById($user_id)
    {
        Log::s("Loading user data for ID: " . $user_id);

        global $_SQL_R;

        try {
            // Prepare
            $query = $this->db->prepare($_SQL_R['get_user_data_from_id']);
            // Execute
            $query->execute([ ':user_id' => $user_id ]);
            // Fetch
            $result = $query->fetch();
            // Clear
            $query->nextRowset();

            return $result;

        } catch (PDOException $e) {
            Log::s(
                "[ERROR]: Error loading user data from ID: " . $user_id
                ."\n\tMessage: " . $e->getMessage()
            );
        }

        return null;
    }
}
