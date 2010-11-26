<?php
/*
 * This file is part of the medialib package.
 * (c) 2006-2010 Dominique Feyer <dominique.feyer@reelpeek.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
 
/**
 * medialibAutoLogin is an abstract class for all auto login classes in medialib.
 *
 * @package    medialib
 * @subpackage sfMedialibTools
 * @author     Dominique Feyer <dominique.feyer@reelpeek.net>
 */
abstract class sfRestClientAbstract {
    
    protected $url = null;
    protected $options = array();
    
    protected $data = null;
    protected $requestBody = null;
    protected $requestLenght = 0;
    
    protected $responseBody = null;
    protected $responseInfo = null;
    protected $response = array();
    
    protected $curlHandle = null;
    
    /**
     * Class constructor.
     *
     * @see initialize()
     */
    public function __construct($url, $options = array(), $data = array()) {
        $this->initialize($url, $options, $data);
    }
    
    /**
     * Initializes this medialibAutoLogin instance.
     *
     * @param array $options An array of options.
     *
     * @throws sfInitializationException If an error occurs while initializing this medialibAutoLogin instance.
     */
    public function initialize($url, $options = array(), $data = array()) {
        
        $this->url = $url;
        
        // Merge default options
        $this->options = array_merge(array(
            'verb' => 'GET',
            'acceptType' => 'application/xml',
            'username' => null,
            'password' => null,
            'timeout' => 10
        ), $this->options);
        
        $this->data = $data;
        
        $this->buildRequestBody();
        
    }
    
    /**
     * Build resquest body, before calling the remote web service
     *
     * @throws InvalidArgumentException If an error occurs with argument data
     */
    public function buildRequestBody() {
        
        $data = ($this->data !== null) ? $this->data : $this->requestBody;
        
        if (!is_array($data)) {  
            throw new InvalidArgumentException('Invalid data input for postBody. Array expected');
        }  
      
        $data = http_build_query($data, '', '&');  
        $this->requestBody = $data;
        
        return $this;
    }
    
    /**
     * Execute the cURL request
     * 
     * @return void
     */
    protected function doExecute () {
        $this->setCurlOpts($this->curlHandle);
        $this->responseBody = curl_exec($this->curlHandle);
        $this->responseInfo  = curl_getinfo($this->curlHandle);
      
        curl_close($this->curlHandle);
    }  
    
    /**
     * Execute the REST request and call the parser if the response is OK
     * 
     * @return void
     */
    public function execute() {
        
        $this->curlHandle = curl_init();
        
        $this->setAuth($ch);
        
        $method = 'execute'.ucwords($this->options['verb']);
        
        try {
            if (method_exists($this, $method)) {
                $this->$method($ch);
            } else {
                throw new InvalidArgumentException('Current verb (' . $this->options['verb'] . ') is an invalid or unsupported REST verb.');
            }
        } catch (Exception $e)  {
            curl_close($ch);
            throw $e;
        }
        
        if ($this->responseInfo['http_code'] == 200) {
            $this->parseResponse();
        } else {
            throw new sfException('Invalid HTTP response code');
        }
    }
    
    /**
     * Parse the response to prepare the array $this->response with the proper value
     *
     * $this->response must contain the following key:
     * - first_name (only for account creation)
     * - last_name (only for account creation)
     * - email_address (only for account creation)
     * - username
     * - password (only for account creation)
     * 
     * @return void
     */
    abstract protected function parseResponse();
    
    /**
     * Execute GET REST Request
     *  
     * @return void
     */
    protected function executeGet() {
        $this->doExecute();
    }
    
    /**
     * Execute POST REST Request
     *  
     * @return void
     */
    protected function executePost() {  
        if (!is_string($this->requestBody)) {  
            $this->buildPostBody();
        }  
      
        curl_setopt($this->curlHandle, CURLOPT_POSTFIELDS, $this->requestBody);  
        curl_setopt($this->curlHandle, CURLOPT_POST, 1);  
      
        $this->doExecute();
    }
    
    /**
     * Execute PUT REST Request
     *  
     * @return void
     */
    protected function executePut() {
        if (!is_string($this->requestBody)) {  
            $this->buildPostBody();  
        }  
      
        $this->requestLength = strlen($this->requestBody);  
      
        $fh = fopen('php://memory', 'rw');  
        fwrite($fh, $this->requestBody);  
        rewind($fh);  
      
        curl_setopt($this->curlHandle, CURLOPT_INFILE, $fh);  
        curl_setopt($this->curlHandle, CURLOPT_INFILESIZE, $this->requestLength);  
        curl_setopt($this->curlHandle, CURLOPT_PUT, true);  
      
        $this->doExecute();  
      
        fclose($fh);  
    }
    
    /**
     * Execute DELETE REST Request
     *  
     * @return void
     */
    protected function executeDelete () {  
        curl_setopt($this->curlHandle, CURLOPT_CUSTOMREQUEST, 'DELETE');  
      
        $this->doExecute();  
    }  
    
    /**
     * Flush the current class variable
     *  
     * @return $this
     */
    public function flush() {
        
        $this->data = null;
        
        $this->requestBody = null;
        $this->requestLenght = 0;
        $this->response = array();
        
        $this->responseBody = null;
        $this->responseInfo = null;
        
        curl_close($this->curlHandle);
        
        $this->curlHandle = null;
        
        $this->sfGuardUser = false;
        
        return $this;
    }
    
    /**
     * Set default Curl configuration
     *  
     * @return void
     */
    protected function setCurlOpts() {
        
        curl_setopt($this->curlHandle, CURLOPT_TIMEOUT, $this->options['timeout']);
        curl_setopt($this->curlHandle, CURLOPT_URL, $this->url);
        curl_setopt($this->curlHandle, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->curlHandle, CURLOPT_HTTPHEADER, array ('Accept: ' . $this->options['acceptType']));
    
    }
    
    /**
     * Set curl authentification (support only HTTP DIGEST)
     *  
     * @return void
     */
    protected function setAuth() {
        
        if ($this->options['username'] !== null && $this->options['password'] !== null) {
            curl_setopt($this->curlHandle, CURLOPT_HTTPAUTH, CURLAUTH_DIGEST);
            curl_setopt($this->curlHandle, CURLOPT_USERPWD, $this->options['username'] . ':' . $this->options['password']);
        }
        
    }
    
    
    
}

?>
