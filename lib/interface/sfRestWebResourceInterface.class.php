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
 * @package    sfRestWebRessourceInterface
 * @author     Dominique Feyer <dominique.feyer@reelpeek.net>
 */
interface sfRestWebRessourceInterface {
  public static function getImplementation();
  public function accept($type);
  public function queryParams($params);
  public function get();
  public function post();
  public function put();
  public function delete();
}
