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

  protected $body;
  protected $info;

  public function setBody($body)
  {
    $this->body = $body;
    return $this;
  }

  public function getBody()
  {
    return $this->body;
  }

  public function setInfo($info)
  {
    $this->info = $info;
    return $this;
  }

  public function getInfo()
  {
    return $this->info;
  }

  /**
   * Return HTTP status code of the response
   *
   * @return  $this
   */
  public function getStatus()
  {
    if (isset($this->info['http_code']))
    {
      return $this->info['http_code'];
    }
  }
}
