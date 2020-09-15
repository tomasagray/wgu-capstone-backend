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

class FacultyRequestProcessor
{
    private $method;
    private $routes;
    private $faculty_dao;
    private $course_dao;
    private $response;

    // Define sub-routes
    const COURSES = 'courses';

    public function __construct()
    {
        $this->method = $_SERVER['REQUEST_METHOD'];
        $this->routes = new RouteProcessor();
        $this->faculty_dao = new UserDao();
        $this->course_dao = new CourseDao();
        $this->response = new Response();
    }

    public function processRoutes()
    {
        switch ($this->method) {
            case RequestProcessor::GET_REQUEST:
                Log::i("Handling GET request ");
                $this->handleGetRequest();
                break;

            case RequestProcessor::POST_REQUEST:
                Log::i("Handling Faculty POST request");
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

    public function getResponse() {
        return $this->response;
    }


    // GET
    // --------------------------------------------------------------------------
    private function handleGetRequest()
    {
        if($this->routes->hasSubRoute()) {
            if($this->routes->getRoute(2) != null) {
                // Get data for faculty member
                Log::i("Handling GET request for faculty data");
                $faculty_id = $this->routes->getRoute(1);
                $data_type = $this->routes->getEndPoint();

                // Analyze request
                switch ($data_type) {
                    case self::COURSES:
                        Log::i("Fetching course mentorships for {$faculty_id}");
                        $courses = $this->course_dao->loadCoursesForMentor($faculty_id);

                        if($courses != null) {
                            Log::i("Successfully loaded courses for mentor: {$faculty_id}");
                            $this->response->setStatusCode(Response::HTTP_200);
                            $this->response->setBody($courses);
                        } else {
                            Log::e("No courses for mentor: {$faculty_id}");
                            $this->response->setStatusCode(Response::HTTP_404);
                        }
                        break;

                    default:
                        Log::e("User submitted invalid faculty request");
                        $this->response->setStatusCode(Response::HTTP_400);
                        break;
                }
            } else {
                // Get faculty member
                $faculty_id = $this->routes->getRoute(1);
                Log::i("Getting faculty: " . $faculty_id);
                $this->getFaculty($faculty_id);
            }
        } else {
            // Get all faculty
            Log::i("Fetching all faculty data");
            $this->getAllFaculty();
        }
    }

    private function getFaculty($faculty_id)
    {
        $faculty = $this->faculty_dao->loadFaculty($faculty_id);

        if($faculty != null) {
            Log::i("Got faculty: " . json_encode($faculty));
            $this->response->setStatusCode(Response::HTTP_200);
            $this->response->setBody( $faculty );
        } else {
            Log::e("Failed fetching data for faculty ID: " . $faculty_id);
            $this->response->setStatusCode(Response::HTTP_404);
        }
    }

    private function getAllFaculty()
    {
        $faculty = $this->faculty_dao->loadAllFaculty();

        if($faculty != null) {
            Log::i("Successfully loaded all faculty data");
            Log::i("Faculty Data: " . json_encode($faculty));

            $this->response->setStatusCode(Response::HTTP_200);
            $this->response->setBody( $faculty );
        } else {
            Log::e("Failed fetching data for all faculty");
            $this->response->setStatusCode(Response::HTTP_404);
        }
    }


    // POST
    // --------------------------------------------------------------------------
    private function handlePostRequest()
    {
        // De-serialize POST data
        $faculty = User::fromJSON( file_get_contents("php://input") );
        if($faculty != null) {
            Log::i("De-serialized Faculty:\n" . $faculty);
            // Add to DB
            $post_success = $this->faculty_dao->add($faculty);

            if($post_success) {
                Log::i("Successfully saved faculty to DB");
                $this->response->setStatusCode(Response::HTTP_200);
            } else {
                $this->response->setStatusCode(Response::HTTP_400);
            }
        }
    }


    // PATCH
    // --------------------------------------------------------------------------
    private function handlePatchRequest()
    {
        if($this->routes->hasSubRoute()) {
            $faculty_id = $this->routes->getRoute(1);
            // Read mentorships
            $mentorships = json_decode( file_get_contents("php://input") );
            Log::i("Read mentor data: " . json_encode($mentorships));

            // Clear old mentorships
            $this->course_dao->clearMentorCourses($faculty_id);

            foreach ($mentorships as $mentorship) {
                $update_success = $this->course_dao->assignMentor($mentorship->course_id, $faculty_id);
                // Catch errors; failure assumed to be conflict
                if(!$update_success) {
                    $this->response->setStatusCode(Response::HTTP_409);
                    return;
                }
            }

            $this->response->setStatusCode(Response::HTTP_200);

        } else {
            // Illogical PATCH request
            Log::e("Invalid PATCH request to faculty processor");
            $this->response->setStatusCode(Response::HTTP_400);
        }
    }


    // DELETE
    // --------------------------------------------------------------------------
    private function handleDeleteRequest()
    {
        // Get faculty ID
        $faculty_id = $this->routes->getEndPoint();
        Log::i("User attempting to delete faculty: {$faculty_id}");

        $delete_success = $this->faculty_dao->delete($faculty_id);
        if($delete_success) {
            Log::i("Successfully deleted faculty");
            $this->response->setStatusCode(Response::HTTP_200);
        } else {
            $this->response->setStatusCode(Response::HTTP_400);
        }
    }
}
