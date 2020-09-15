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

// Error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Includes
require_once "constants.php";
require_once "log/Log.php";
require_once "rest/RouteProcessor.php";
require_once "rest/RequestProcessor.php";
require_once "rest/AssessmentRequestProcessor.php";
require_once "rest/CourseRequestProcessor.php";
require_once "rest/TermRequestProcessor.php";
require_once "rest/FacultyRequestProcessor.php";
require_once "rest/StudentRequestProcessor.php";
require_once "rest/NoteRequestProcessor.php";
require_once "rest/LoginRequestProcessor.php";

// Parse API request
Log::i("Connection made from: {$_SERVER['REMOTE_ADDR']}");
$rp = new RequestProcessor();
$rp->analyzeRequest();

// Send response back to client
print_response( $rp->getResponse() );


// Send response
function print_response(Response $response = null)
{
    // We only serve fresh, organic, locally-sourced
    // JSON here, my friend!
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");
    header("Access-Control-Allow-Methods: OPTIONS,GET,POST,PATCH,DELETE");
    header("Access-Control-Max-Age: 3600");
    header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

    if($response == null) {
        header(Response::HTTP_400);
        return;

    } else {
        header( $response->getStatusCode() );

        if( $response->hasBody() ) {
            echo $response->getBody();
        }
    }
}
