<?php
/**
 * E-POSTBUSINESS API integration for Contao Open Source CMS
 * Copyright (c) 2015-2016 Richard Henkenjohann
 * @package E-POST
 * @author  Richard Henkenjohann <richard-epost@henkenjohann.me>
 */

namespace CleverreachSync;


abstract class AbstractApi
{

    /**
     * Current object instance (Singleton)
     *
     * @type AbstractApi
     */
    protected static $instance;


    /**
     * @var string
     */
    protected $apiHost = 'https://rest.cleverreach.com';


    /**
     * @var string
     */
    protected $token;


    /**
     * Authenticate and set token
     */
    public function __construct()
    {
        if (!\Config::get('cr_active')) {
            throw new \LogicException('CleverReach is not activated and configured in system configuration');
        }

        // Login sets required token
        $this->login
        (
            \Config::get('cr_client_id'),
            \Config::get('cr_login'),
            \Encryption::decrypt(
                \Config::get('cr_password')
            ) //@todo it is not secure to save the password where the encryption key is saved (file system/localconfig)
        );
    }


    /**
     * Instantiate the AbstractApi instance
     *
     * @return static
     */
    public static function getInstance()
    {
        if (null === static::$instance) {
            static::$instance = new static();
        }

        return static::$instance;
    }


    /**
     * Set the token by logging in
     *
     * @param integer $client_id
     * @param string  $login
     * @param string  $password
     *
     * @return bool
     */
    public function login($client_id, $login, $password)
    {
        $request = $this->callEndpoint(
            '/v1/login.json',
            array
            (
                'client_id' => $client_id,
                'login'     => $login,
                'password'  => $password,
            )
        );

        if (!$this->hasRequestError($request)) {
            // Set token
            $this->token = trim($request->response, '"');

            return true;
        }

        return false;
    }


    /**
     * Refresh the token using the current token
     *
     * @return bool
     */
    public function refreshToken()
    {
        $request = $this->callEndpoint('/v1/login/refresh.json', [], 'POST');

        if (!$this->hasRequestError($request)) {
            // Set token
            $this->token = trim($request->response, '"');

            return true;
        }

        return false;
    }


    /**
     * Call an endpoint and return request instance
     *
     * @param string                       $endpoint
     * @param array|\JsonSerializable|null $data
     * @param string                       $method
     *
     * @return \Request
     */
    protected function callEndpoint($endpoint, $data = array(), $method = 'GET')
    {
        $request = new \Request();

        // Set given data json encoded
        if (!empty($data)) {
            $request->data = json_encode($data);

            if ('GET' === $method) {
                $method = 'POST';
            }
        }

        // Set current token as Authentication bearer
        if (strlen($this->token)) {
            $request->setHeader('Authorization', 'Bearer '.$this->token);
        }

        // Send request
        $request->send($this->apiHost.$endpoint, null, $method);

        // Receiving method has to check for errors
        return $request;
    }


    /**
     * Check for error in request and log error
     *
     * @param \Request $request
     *
     * @return bool True if an error occurred
     */
    protected function hasRequestError(\Request $request)
    {
        if ($request->hasError()) {
            $json = json_decode($request->response);
            \System::log(
                sprintf('CleverReach error "%s: %s" occurred', $json->error->code, $json->error->mesage),
                __METHOD__,
                TL_ERROR
            );

            return true;
        }

        return false;
    }
}
