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
 * @package    sfRestWebResource
 * @author     Dominique Feyer <dominique.feyer@reelpeek.net>
 */
class sfRestWebResource {
  /**
   * Creates an instance of a sfRestWebRessource
   *
   * @param  string  $implementation   The web resource implementation (curl, ...)
   * @return object  a web ressource object
   * @throws sfException
   */
  public static function getInstance($url, $implementation = 'curl')
  {
    if (!isset($implementation) || $implementation == '')
    {
      throw new sfClientRestException('Check your web resource configuration, the implementation can not be empty.');
    }
    $classname = sprintf('sfRestWebResource%s', ucfirst($implementation));

    if (!class_exists($classname))
    {
      throw new sfException(sprintf('Could not find web ressource "%s"', $classname));
    }

    return new $classname($url);
  }
}
