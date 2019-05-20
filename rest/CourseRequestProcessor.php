<?php
namespace Capstone;

require_once "data/access/CourseDao.php";
require_once "rest/Response.php";
require_once "rest/RouteProcessor.php";

class CourseRequestProcessor
{
    private $method;
    private $routes;
    private $course_dao;
    private $response;

    public function __construct($request_method, RouteProcessor $routes)
    {
        $this->method = $request_method;
        $this->routes = $routes;
        $this->course_dao = new CourseDao(Database::getInstance());
        $this->response = new Response();
    }

    public function processRoutes()
    {
        if($this->routes->hasSubRoute())
        {
            // Get specific course
            $course_id = $this->routes->getRoute(1);
            Log::i("Getting course: " .$course_id);
            $this->getCourse($course_id);

        } else {
            // Get all courses as a collection
            Log::i("Fetching all courses");
            $this->getAllCourses();
        }
    }

    public function getResponse() {
        return $this->response;
    }

    private function getCourse($course_id)
    {
        $course = $this->course_dao->load($course_id);

        if ($course != null) {
            Log::i("Retrieved course:\n" . json_encode($course));
            $this->response->setStatusCode(Response::HTTP_200);
            $this->response->setBody( json_encode($course) );

        } else {
            Log::e("Could not retrieve data for course ID: " . $course_id);
            $this->response->setStatusCode(Response::HTTP_404);
        }
    }

    private function getAllCourses()
    {
        $courses = $this->course_dao->loadAll();

        if($courses != null) {
            Log::i("Retrieved courses data: " + json_encode($courses) );
            $this->response->setStatusCode(Response::HTTP_200);
            $this->response->setBody( json_encode($courses) );

        } else {
            Log::e("Could not load all courses data");
            $this->response->setStatusCode(Response::HTTP_404);
        }
    }
}