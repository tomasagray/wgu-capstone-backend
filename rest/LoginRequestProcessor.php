<?php
namespace Capstone;

require_once "rest/Response.php";
require_once "rest/RouteProcessor.php";
require_once "data/access/UserDao.php";

class LoginRequestProcessor
{
    // Hash algorithm
    const HASH_METHOD = 'sha256';

    private $method;
    private $routes;
    private $response;
    private $user_dao;

    public function __construct()
    {
        $this->method = $_SERVER['REQUEST_METHOD'];
        $this->routes = new RouteProcessor();
        $this->response = new Response();
        $this->user_dao = new UserDao();
    }

    public function getResponse() {
        return $this->response;
    }

    public function login()
    {
        $auth_heads = self::getAuthHeaders();

        // Get user data from DB
        Log::s("Login:" . $auth_heads['user_id']);
        $user = $this->user_dao->loadLoginDataByEmail($auth_heads['user_id']);

        Log::s("Loaded user: " . json_encode($user));

        if($user != null) {

            // Generate server-side hash of login data
            $hash = hash_hmac(
                self::HASH_METHOD,
                $user['email'],
                $user['password'],
                true
            );
            $key = base64_decode($auth_heads['key']);
            // If the user has submitted correct information, the server-generated
            // hash should match the client hash
            if($key == $hash) {
                $userData = [
                    'user_id' => $user['user_id'],
                    'token' => self::generateToken($user),
                    'user' => $user
                ];
                $this->response->setBody( $userData );
                $this->response->setStatusCode(Response::HTTP_200);

                Log::s("Sending response:" . json_encode($userData));

            } else {
                $k = base64_encode($key);
                $h = base64_encode($hash);
                Log::s(
                    "Could not log in user: {$auth_heads['user_id']}\n"
                            ."\tHash does not match; Client: {$k}, Server: {$h}"
                );
                $this->response->setStatusCode(Response::HTTP_403);
            }
        } else {
            Log::s("Failed to load user data for: {$auth_heads['user_id']}");
            $this->response->setStatusCode(Response::HTTP_403);
        }
    }


    public function isAuthorized()
    {
        $auth_heads = self::getAuthHeaders();
        // Attempt to load user data
        $attempt_user = $this->user_dao->loadLoginDataById($auth_heads['user_id']);

        if($auth_heads != null && $attempt_user != null) {
            // Ensure correct token submitted
            $attempt_token = self::generateToken($attempt_user);

            Log::s("Comparing keys for {$auth_heads['user_id']}: {$attempt_token} / {$auth_heads['key']}");

            if($attempt_token == $auth_heads['key']) {
                Log::s("Access granted to {$auth_heads['user_id']} @ {$_SERVER['REMOTE_ADDR']}");
                return true;
            } else {
                Log::s("Keys do not match! Access denied!");
                return false;
            }
        }

        // Unauthorized
        Log::s("!!! Unauthorized access attempted from {$_SERVER['REMOTE_ADDR']} !!!");
        return false;
    }

    public static function getAuthHeaders()
    {
        // Get headers
        $headers = getallheaders();
        Log::i("AUTH HEADERS: " . json_encode($headers));
        // Check for auth header
        if(!isset($headers['Authorization']) || $headers['Authorization'] == "") {
            Log::s("Auth header not set");
            return null;
        }

        // Get authorization header
        $auth_key = $headers['Authorization'];
        Log::s("Login attempt with auth header: {$auth_key} from: {$_SERVER['REMOTE_ADDR']}");
        $auth_data = explode(':', $auth_key);

        // Verify we got two pieces
        if(count($auth_data) != 2)
            return null;

        return [
            'user_id' => $auth_data[0],
            'key'   => $auth_data[1]
        ];
    }

    public static function generateToken($user)
    {
        // Token string
        $str =
            $user['user_id'] . ':' .
            $user['email'] . ':' .
            $user['phone'] . ':' .
            $user['password'];

        $token = hash_hmac(
            self::HASH_METHOD,
            $str,
            $user['password'],
            true
        );

        return base64_encode($token);
    }
}