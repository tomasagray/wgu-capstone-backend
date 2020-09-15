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

require_once "data/access/CourseDao.php";
require_once "data/access/AssessmentDao.php";
require_once "rest/Response.php";
require_once "rest/RouteProcessor.php";

class CourseRequestProcessor
{
    private $method;
    private $routes;
    private $course_dao;
    private $assessment_dao;
    private $response;

    // Define sub-routes
    const ASSESSMENTS = 'assessments';
    const NOTES = 'notes';

    public function __construct()
    {
        $this->method = $_SERVER['REQUEST_METHOD'];
        $this->routes = new RouteProcessor();
        $this->course_dao = new CourseDao();
        $this->assessment_dao = new AssessmentDao();
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

            case RequestProcessor::PATCH_REQUEST:
                Log::i("Handling PATCH request");
                $this->handlePatchRequest();
                break;

            case RequestProcessor::DELETE_REQUEST:
                Log::i("Handling DELETE request");
                $this->handleDeleteRequest();
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
        if($this->routes->hasSubRoute())
        {
            Log::i("Getting course metadata for course: {$this->routes->getRoute(1)}");
            // Get specific course
            if($this->routes->getSubRouteCount() == 1) {
                $course_id = $this->routes->getRoute(1);
                Log::i("Getting course: " . $course_id);
                $this->getCourse($course_id);

            }
            else if($this->routes->getSubRouteCount() == 2) {

                // Get related course data
                $sub_route = $this->routes->getRoute(2);

                // Get assessments for this course
                if ($sub_route == self::ASSESSMENTS) {
                    $this->getCourseAssessments();

                } else if ($sub_route == 'mentor') {
                    // TODO: Get mentor data for this course, or delete this
                } else {
                    // Invalid request
                    $this->response->setStatusCode(Response::HTTP_400);
                }
            }
        } else {
            // Get all courses as a collection
            Log::i("Fetching all courses");
            $this->getAllCourses();
        }
    }

    private function getCourse($course_id)
    {
        $course = $this->course_dao->load($course_id);

        if ($course != null) {
            $this->response->setStatusCode(Response::HTTP_200);
            $this->response->setBody( $course );

        } else {
            Log::e("Could not retrieve data for course ID: " . $course_id);
            $this->response->setStatusCode(Response::HTTP_404);
        }
    }
    // TODO: Combine these
    private function getAllCourses()
    {
        $courses = $this->course_dao->loadAll();

        if($courses != null) {
            Log::i("Retrieved courses data: " . json_encode($courses) );
            $this->response->setStatusCode(Response::HTTP_200);
            $this->response->setBody( $courses );

        } else {
            Log::e("Could not load all courses data");
            $this->response->setStatusCode(Response::HTTP_404);
        }
    }

    private function getCourseAssessments()
    {
        $course_id = $this->routes->getRoute(1);
        Log::i("Getting assessments for course: " . $course_id);
        $assessments = $this->assessment_dao->loadAssessmentsForCourse($course_id);
        Log::i("Got assessments: " . count($assessments));

        if($assessments != null) {
            Log::i("Got assessments for course: {$course_id}:\n" . json_encode($assessments));
            $this->response->setStatusCode(Response::HTTP_200);
            $this->response->setBody( $assessments);
        } else {
            Log::e(
                "Could not fetch assessments for course: " . $course_id
            );
            $this->response->setStatusCode(Response::HTTP_404);
        }
    }


    // POST
    // -----------------------------------------
    private function handlePostRequest()
    {
        Log::i("Handling POST request for courses");

        // If we are posting course-related data
        if($this->routes->hasSubRoute()) {
            // Parse sub-routes
            $course_id = $this->routes->getRoute(1);
            $data_type = $this->routes->getRoute(2);

            // Ensure valid routes
            if($course_id == null || $data_type == null) {
                $this->response->setStatusCode(Response::HTTP_400);
                return;
            }

            switch ($data_type) {
                case self::ASSESSMENTS:
                    Log::i("POSTing an assessment for course: {$course_id}");
                    $assessment = Assessment::fromJSON( file_get_contents("php://input") );
                    if($assessment != null) {
                        // Add assessment to DB
                        $result = $this->assessment_dao->save($assessment);

                        if($result) {
                            $this->response->setStatusCode(Response::HTTP_200);
                        } else {
                            $this->response->setStatusCode(Response::HTTP_400);
                        }
                    }

                    break;

                case self::NOTES:
                    // TODO: handle notes POST
                    Log::i("Notes POST request made for course: {$course_id}");
                    break;

                default:
                    $this->response->setStatusCode(Response::HTTP_400);
                    break;
            }

            // If we are posting a course...
        } else {
            // De-serialize course from JSON
            $course = Course::fromJSON(file_get_contents("php://input"));
            if ($course != null) {
                //            Log::i("De-serialized: " . $course);
                // Add course to DB
                $result = $this->course_dao->save($course);

                if ($result) {
                    $this->response->setStatusCode(Response::HTTP_200);
                } else {
                    $this->response->setStatusCode(Response::HTTP_400);
                }
            } else {
                $this->response->setStatusCode(Response::HTTP_400);
            }
        }
    }

