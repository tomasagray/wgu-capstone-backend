<?php
namespace Capstone;

require_once "rest/UniversityRequestProcessor.php";
require_once "rest/ReportRequestProcessor.php";

class RequestProcessor
{
    // Request types
    const GET_REQUEST = "GET";
    const POST_REQUEST = "POST";
    const PATCH_REQUEST = "PATCH";
    const DELETE_REQUEST = "DELETE";

    // Define base routes
    const LOGIN = "login";
    const STUDENTS = "students";
    const COURSES = "courses";
    const FACULTY = "faculty";
    const UNIVERSITY = "university";
    const REPORTS = "reports";


    // Determine resource  type
    // IF resource type == LOGIN
    //     Send to login processor
    //
    // Ensure user is authorized to THIS ACTION on THIS RESOURCE
    // If no, return 403
    //
    // Send to appropriate type processor
    // Type processor:
    //    Determines action type
    //    Performs action

    private $route_processor;
    private $login_processor;
    private $request_processor;
    private $response;

    public function __construct(){
        Log::i("Initializing request processor");
        $this->route_processor = new RouteProcessor();
        $this->login_processor = new LoginRequestProcessor();
    }

    public function analyzeRequest()
    {
        $request_type = $this->route_processor->getRequestType();

        // Special case: login
        if($request_type == self::LOGIN) {
            // Send to login processor
            Log::i("Sending to login processor");
            $this->login_processor->login();
            // Get response
            $this->response = $this->login_processor->getResponse();

            return;
        }

        // ... otherwise proceed
        if( $this->login_processor->isAuthorized() ) {
            switch ( $request_type ) {
                case self::STUDENTS:
                    // Send to student processor
                    $this->request_processor = new StudentRequestProcessor();
                    $this->request_processor->processRoutes();
                    $this->response = $this->request_processor->getResponse();
                    break;

                case self::FACULTY:
                    $this->request_processor = new FacultyRequestProcessor();
                    $this->request_processor->processRoutes();
                    $this->response = $this->request_processor->getResponse();
                    break;

                case self::COURSES:
                    // Send to course processor
                    $this->request_processor = new CourseRequestProcessor();
                    $this->request_processor->processRoutes();
                    $this->response = $this->request_processor->getResponse();
                    break;

                case self::UNIVERSITY:
                    // Send to university data processor
                    $this->request_processor = new UniversityRequestProcessor();
                    $this->request_processor->processRoutes();
                    $this->response = $this->request_processor->getResponse();
                    break;

                case self::REPORTS:
                    // Send to reports processor
                    $this->request_processor = new ReportRequestProcessor();
                    $this->request_processor->processRoutes();
                    $this->response = $this->request_processor->getResponse();
                    break;

                default:
                    Log::e(
                        "User attempted to connect to invalid URL:"
                        ."\nClient IP: ". $_SERVER['REMOTE_ADDR']
                        ."\nURL: " . $_SERVER['REQUEST_URI']
                    );
                    header(Response::HTTP_400 );
                    exit();

            }
        } else {
            $this->response = new Response();
            $this->response->setStatusCode(Response::HTTP_403);
        }
    }

    public function getResponse() {
        return $this->response;
    }
}