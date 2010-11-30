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
 * @package    sfRestMetadataAbstract
 * @author     Dominique Feyer <dominique.feyer@reelpeek.net>
 */
abstract class sfRestMetadataAbstract implements sfRestMetadataInterface
{
  public static function getInstance($metadata) {
    $class = get_called_class();
    return new $class($metadata);
  }
}
