<?php
 /**
  * (c) 2006-2010 Dominique Feyer <dominique.feyer@reelpeek.net>
  *
  * For the full copyright and license information, please view the LICENSE
  * file that was distributed with this source code.
  */
 
/**
 * Resume
 *
 * This class ...
 *
 * @package    sfRestWebResourceAbstract
 * @author     Dominique Feyer <dominique.feyer@reelpeek.net>
 */
abstract class sfRestWebResourceAbstract implements sfRestWebRessourceInterface
{
  protected $verb;

  /**
   * @var sfRestClientResponse $response
   */
  protected $response;
  protected $responseType;

  protected $requestBody;
  protected $requestLength;

  protected $queryParams;
  
  abstract protected function execute();

  public static function getInstance($url)
  {
    $class = get_called_class();
    return new $class($url);
  }

  /**
   * Return HTTP status code of the response
   *
   * @return  $this
   */
  abstract public function getStatus();

  /**
   * Client HTTP mode setter
   *
   * @param   string $verb              HTTP verb
   * @return  $this
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
  public function accept($type)
  {
    if (!is_array($type))
    {
      $this->responseType = $type;
    } else {
      $type = array_map('trim', $type);
      $this->responseType = implode($type, ', ');
    }
    return $this;
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
}
