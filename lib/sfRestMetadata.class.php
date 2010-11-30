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
 * @package    sfRestMetadata
 * @author     Dominique Feyer <dominique.feyer@reelpeek.net>
 */
class sfRestMetadata extends sfRestMetadataAbstract
{
  protected $metadata = array();

  public function __construct($metadata) {
    $this->metadata = $metadata;
  }

  public function setMetaData($metadata)
  {
    $this->metadata = $metadata;
    return $this;
  }

  public function getMetaData()
  {
    return $this->metadata;
  }

  /**
   * Return HTTP status code
   *
   * @return int HTTP response code
   */
  public function getStatus()
  {
    if (isset($this->metadata['http_code']))
    {
      return (int) $this->metadata['http_code'];
    }
  }

  /**
   * Return HTTP location
   *
   * @return string Unified Resource Location
   */
  public function getLocation()
  {
    if (isset($this->metadata['url']))
    {
      return (string) $this->metadata['url'];
    }
  }

  /**
   * Return content type
   *
   * @return string Content type
   */
  public function getContentType()
  {
    if (isset($this->metadata['content_type']))
    {
      return (string) $this->metadata['content_type'];
    }
  }

  /**
   * Return header size
   *
   * @return int Header size
   */
  public function getHeaderSize()
  {
    if (isset($this->metadata['header_size']))
    {
      return (int) $this->metadata['header_size'];
    }
  }

  /**
   * Return request size
   *
   * @return int Request size
   */
  public function getRequestSize()
  {
    if (isset($this->metadata['request_size']))
    {
      return (int) $this->metadata['request_size'];
    }
  }

  /**
   * Return total time
   *
   * @return float Total time
   */
  public function getTotalTime()
  {
    if (isset($this->metadata['total_time']))
    {
      return $this->metadata['total_time'];
    }
  }

  /**
   * Return name loockup time
   *
   * @return float Total time
   */
  public function getNameLookupTime()
  {
    if (isset($this->metadata['namelookup_time']))
    {
      return $this->metadata['namelookup_time'];
    }
  }

  /**
   * Return connect time
   *
   * @return float Total time
   */
  public function getConnectTime()
  {
    if (isset($this->metadata['connect_time']))
    {
      return $this->metadata['connect_time'];
    }
  }

  /**
   * Return upload size
   *
   * @return int Upload size
   */
  public function getUploadSize()
  {
    if (isset($this->metadata['size_upload']))
    {
      return $this->metadata['size_upload'];
    }
  }

  /**
   * Return upload speed size
   *
   * @return int Upload speed
   */
  public function getUploadSpeed()
  {
    if (isset($this->metadata['speed_upload']))
    {
      return $this->metadata['speed_upload'];
    }
  }

  /**
   * Return upload content lenght
   *
   * @return int Content lenght
   */
  public function getUploadContentLength()
  {
    if (isset($this->metadata['upload_content_length']))
    {
      return $this->metadata['upload_content_length'];
    }
  }

  /**
   * Return download size
   *
   * @return int Download size
   */
  public function getDownloadSize()
  {
    if (isset($this->metadata['size_download']))
    {
      return $this->metadata['size_download'];
    }
  }

  /**
   * Return download speed size
   *
   * @return int Download speed
   */
  public function getDownloadSpeed()
  {
    if (isset($this->metadata['speed_download']))
    {
      return $this->metadata['speed_download'];
    }
  }

  /**
   * Return download content lenght
   *
   * @return int Content lenght
   */
  public function getDownloadContentLength()
  {
    if (isset($this->metadata['download_content_length']))
    {
      return $this->metadata['download_content_length'];
    }
  }
}
