<?php

namespace Drupal\google_analytics_popularity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityChangedInterface;

/**
 * Provides an interface defining a Popularity Results entity.
 *
 * Interface defined so we can join the other interfaces it extends.
 *
 * @ingroup google_analytics_popularity
 */
interface PopularityResultsInterface extends ContentEntityInterface, EntityChangedInterface {

  /**
   * Sets the referenced node id of this popularity results.
   *
   * @param int $nid
   *   Node id.
   *
   * @return $this
   *   The called popularity results entity.
   */
  public function setNid($nid);

  /**
   * Returns the referenced node id of this popularity results.
   *
   * @return int
   *   The referenced node id of this popularity results.
   */
  public function getNid();

  /**
   * Sets the referenced node type of this popularity results.
   *
   * @param string $type
   *   Node type.
   *
   * @return $this
   *   The called popularity results entity.
   */
  public function setBundle($type);

  /**
   * Returns the referenced node type of this popularity results.
   *
   * @return string
   *   The referenced node type of this popularity results.
   */
  public function getBundle();

  /**
   * Sets the referenced node title of this popularity results.
   *
   * @param string $title
   *   Node type.
   *
   * @return $this
   *   The called popularity results entity.
   */
  public function setTitle($title);

  /**
   * Returns the referenced node title of this popularity results.
   *
   * @return string
   *   The referenced node title of this popularity results.
   */
  public function getTitle();

  /**
   * Sets the referenced node url alias path of this popularity results.
   *
   * @param string $path
   *   Node type.
   *
   * @return $this
   *   The called popularity results entity.
   */
  public function setPath($path);

  /**
   * Returns the referenced node url alias path of this popularity results.
   *
   * @return string
   *   The referenced node turl alias path of this popularity results.
   */
  public function getPath();

  /**
   * Sets the page views count of this popularity results.
   *
   * @param int $pageviews
   *   Count of page views.
   *
   * @return $this
   *   The called popularity results entity.
   */
  public function setPageviews($pageviews);

  /**
   * Returns the page views count of this popularity results.
   *
   * @return int
   *   The page views count of this popularity results.
   */
  public function getPageviews();

  /**
   * Sets the referenced node language prefix of this popularity results.
   *
   * @param string $langcode
   *   Language prefix.
   *
   * @return $this
   *   The called popularity results entity.
   */
  public function setLangcode($langcode);

  /**
   * Returns the referenced node language prefix of this popularity results.
   *
   * @return string
   *   The referenced node language prefix of this popularity results.
   */
  public function getLangcode();
}
