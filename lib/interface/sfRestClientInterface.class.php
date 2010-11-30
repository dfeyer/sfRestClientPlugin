<?php
 /**
  * (c) 2006-2010 Dominique Feyer <dominique.feyer@reelpeek.net>
  *
  * For the full copyright and license information, please view the LICENSE
  * file that was distributed with this source code.
  */

/**
 * RESTful client interface
 *
 * This interface describe the public method for the sfRestClient plugin.
 * This interface is inspired by the Jersey Client API.
 *
 * @package    sfRestClientInterface
 * @author     Dominique Feyer <dominique.feyer@reelpeek.net>
 */

interface sfRestClientInterface {
  public static function create();
  public function resource($url);
}
