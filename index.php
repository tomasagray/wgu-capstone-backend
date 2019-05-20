<?php
namespace Capstone;

// Error reporting
//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


// We only serve fresh, organic, locally-sourced
// JSON here, my friend!
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: OPTIONS,GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Includes
require_once "constants.php";
require_once "log/Log.php";
require_once "rest/RouteProcessor.php";
require_once "rest/AssessmentRequestProcessor.php";
require_once "rest/CourseRequestProcessor.php";
require_once "rest/TermRequestProcessor.php";
require_once "rest/FacultyRequestProcessor.php";
require_once "rest/StudentRequestProcessor.php";

Log::i("Connection made to: " . $_SERVER['REQUEST_URI']);

// Global fields
$routes = new RouteProcessor();
$processor = null;
$response = null;

// Process base route
switch($routes->getBaseRoute())
{
    case "terms":
        // Send to term processor
        Log::i("Term request made");
        $processor = new TermRequestProcessor($_SERVER['REQUEST_METHOD'], $routes);
        $processor->processRoutes();
        print_response( $processor->getResponse() );
        break;

    case "courses":
        // Send to courses processor
        Log::i("Course request made");
        $processor = new CourseRequestProcessor($_SERVER['REQUEST_METHOD'], $routes);
        $processor->processRoutes();
        print_response( $processor->getResponse() );
        break;

    case "assessments":
        // Send to assessment processor
        Log::i("Assessment request made");
        $processor = new AssessmentRequestProcessor($_SERVER['REQUEST_METHOD'], $routes);
        $processor->processRoutes();
        print_response( $processor->getResponse() );
        break;

    case "students":
        // Send to students processor
        Log::i("Student request made");
        $processor = new StudentRequestProcessor($_SERVER['REQUEST_METHOD'], $routes);
        $processor->processRoutes();
        print_response( $processor->getResponse() );
        break;

    case "faculty":
        // Send to faculty processor
        Log::i("Faculty request made");
        $processor = new FacultyRequestProcessor($_SERVER['REQUEST_METHOD'], $routes);
        $processor->processRoutes();
        print_response( $processor->getResponse() );
        break;

    case "address":
        // Send to address processor
        Log::i("Address request made");
        $processor = new AddressRequestProcessor($_SERVER['REQUEST_METHOD'], $routes);
        $processor->processRoutes();
        print_response( $processor->getResponse() );
        break;

    case "images":
        // Send to image processor
        break;

    default:
        Log::e("User attempted to connect to invalid URL");
        header(Response::HTTP_404 );
        exit();
}

// Send response
function print_response(Response $response)
{
    header( $response->getStatusCode() );
    if( $response->hasBody() ) {
        Log::i("Sending response: " . $response->getBody());
        echo $response->getBody();
    }
}
