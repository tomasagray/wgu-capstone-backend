<?php
namespace Capstone;

require_once "data/access/StudentDao.php";
require_once "rest/Response.php";
require_once "rest/RouteProcessor.php";

class StudentRequestProcessor
{
    private $method;
    private $routes;
    private $student_dao;
    private $response;

    public function __construct($request_method, RouteProcessor $routes)
    {
        $this->method = $request_method;
        $this->routes = $routes;
        $this->student_dao = new StudentDao(Database::getInstance());
        $this->response = new Response();
    }

    public function getResponse() {
        return $this->response;
    }

    public function processRoutes()
    {
        if($this->routes->hasSubRoute()) {
            // Get specific student
            $student_id = $this->routes->getRoute(1);
            Log::i("Fetching data for student: " . $student_id);
            $this->getStudent($student_id);

        } else {
            Log::i("Fetching all student data");
            $this->getAllStudents();
        }
    }

    private function getStudent($student_id)
    {
        $student = $this->student_dao->load($student_id);

        if($student != null) {
            Log::i("Successfully retrieved data for student: " . $student_id);
            $this->response->setStatusCode(Response::HTTP_200);
            $this->response->setBody( json_encode($student) );
        } else {
            Log::e("Failed to retrieve data for student: ". $student_id);
            $this->response->setStatusCode(Response::HTTP_404);
        }
    }

    private function getAllStudents()
    {
        $students = $this->student_dao->loadAll();

        if($students != null) {
            Log::i("Successfully retrieved data for all students");
            $this->response->setStatusCode(Response::HTTP_200);
            $this->response->setBody( json_encode($students) );
        } else {
            Log::e("Failed to retrieve all student data");
            $this->response->setStatusCode(Response::HTTP_404);
        }
    }
}