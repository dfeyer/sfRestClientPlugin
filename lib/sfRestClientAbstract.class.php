<?php
/*
 * This file is part of the medialib package.
 * (c) 2006-2010 Dominique Feyer <dominique.feyer@reelpeek.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfRestClientAbstract is an abstract class for building RESTfull client
 *
 * This implementation is based on CURL, so you need to have the PHP curl extension loaded.
 *
 * @package    sfRestClientPlugin
 * @subpackage sfRestClientPlugin
 * @author     Dominique Feyer <dominique.feyer@reelpeek.net>
 */
abstract class sfRestClientAbstract
{

  protected $url = null;
  protected $options = array();

  protected $serializer;

  protected $data = null;
  protected $requestBody = null;
  protected $requestLenght = 0;

  protected $responseBody = null;
  protected $responseInfo = null;

  public $payload = array();

  protected $curlHandle = null;

  /**
   * Class constructor.
   *
   * @param   string $url               The URL of the remote webservice
   * @param   array  $options           An array of configuration parameters
   * @param    array  $data              An array containing URL paramter (parameter name as key name)
   *
   * @throws sfInitializationException  If CURL extension is not loaded
   * @see initialize()
   */
  public function __construct($url, $options = array(), $data = array())
  {
    if (!in_array('curl', get_loaded_extensions()))
    {
      throw new sfInitializationException('Check your PHP configuration this plugin require CURL extension');
    }
    $this->initialize($url, $options, $data);
  }

  /**
   * Initializes this medialibAutoLogin instance.
   *
   * @param   string $url               The URL of the remote webservice
   * @param   array  $options           An array of configuration parameters
   * @param   array  $data              An array containing URL paramter (parameter name as key name)
   */
  public function initialize($url, $options = array(), $data = array())
  {
    $this->setUrl($url);

    // Merge default options
    $this->setOptions($options);

    $this->data = $data;

    $this->buildRequestBody();
  }

  /**
   * Data setter
   *
   * @param mixed $data
   * @access public
   * @return void
   */
  public function setData($data)
  {
     $this->data = $data;
  }

  /**
   * Data getter
   *
   * @access public
   * @return array
   */
  public function getData()
  {
      return $this->data;
  }

  /**
   * Service URL setter
   *
   * @param   string $url               The URL of the remote webservice
   * @return  sfRestClientAbstract      Current intance of sfRestClientAbstract
   */
  public function setUrl($url) {
    $this->url = $url;
    return $this;
  }

  /**
   * Service URL getter
   *
   * @return  string                    Current URL if setted or null
   */
  public function getUrl() {
    return $this->url;
  }

  /**
   * Client HTTP mode setter
   *
   * @param   string $verb              HTTP verb
   * @return  sfRestClientAbstract      Current intance of sfRestClientAbstract
   */
  public function setVerb($verb) {
    $this->options['verb'] = $verb;
    return $this;
  }

  /**
   * Client HTTP mode getter
   *
   * @return  string                    HTTP verb
   */
  public function getVerb() {
    return $this->options['verb'];
  }

  /**
   * PUT the current request
   *
   * @return  sfRestClientAbstract      Current intance of sfRestClientAbstract
   */
  public function put() {
    return $this->setVerb('PUT')->execute();
  }

  /**
   * GET the current request
   *
   * @return  sfRestClientAbstract      Current intance of sfRestClientAbstract
   */
  public function get() {
    return $this->setVerb('GET')->execute();
  }

  /**
   * POST the current request
   *
   * @return  sfRestClientAbstract      Current intance of sfRestClientAbstract
   */
  public function post() {
    return $this->setVerb('POST')->execute();
  }

  /**
   * DELETE the current request
   *
   * @return  sfRestClientAbstract      Current intance of sfRestClientAbstract
   */
  public function delete() {
    return $this->setVerb('DELETE')->execute();
  }

  /**
   * Client options setter
   *
   * The setter merge the provided array with the default value
   *
   * @param   array $options            The configuration paramter
   * @return  sfRestClientAbstract      Current intance of sfRestClientAbstract
   */
  public function setOptions($options) {
    $this->options = array_merge(array(
      'verb' => 'GET',
      'acceptType' => 'application/xml',
      'serializer' => 'xml',
      'username' => null,
      'password' => null,
      'timeout' => 10
    ), $options);

    return $this;
  }

