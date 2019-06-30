<?php
namespace Capstone;


require_once "rest/Response.php";
require_once "rest/RouteProcessor.php";
require_once "rest/RequestProcessor.php";
require_once "data/access/DocumentDao.php";

class UniversityRequestProcessor
{
    private $method;
    private $routes;
    private $response;
    private $document_dao;

    // Document file size limit (bytes)
    const DOC_FILE_SIZE_MAX = 500000;
    // Address storage location
    const ADDRESS_FILE = 'storage/address.json';

    // Define sub-routes
    const DOCUMENTS = 'documents';
    const ADDRESS = 'address';

    // Constructor
    // ----------------------------------------------
    public function __construct()
    {
        $this->method = $_SERVER['REQUEST_METHOD'];
        $this->routes = new RouteProcessor();
        $this->response = new Response();
        $this->document_dao = new DocumentDao();
    }

    public function getResponse() {
        return $this->response;
    }

    /**
     * Parse the routes and determine the nature of the request.
     *
     */
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


    // GET
    // -----------------------------------------------------------------------
    private function handleGetRequest()
    {
        $data_type = $this->routes->getEndPoint();
        switch ($data_type) {
            case self::DOCUMENTS:
                // Read docs
                Log::i("Loading documents");
                $docs = $this->document_dao->loadAll();

                if($docs != null) {
                    Log::i("Successfully loaded all documents");
                    $this->response->setStatusCode(Response::HTTP_200);
                    $this->response->setBody($docs);

                } else {
                    Log::i("Did not find any documents");
                    $this->response->setStatusCode(Response::HTTP_404);
                }
                break;

            case self::ADDRESS:
                Log::i("Loading address data from storage");
                $address = file_get_contents(self::ADDRESS_FILE);

                if($address) {
                    Log::i("Read address data: " . json_encode($address));
                    $this->response->setBody($address);
                    $this->response->setStatusCode(Response::HTTP_200);
                } else {
                    Log::e("Could not load address data!");
                    $this->response->setStatusCode(Response::HTTP_500);
                }
                break;

            default:
                Log::e("User submitted invalid request for data");
                $this->response->setStatusCode(Response::HTTP_400);
                break;
        }
    }

    // POST
    // -----------------------------------------------------------------------
    private function handlePostRequest()
    {
        // Determine post type
        $post_type = $this->routes->getEndPoint();
        switch ($post_type) {
            case self::DOCUMENTS:
                // TODO: Add file type validation

                // Read document post
                $upload_size = (int) $_SERVER['CONTENT_LENGTH'];
                if($upload_size < self::DOC_FILE_SIZE_MAX) {
                    Log::i("Data: " . json_encode( $_POST['title'] ));
                    Log::i("Loaded data; size: " . $upload_size);
                    $result = $this->savePostDocument();

                    if($result) {
                        Log::i("Document upload complete");
                        $this->response->setStatusCode(Response::HTTP_200);
                    } else {
                        $this->response->setStatusCode(Response::HTTP_500);
                    }

                } else {
                    Log::e("User attempted to upload too much data");
                    $this->response->setStatusCode(Response::HTTP_413);
                }
                break;

            default:
                Log::e("User submitted an invalid post");
                $this->response->setStatusCode(Response::HTTP_400);
                break;
        }
        // Validate data
        // Save data
        // Send response
    }

    private function savePostDocument()
    {
        // Read file from input stream
        $doc = $_FILES['file'];
        $id = generateUUID();
        // Create file identifier
        $data = [
            'document_id' => $id,
            'title' => $_POST['title'],
            'description' => $_POST['description'],
            'file_name' => $doc['name'],
            'file_type' => $doc['type'],
            'file_size' => $doc['size']
        ];

        // Update DB
        $db_update = $this->document_dao->save($data);

        if($db_update) {
            Log::i("Database updated successfully");
            // Save file to storage
            $file_name = $id . '-' . $doc['name'];
            $file_path = DocumentDao::STORAGE_LOCATION . $file_name;

            if( move_uploaded_file($doc['tmp_name'], $file_path) ) {
                Log::i("File uploaded successfully to storage");
                return true;

            } else {
                // Rollback DB update
                $this->document_dao->delete($id);
                Log::e("Error saving file to local filesystem!");
            }
        }

        return false;
    }

    // DELETE
    // -----------------------------------------------------------------------
    private function handleDeleteRequest()
    {
        $data_type = $this->routes->getRoute(1);
        switch ($data_type) {
            case self::DOCUMENTS:
                $doc_id = $this->routes->getEndPoint();
                Log::i("Deleting document: {$doc_id}");

                $delete_success = $this->document_dao->delete($doc_id);

                if($delete_success) {
                    Log::i("Successfully deleted document: {$doc_id}");
                    $this->response->setStatusCode(Response::HTTP_200);
                } else {
                    Log::e("Could not delete document for  ID: {$doc_id}");
                    $this->response->setStatusCode(Response::HTTP_400);
                }
                break;

            default:
                Log::e("Invalid delete request to UniversityRequestProcessor");
                $this->response->setStatusCode(Response::HTTP_400);
                break;
        }
    }

    // PATCH
    // ----------------------------------------------------------------------
    private function handlePatchRequest()
    {
        $data_type = $this->routes->getEndPoint();
        switch ($data_type) {
            case self::ADDRESS:
                Log::i("Updating address data");
                Log::i("Read from input stream: " . file_get_contents("php://input"));
                // Ensure we have valid JSON
                $json = file_get_contents("php://input");
                // Save to local filesystem
                $save_result = file_put_contents(
                    self::ADDRESS_FILE,
                    $json
                );

                if($save_result > 0) {
                    Log::i("Successfully saved address");
                    $this->response->setStatusCode(Response::HTTP_200);
                } else {
                    Log::e("Error saving address to local storage!");
                    $this->response->setStatusCode(Response::HTTP_500);
                }
                break;

            default:
                Log::e("User attempting invalid patch request to University data");
                $this->response->setStatusCode(Response::HTTP_400);
                break;
        }
    }
}