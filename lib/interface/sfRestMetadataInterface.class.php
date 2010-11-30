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
 * @package    sfRestClientResponseMetadataInterface
 * @author     Dominique Feyer <dominique.feyer@reelpeek.net>
 */
interface sfRestMetadataInterface {
  public function setMetaData($metadata);
  public function getMetaData();
  public function getStatus();
  public function getLocation();
  public function getContentType();
  public function getHeaderSize();
  public function getRequestSize();
  public function getTotalTime();
  public function getNameLookupTime();
  public function getConnectTime();
  public function getUploadSize();
  public function getUploadSpeed();
  public function getUploadContentLength();
  public function getDownloadSize();
  public function getDownloadSpeed();
  public function getDownloadContentLength();
}
