<?php
/*
 * This file is part of the medialib package.
 * (c) 2006-2010 Dominique Feyer <dominique.feyer@reelpeek.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
 
/**
 * sfRestClient is a generic RESTful client
 *
 * @package    sfRestClientPlugin
 * @subpackage sfRestClientPlugin
 * @author     Dominique Feyer <dominique.feyer@reelpeek.net>
 */
class sfRestClient extends sfRestClientAbstract
{
  protected function unserialize() {
    $this->payload = $this->getSerializer()->unserialize($this->responseBody);
    
    return $this;
  }
}

?>
