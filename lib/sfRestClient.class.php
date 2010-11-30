<?php
/*
 * This file is part of the medialib package.
 * (c) 2006-2010 Dominique Feyer <dominique.feyer@reelpeek.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfRestClientAbstract is a generic RESTful client implementation for
 * Symfony Framework.
 *
 * @package    sfRestClientPlugin
 * @author     Dominique Feyer <dominique.feyer@reelpeek.net>
 */
class sfRestClient implements sfRestClientInterface
{
  protected $options;

  /**
   * Class constructor.
   *
   * @param   array  $options           An array of configuration parameters
   *
   * @throws sfInitializationException  If CURL extension is not loaded
   * @see initialize()
   */
  public function __construct($options = array())
  {
    if (!in_array('curl', get_loaded_extensions()))
    {
      throw new sfClientRestException('Check your PHP configuration this plugin require CURL extension');
    }
    $this->initialize($options);
  }

  /**
   * Initializes this medialibAutoLogin instance.
   *
   * @param   string $url               The URL of the remote webservice
   * @param   array  $options           An array of configuration parameters
   * @param   array  $data              An array containing URL paramter (parameter name as key name)
   */
  protected function initialize($options)
  {
    $this->setOptions($options);
  }

  public static function create($options = array()) {
    $class = __CLASS__;
    return new $class($options);
  }

  /**
   * Client options setter
   *
   * The setter merge the provided array with the default value
   *
   * @param   array $options            The configuration paramter
   * @return  sfRestClientAbstract      Current intance of sfRestClientAbstract
   */
  protected function setOptions($options)
  {
    $this->options = array_merge(array(
      'implementation' => sfConfig::get('app_sfRestClient_client_ressource', 'curl')
    ), $options);

    return $this;
  }

  /**
   * Return a web ressource based on the current implementation
   *
   * The setter merge the provided array with the default value
   *
   * @param   array $options            The configuration paramter
   * @return  sfRestClientAbstract      Current intance of sfRestClientAbstract
   */
  public function resource($url)
  {
    return sfRestWebResource::getInstance($url, $this->options['implementation']);
  }
}
