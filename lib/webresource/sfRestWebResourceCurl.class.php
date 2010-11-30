<?php
 /**
  * (c) 2006-2010 Dominique Feyer <dominique.feyer@reelpeek.net>
  *
  * For the full copyright and license information, please view the LICENSE
  * file that was distributed with this source code.
  */
 
/**
 * sfRestWebRessource is a RESTful client implementation for
 * Symfony Framework based on cURL
 *
 * @package    sfRestWebResource
 * @author     Dominique Feyer <dominique.feyer@reelpeek.net>
 */
class sfRestWebResourceCurl extends sfRestWebResourceAbstract
{
  protected $url;
  protected $verb;
  protected $protocol;

  protected $responseBody;
  protected $responseType;
  protected $responseInfo;

  protected $requestBody;
  protected $requestLength;

  protected $queryParams;

  protected $handler;
  protected $timeout;

  public static function getImplementation() {
    return 'curl';
  }
  
  public function __construct($url) {
    $this->url = $url;
    $this->verb = sfConfig::get('app_sfRestClient_web_resource_verb', 'GET');
    $this->protocol = sfConfig::get('app_sfRestClient_web_resource_protocol', 'HTTP');
    $this->timeout = sfConfig::get('app_sfRestClient_web_resource_timeout', 10);
  }
  
  /**
   * Client HTTP mode setter
   *
   * @param   string $verb              HTTP verb
   * @return  sfRestClientAbstract      Current intance of sfRestClientAbstract
   */
  public function setVerb($verb) {
    $this->verb = $verb;
    return $this;
  }
  
  /**
   * Client HTTP mode getter
   *
   * @return  string                    HTTP verb
   */
  public function getVerb() {
    return $this->verb;
  }

  /**
   * Accept only reponse with this type
   *
   * @param   string $accept        Response MIME type
   * @return  $this
   */
  public function accept($type) {
    $this->responseType = $type;
  }

  /**
   * Add query parameters
   *
   * @param   string $accept        Response MIME type
   * @return  $this
   */
  public function queryParams($params) {
    $this->queryParams = $params;
  }
  
  /**
   * PUT the current request
   *
   * @return  $this
   */
  public function put() {
    return $this->setVerb('PUT')->execute();
  }
  
  /**
   * GET the current request
   *
   * @return  $this
   */
  public function get() {
    return $this->setVerb('GET')->execute();
  }
  
  /**
   * POST the current request
   *
   * @return  $this
   */
  public function post() {
    return $this->setVerb('POTS')->execute();
  }
  
  /**
   * DELETE the current request
   *
   * @return  $this
   */
  public function delete() {
    return $this->setVerb('DELETE')->execute();
  }

  /**
   * Build resquest body, before calling the remote web service
   *
   * @throws InvalidArgumentException If an error occurs with argument data
   */
  public function buildRequestBody()
  {
    $data = ($this->queryParams !== null) ? $this->queryParams : $this->requestBody;

    if (!is_array($data))
    {
        throw new InvalidArgumentException('Invalid data input for postBody. Array expected');
    }

    $data = http_build_query($data, '', '&');
    $this->requestBody = $data;

    return $this;
  }

  /**
   * Build post body, before calling the remote web service
   *
   * @throws InvalidArgumentException If an error occurs with argument data
   */
  public function buildPostBody() {
    
  }

  /**
   * Execute the cURL request
   *
   * @return void
   */
  protected function doExecute()
  {
    $this->setCurlOpts($this->handler);
    $this->responseBody = curl_exec($this->handler);
    $this->responseInfo = curl_getinfo($this->handler);

    curl_close($this->handler);
  }

  /**
   * Execute the REST request and call the parser if the response is OK
   *
   * @return void
   */
  public function execute()
  {
    $this->handler = curl_init();

    $method = 'execute'.ucwords(strtolower($this->verb));

    try
    {
      if (method_exists($this, $method))
      {
        $this->$method();
      }
      else
      {
        throw new InvalidArgumentException('Current verb (' . $this->verb . ') is an invalid or unsupported REST verb.');
      }
    } catch (Exception $e)
    {
      curl_close($this->handler);
      throw $e;
    }

    if ($this->responseInfo['http_code'] == 200)
    {
      // TODO Do something with the response
    }
    else
    {
      throw new sfException(sprintf('Invalid HTTP response code: %s: %s', $this->responseInfo['http_code'], $this->url));
    }

    return $this;
  }

  /**
   * Execute GET REST Request
   *
   * @return void
   */
  protected function executeGet()
  {
    $this->doExecute();
  }

  /**
   * Execute POST REST Request
   *
   * @return void
   */
  protected function executePost()
  {
    if (!is_string($this->requestBody))
    {
        $this->buildPostBody();
    }

    curl_setopt($this->handler, CURLOPT_POSTFIELDS, $this->requestBody);
    curl_setopt($this->handler, CURLOPT_POST, 1);

    $this->doExecute();
  }

  /**
   * Execute PUT REST Request
   *
   * @return void
   */
  protected function executePut()
  {
    if (!is_string($this->requestBody) || $this->requestBody == '')
    {
        $this->buildPostBody();
    }

    $this->requestLength = strlen($this->requestBody);

    $fh = fopen('php://memory', 'rw');
    fwrite($fh, $this->requestBody);
    rewind($fh);

    curl_setopt($this->handler, CURLOPT_INFILE, $fh);
    curl_setopt($this->handler, CURLOPT_INFILESIZE, $this->requestLength);
    curl_setopt($this->handler, CURLOPT_PUT, true);

    $this->doExecute();

    fclose($fh);
  }

  /**
   * Execute DELETE REST Request
   *
   * @return void
   */
  protected function executeDelete()
  {
    curl_setopt($this->handler, CURLOPT_CUSTOMREQUEST, 'DELETE');

    $this->doExecute();
  }

  /**
   * Set default Curl configuration
   *
   * @return void
   */
  protected function setCurlOpts()
  {
    curl_setopt($this->handler, CURLOPT_TIMEOUT, $this->timeout);
    curl_setopt($this->handler, CURLOPT_URL, $this->url);
    curl_setopt($this->handler, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($this->handler, CURLOPT_HTTPHEADER, array ('Accept: ' . $this->responseType));
    if ($this->protocol === 'HTTPS') {
      curl_setopt($this->handler, CURLOPT_SSL_VERIFYPEER, 0);
    }
  }
}
