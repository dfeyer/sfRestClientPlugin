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
 * @package    sfRestClientResponse
 * @author     Dominique Feyer <dominique.feyer@reelpeek.net>
 */
class sfRestClientResponse {

  protected $response;
  protected $metadata;

  public function setResponse($response)
  {
    $this->response = $response;
    return $this;
  }

  public function getResponse()
  {
    return $this->response;
  }

  public function setMetaData($metadata)
  {
    $this->metadata = sfRestMetadata::getInstance($metadata);
    return $this;
  }

  public function getMetaData()
  {
    return $this->metadata;
  }
  
}
