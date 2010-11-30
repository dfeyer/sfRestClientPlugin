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
  abstract public static function getImplementation();

  public static function getInstance($url)
  {
    $class = __CLASS__;
    return new $class($url);
  }
}