    // PATCH
    // -----------------------------------------
    private function handlePatchRequest()
    {
        // Analyze update type
        if($this->routes->getRoute(2) != null)
        {
            $course_id = $this->routes->getRoute(1);
            $data_type = $this->routes->getRoute(2);
            Log::i("Patching data for course: {$course_id}, type: {$data_type}");

            // Determine type of metadata to update
            switch ($data_type) {
                case self::ASSESSMENTS:
                    // De-serialize assessment data
                    $assessment = Assessment::fromJSON( file_get_contents("php://input"));
                    Log::i("Updating assessment: {$assessment->getAssessmentId()} ");

                    if($assessment != null) {
                        $update_success = $this->assessment_dao->update($assessment);

                        if($update_success) {
                            $this->response->setStatusCode(Response::HTTP_200);
                            Log::i("Assessment updated successfully");

                        } else {
                            $this->response->setStatusCode(Response::HTTP_404);
                        }
                    } else {
                        $this->response->setStatusCode(Response::HTTP_400);
                    }
                    break;

                default:
                    Log::e("Bad request made from {$_SERVER['REMOTE_ADDR']}");
                    $this->response->setStatusCode(Response::HTTP_400);
                    break;
            }
        } else {
            // Update course date
            // De-serialize course data
            $course = Course::fromJSON( file_get_contents("php://input") );
            Log::i("De-serialized: COURSE = " . json_encode($course));
            if($course != null) {
                // Send to DB
                $update_success = $this->course_dao->update($course);

                if($update_success) {
                    $this->response->setStatusCode(Response::HTTP_200);
                    Log::i("Successfully updated course {$course->getCourseId()}");
                } else {
                    $this->response->setStatusCode(Response::HTTP_400);
                    Log::e("Could not update course: {$course->getCourseId()}");
                }
            } else {
                $this->response->setStatusCode(Response::HTTP_400);
                Log::e("Could not understand user request");
            }
        }
    }

    // DELETE
    // ----------------------------------------
    private function handleDeleteRequest()
    {
        if( $this->routes->getSubRouteCount() == 2 ) {
            // We are deleting course metadata
            $course_id = $this->routes->getRoute(1);
            $data_type = $this->routes->getRoute(2);
            Log::i("Deleting data for course: {$course_id}, type: {$data_type}");

            // Analyze request data type
            switch ($data_type) {
                case self::ASSESSMENTS:
                    $assessment_id = $this->routes->getRoute(3);
                    if($assessment_id != null) {
                        $delete_success = $this->assessment_dao->delete($assessment_id);

                        if($delete_success) {
                            Log::i("Deleted assessment: {$assessment_id}");
                            $this->response->setStatusCode(Response::HTTP_200);
                        } else {
                            $this->response->setStatusCode(Response::HTTP_400);
                        }
                    } else {
                        $this->response->setStatusCode(Response::HTTP_400);
                    }

                    break;

                default:
                    Log::e("User made a bad delete request");
                    $this->response->setStatusCode(Response::HTTP_400);
                    break;
            }

        } else {
            // We are deleting a course, with metadata as well
            // Get course ID
            $course_id = $this->routes->getRoute(1);
            Log::i("Deleting course: {$course_id}");

            if ($course_id != null) {
                $delete_success = $this->course_dao->delete($course_id);

                if ($delete_success) {
                    Log::i("Course {$course_id} deleted");
                    $this->response->setStatusCode(Response::HTTP_200);
                } else {
                    $this->response->setStatusCode(Response::HTTP_400);
                }
            } else {
                Log::e("Requested course ID was null");
                $this->response->setStatusCode(Response::HTTP_400);
            }
        }
    }
}
