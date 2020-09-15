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

require_once "data/access/ReportDao.php";
require_once "rest/Response.php";
require_once "rest/RouteProcessor.php";
require_once "rest/RequestProcessor.php";

class ReportRequestProcessor
{
    private $routes;
    private $response;
    private $report_dao;

    // Define sub-routes; report types
    const COURSE_STATUS_DIST = 'course_status_distribution';
    const ENROLLMENTS_PER_MONTH = 'enrollments_per_month';
    const STUDENTS_PER_COURSE = 'students_per_course';

    public function __construct()
    {
        $this->routes = new RouteProcessor();
        $this->response = new Response();
        $this->report_dao = new ReportDao();
    }

    public function getResponse() {
        return $this->response;
    }

    public function processRoutes()
    {
        // Only GET requests expected
        if($_SERVER['REQUEST_METHOD'] !== 'GET') {
            Log::e("User attempted unauthorized report method: {$_SERVER['REQUEST_METHOD']}");
            $this->response->setStatusCode(Response::HTTP_403);
            return;
        }

        // Determine report type requested
        $report_type = $this->routes->getEndPoint();

        switch ($report_type) {
            case self::COURSE_STATUS_DIST:
                Log::i("Fetching data for course status report");
                $data = $this->report_dao->getCourseStatusDistribution();
                if($data != null) {
                    $this->response->setBody( $data );
                    $this->response->setStatusCode(Response::HTTP_200);
                }
                break;

            case self::ENROLLMENTS_PER_MONTH:
                Log::i("Fetching enrollment report data");
                $data = $this->report_dao->getEnrollmentsPerMonth();
                if($data != null) {
                    $this->response->setBody( $data );
                    $this->response->setStatusCode(Response::HTTP_200);
                }
                break;

            case self::STUDENTS_PER_COURSE:
                Log::i("Fetching data for student per course report");
                $data = $this->report_dao->getStudentsPerCourse();
                if($data != null) {
                    $this->response->setBody($data);
                    $this->response->setStatusCode(Response::HTTP_200);
                }
                break;

            default:
                Log::e("User submitted invalid report request");
                $this->response->setStatusCode(Response::HTTP_400);
                break;
        }
    }
}