  /**
   * Client options getter
   *
   * @return  array                     The current configuration paramter
   */
  public function getOptions() {
    return $this->options;
  }

  /**
   * Get a serailizer instance from the extension sfDoctrineRestGeneratorPlugin
   *
   * @return   sfRessourceSerializer  An instance of the requested serializer
   */
  protected function getSerializer()
  {
    if (!isset($this->serializer))
    {
      try
      {
        $this->serializer = sfResourceSerializer::getInstance($this->options['serializer']);
      }
      catch (sfException $e)
      {
        throw new sfException($e->getMessage());
      }
    }

    return $this->serializer;
  }

  /**
   * Build resquest body, before calling the remote web service
   *
   * @throws InvalidArgumentException If an error occurs with argument data
   */
  public function buildRequestBody()
  {
    $data = ($this->data !== null) ? $this->data : $this->requestBody;

    if (!is_array($data))
    {
        throw new InvalidArgumentException('Invalid data input for postBody. Array expected');
    }    
    $data = http_build_query($data, '', '&');    
    $this->requestBody = $data;

    return $this;
  }

  public function buildPostBody() {
    $this->requestBody = $this->getSerializer()->serialize($this->payload);
  }

  /**
   * Execute the cURL request
   *
   * @return void
   */
  protected function doExecute()
  {
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
  public function execute()
  {
    $this->curlHandle = curl_init();

    $this->setAuth();

    $method = 'execute'.ucwords(strtolower($this->options['verb']));

    try
    {
      if (method_exists($this, $method))
      {
        $this->$method();
      }
      else
      {
        throw new InvalidArgumentException('Current verb (' . $this->options['verb'] . ') is an invalid or unsupported REST verb.');
      }
    } catch (Exception $e)
    {
      curl_close($this->curlHandle);
      throw $e;
    }

    if ($this->responseInfo['http_code'] >= 200 && $this->responseInfo['http_code'] < 300)
    {
      $this->unserialize();
    }
    else
    {
        $this->unserialize();
	$error = "Unknow error";
	if (isset($this->payload['error']))
	{
	   $error = join("\n", (array)$this->payload['error']);
	}
        throw new sfException(sprintf("Invalid HTTP response code: %s: %s\nTrace : %s\n",
            $this->responseInfo['http_code'], $this->url, $error));
    }

    return $this;
  }

  /**
   * Unserialize the response to prepare the array $this->response with the proper value
   *
   * @return void
   */
  abstract protected function unserialize();

  /**
   * Execute GET REST Request
   *
   * @return void
   */
  protected function executeGet()
  {
     if ($this->requestBody != null)
	$this->url .= '?'.$this->requestBody;
     $this->doExecute();
  }

  /**
   * Execute POST REST Request
   *
   * @return void
   */
  protected function executePost()
  {
    if (!$this->requestBody)
    {
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
  protected function executeDelete ()
  {
    curl_setopt($this->curlHandle, CURLOPT_CUSTOMREQUEST, 'DELETE');

    $this->doExecute();
  }

  /**
   * Flush the current class variable
   *
   * @return $this
   */
  public function flush()
  {
    $this->data = null;

    $this->requestBody = null;
    $this->requestLenght = 0;
    $this->payload = array();

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
  protected function setCurlOpts()
  {
    curl_setopt($this->curlHandle, CURLOPT_TIMEOUT, $this->options['timeout']);
    curl_setopt($this->curlHandle, CURLOPT_URL, $this->url);
    curl_setopt($this->curlHandle, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($this->curlHandle, CURLOPT_HTTPHEADER, array ('Accept: ' . $this->options['acceptType']));
    curl_setopt($this->curlHandle, CURLOPT_SSL_VERIFYPEER, 0);
  }

  /**
   * Set curl authentification (support only HTTP DIGEST)
   *
   * @return void
   */
  protected function setAuth()
  {
    if ($this->options['username'] !== null && $this->options['password'] !== null) {
      curl_setopt($this->curlHandle, CURLOPT_HTTPAUTH, CURLAUTH_DIGEST);
      curl_setopt($this->curlHandle, CURLOPT_USERPWD, $this->options['username'] . ':' . $this->options['password']);
    }
  }
}
