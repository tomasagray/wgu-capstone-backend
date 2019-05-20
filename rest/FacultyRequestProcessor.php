<?php
namespace Capstone;

require_once "data/access/FacultyDao.php";
require_once "rest/Response.php";
require_once "rest/RouteProcessor.php";

class FacultyRequestProcessor
{
    private $method;
    private $routes;
    private $faculty_dao;
    private $response;

    public function __construct($request_method, RouteProcessor $routes)
    {
        $this->method = $request_method;
        $this->routes = $routes;
        $this->faculty_dao = new FacultyDao(Database::getInstance());
        $this->response = new Response();
    }

    public function processRoutes()
    {
        Log::i("HEre we are!");
        if($this->routes->hasSubRoute()) {
            $faculty_id = $this->routes->getRoute(1);
            Log::i("Getting faculty: " . $faculty_id);
            $this->getFaculty($faculty_id);

        } else {
            Log::i("Fetching all faculty data");
            $this->getAllFaculty();
        }
    }

    public function getResponse() {
        return $this->response;
    }

    private function getFaculty($faculty_id)
    {
        $faculty = $this->faculty_dao->load($faculty_id);

        if($faculty != null) {
            Log::i("Got faculty: " . $faculty);
            $this->response->setStatusCode(Response::HTTP_200);
            $this->response->setBody( json_encode($faculty) );
        } else {
            Log::e("Failed fetching data for faculty ID: " . $faculty_id);
            $this->response->setStatusCode(Response::HTTP_404);
        }
    }

    private function getAllFaculty()
    {
        $faculty = $this->faculty_dao->loadAll();

        if($faculty != null) {
            Log::i("Successfully loaded all faculty data");
            $this->response->setStatusCode(Response::HTTP_200);
            $this->response->setBody( json_encode($faculty) );
        } else {
            Log::e("Failed fetching data for all faculty");
            $this->response->setStatusCode(Response::HTTP_404);
        }
    }
}