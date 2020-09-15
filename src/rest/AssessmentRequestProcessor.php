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

require_once "data/access/AssessmentDao.php";
require_once "rest/Response.php";
require_once "rest/RouteProcessor.php";

// TODO: Delete this class?
class AssessmentRequestProcessor
{
    private $method;
    private $routes;
    private $assessment_dao;
    private $response;

    public function __construct($request_method, RouteProcessor $routes)
    {
        $this->method = $request_method;
        $this->routes = $routes;
        $this->assessment_dao = new AssessmentDao();
        $this->response = new Response();
    }

    public function getResponse() {
        return $this->response;
    }

    public function processRoutes()
    {
        if($this->routes->hasSubRoute()) {
            // Get specific assessment
            $assessment_id = $this->routes->getRoute(1);
            Log::i("Fetching data for assessment: " . $assessment_id);
            $this->getAssessment($assessment_id);
        } else {
            Log::i("Fetching all assessment data");
            $this->getAllAssessments();
        }
    }

    private function getAssessment($assessment_id)
    {
        $assessment = $this->assessment_dao->load($assessment_id);

        if($assessment != null) {
            Log::i("Successfully retrieved data for assessment: " . $assessment);
            $this->response->setStatusCode(Response::HTTP_200);
            $this->response->setBody( $assessment );
        } else {
            Log::e("Error loading data for assessment: " . $assessment_id);
            $this->response->setStatusCode(Response::HTTP_404);
        }
    }

    private function getAllAssessments()
    {
        $assessments = $this->assessment_dao->loadAll();
        if($assessments != null) {
            Log::i("Successfully retrieved all assessment data");
            $this->response->setStatusCode(Response::HTTP_200);
            $this->response->setBody( $assessments );
        } else {
            Log::e("Error getting all assessment data from DB");
            $this->response->setStatusCode(Response::HTTP_404);
        }
    }
}
