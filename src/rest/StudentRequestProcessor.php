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

require_once "data/access/UserDao.php";
require_once "rest/Response.php";
require_once "rest/RouteProcessor.php";
require_once "rest/RequestProcessor.php";


class StudentRequestProcessor
{
    private $method;
    private $routes;
    private $student_dao;
    private $term_dao;
    private $response;

    // Define sub-routes
    const TERMS = 'terms';
    const TERM_COURSES = 'courses';

    public function __construct()
    {
        $this->method = $_SERVER['REQUEST_METHOD'];
        $this->routes = new RouteProcessor();
        $this->student_dao = new UserDao();
        $this->term_dao = new TermDao();
        $this->response = new Response();
    }

    public function getResponse() {
        return $this->response;
    }

    public function processRoutes()
    {
        switch ($this->method) {
            case RequestProcessor::GET_REQUEST:
                Log::i("Handling GET request ");
                $this->handleGetRequest();
                break;

            case RequestProcessor::POST_REQUEST:
                Log::i("Handling POST request");
                $this->handlePostRequest();
                break;

            case RequestProcessor::DELETE_REQUEST:
                Log::i("Handling DELETE request");
                $this->handleDeleteRequest();
                break;

            case RequestProcessor::PATCH_REQUEST:
                Log::i("Handling PATCH request");
                $this->handlePatchRequest();
                break;

            default:
                Log::e("Invalid request method received at StudentRequestProcessor");
                break;
        }
    }

    // GET
    // ------------------------------------------
    private function handleGetRequest()
    {
        if ($this->routes->hasSubRoute()) {
            // Parse sub-routes
            if($this->routes->getRoute(2) != null) {
                Log::i("Handling GET request for student data");
                $student_id = $this->routes->getRoute(1);
                $data_type = $this->routes->getEndPoint();

                // Validation
                if($student_id == null || $data_type == null) {
                    $this->response->setStatusCode(Response::HTTP_400);
                    return;
                }

                switch ($data_type) {
                    case self::TERMS:
                        $term_id = $this->routes->getRoute(3);
                        if($term_id != null) {
                            Log::i("Getting term data for: {$term_id}");
                            // TODO: Address this!
                            // Make sure student_id != undefined in request
                            $term = $this->term_dao->load($term_id);
                            if($term != null) {
                                $this->response->setBody($term);
                                $this->response->setStatusCode(Response::HTTP_200);
                            } else {
                                $this->response->setStatusCode(Response::HTTP_404);
                            }
                        } else {
                            Log::i("Getting terms for student: {$student_id}");
                            $terms = $this->term_dao->loadStudentTerms($student_id);
                            if ($terms != null) {
                                Log::i("Successfully loaded terms: ". json_encode($terms));
                                $this->response->setStatusCode(Response::HTTP_200);
                                $this->response->setBody($terms);
                            } else {
                                $this->response->setStatusCode(Response::HTTP_404);
                            }
                        }
                        break;

                    case self::TERM_COURSES:
                        $term_id = $this->routes->getRoute(3);
                        Log::i("Getting courses for term: {$term_id}");
                        $courses = $this->term_dao->getCoursesForTerm($term_id);

                        if($courses != null) {
                            Log::i("Successfully loaded term courses");
                            $this->response->setStatusCode(Response::HTTP_200);
                            $this->response->setBody($courses);
                        } else {
                            $this->response->setStatusCode(Response::HTTP_404);
                        }

                        break;

                    default:
                        Log::i("Invalid sub-route for student data: {$data_type}");
                        $this->response->setStatusCode(Response::HTTP_400);
                        break;
                }
            } else {
                // Get specific student
                $student_id = $this->routes->getRoute(1);
                Log::i("Fetching data for student: " . $student_id);
                $this->getStudent($student_id);
            }

        } else {
            Log::i("Fetching all student data");
            $this->getAllStudents();
        }
    }

    private function getStudent($student_id)
    {
        $student = $this->student_dao->loadStudent($student_id);

        if($student != null) {
            Log::i("Successfully retrieved data for student: " . $student_id);
            $this->response->setStatusCode(Response::HTTP_200);
            $this->response->setBody( $student );
        } else {
            Log::e("Failed to retrieve data for student: ". $student_id);
            $this->response->setStatusCode(Response::HTTP_404);
        }
    }
    // TODO: Combine these
    private function getAllStudents()
    {
        $students = $this->student_dao->loadAllStudents();

        if($students != null) {
            Log::i("Successfully retrieved data for all students; #: " . count($students));
            Log::i("Student data:\n". json_encode($students));
            $this->response->setStatusCode(Response::HTTP_200);
            $this->response->setBody( $students );
        } else {
            Log::e("Failed to retrieve all student data");
            $this->response->setStatusCode(Response::HTTP_404);
        }
    }


    // POST
    // ----------------------------------------
    private function handlePostRequest()
    {
        // POSTing student data
        if($this->routes->hasSubRoute()) {
            // Parse sub-routes
            $student_id = $this->routes->getRoute(1);
            $data_type = $this->routes->getEndPoint();

            // Ensure valid routes
            if($student_id == null || $data_type == null) {
                $this->response->setStatusCode(Response::HTTP_400);
                return;
            }

            switch ($data_type) {
                case self::TERMS:
                    Log::i("Adding term for student {$student_id}");

                    // Get raw input
                    $input = file_get_contents("php://input");

                    // De-serialize term data
                    $term = Term::fromJSON( $input );

                    if( $this->term_dao->save($term) ) {
                        // De-serialize term courses
                        $raw_json = json_decode($input);
                        foreach ($raw_json->courses as $course) {
                            $this->term_dao->assignCourse($term->getTermId(), $course);
                        }

                        Log::i("Term saved successfully");
                        $this->response->setStatusCode(Response::HTTP_200);

                    } else {
                        $this->response->setStatusCode(Response::HTTP_500);
                    }
                    break;

                case self::TERM_COURSES:
                    Log::i("Adding course to term: " . $this->routes->getRoute(3));
                    Log::i("Course: " . file_get_contents("php://input"));

                    $term_id = $this->routes->getRoute(3);
                    $course_data = json_decode( file_get_contents("php://input") );
                    $post_success = $this->term_dao->assignCourse($term_id, $course_data);

                    if($post_success) {
                        Log::i("Course successfully associated with term");
                        $this->response->setStatusCode(Response::HTTP_200);
                    } else {
                        $this->response->setStatusCode(Response::HTTP_500);
                    }
                    break;

                default:
                    $this->response->setStatusCode(Response::HTTP_400);
                    return;
            }


            // POSTing new Student
        } else {
            // De-serialize JSON input from POST
            $new_student = Student::fromJSON( file_get_contents("php://input") );
            if($new_student != null) {
                Log::i("De-serialized:\n" . $new_student);
                // Add student to DB
                $post_success = $this->student_dao->add($new_student);

                if($post_success) {
                    $this->response->setStatusCode(Response::HTTP_200);
                } else {
                    $this->response->setStatusCode(Response::HTTP_400);
                }
            }
        }
    }


    // DELETE
    // ----------------------------------------
    private function handleDeleteRequest() {
        if($this->routes->getSubRouteCount() == 2) {
            // We are deleting data related to a student
            // Parse sub-routes
            $student_id = $this->routes->getRoute(1);
            $data_type = $this->routes->getEndPoint();
            if($student_id == null || $data_type == null) {
                $this->response->setStatusCode(Response::HTTP_400);
                return;
            }

            switch ($data_type) {
                case self::TERMS:
                    // Get term ID
                    $json =   json_decode( file_get_contents("php://input"));
                    $term_id = $json->term_id;

                    $success = $this->term_dao->delete($term_id);
                    if($success)
                        $this->response->setStatusCode(Response::HTTP_200);
                    else
                        $this->response->setStatusCode(Response::HTTP_500);
                    break;

                case self::TERM_COURSES:
                    $term_id = $this->routes->getRoute(3);
                    $course_id = $this->routes->getRoute(5);
                    Log::i("Removing course: {$course_id} from term: {$term_id} ");
                    break;

                default:
                    Log::e("Invalid delete request to StudentRequestProcessor: {$data_type}");
                    $this->response->setStatusCode(Response::HTTP_400);
                    return;
            }
        } else {
            // We are deleting a student
            $student_id = $this->routes->getRoute(1);
            Log::i("Deleting student: {$student_id}");

            $delete_success = $this->student_dao->delete($student_id);
            if($delete_success) {
                Log::i("Successfully deleted student");
                $this->response->setStatusCode(Response::HTTP_200);
            } else {
                $this->response->setStatusCode(Response::HTTP_400);
            }
        }
    }


    // PATCH
    // ---------------------------------------
    private function handlePatchRequest() {
        if($this->routes->hasSubRoute()) {
            // Editing user metadata
            // Parse sub-routes
            $student_id = $this->routes->getRoute(1);
            $data_type = $this->routes->getEndPoint();
            if($student_id == null || $data_type == null) {
                $this->response->setStatusCode(Response::HTTP_400);
                return;
            }

            switch ($data_type) {
                case self::TERMS:
                    Log::i("Editing term for student: {$student_id}");

                    // Get raw input
                    $input = file_get_contents("php://input");
                    // De-serialize term data
                    $term = Term::fromJSON( $input );

                    $update_result = $this->term_dao->update($term);

                    if($update_result) {
                        // Clear old term courses
                        $this->term_dao->clearTermCourses($term->getTermId());
                        // De-serialize term courses
                        $raw_json = json_decode($input);
                        foreach ($raw_json->courses as $course) {
                            // Add each course to term
                            Log::i("Got course: " . json_encode($course));
                            $assign_success = $this->term_dao->assignCourse($term->getTermId(), $course);
                            // Catch errors
                            if(!($assign_success)) {
                                // Something went wrong
                                $this->response->setStatusCode(Response::HTTP_409);
                                return;
                            }
                        }

                        Log::i("Term successfully updated");
                        $this->response->setStatusCode(Response::HTTP_200);
                    } else {
                        Log::e("Error updating term");
                        $this->response->setStatusCode(Response::HTTP_400);
                    }
                    break;

                default:
                    Log::e("User attempted invalid PATCH operation: {$data_type}");
                    $this->response->setStatusCode(Response::HTTP_400);
                    break;
            }
        } else {
            // Editing user data
            // De-serialize user data
            $user = User::fromJSON( file_get_contents("php://input") );
            if($user != null) {
                Log::i("De-serialized:\n" . $user);
                $update_success = $this->student_dao->update($user);

                if($update_success) {
                    $this->response->setStatusCode(Response::HTTP_200);
                } else {
                    $this->response->setStatusCode(Response::HTTP_400);
                }
            }
        }
    }
}
