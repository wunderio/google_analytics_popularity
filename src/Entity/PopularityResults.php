<?php

namespace Drupal\google_analytics_popularity\Entity;

use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\google_analytics_popularity\PopularityResultsInterface;

/**
 * Defines the Google Analytics Popularity Results entity.
 *
 * @ingroup google_analytics_popularity
 *
 * @ContentEntityType(
 *   id = "popularity_results",
 *   label = @Translation("Popularity results"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "views_data" = "Drupal\google_analytics_popularity\Entity\PopularityResultsViewsData",
 *   },
 *   base_table = "popularity_results",
 *   data_table = "popularity_results_field_data",
 *   translatable = TRUE,
 *   entity_keys = {
 *     "id" = "prid",
 *     "uuid" = "uuid",
 *     "label" = "path",
 *     "langcode" = "langcode",
 *     "created" = "created",
 *     "changed" = "changed",
 *   },
 * )
 */
class PopularityResults extends ContentEntityBase implements PopularityResultsInterface {

  use EntityChangedTrait;

  /**
   * {@inheritdoc}
   */
  public function setReferencedNodeId($nid) {
    $this->set('node_id', $nid);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getReferencedNodeId() {
    return $this->get('node_id')->target_id;
  }

  /**
   * {@inheritdoc}
   */
  public function setReferencedNodeType($type) {
    $this->set('node_type', $type);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getReferencedNodeType() {
    return $this->get('node_type')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setReferencedNodeTitle($title) {
    $this->set('node_title', $title);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getReferencedNodeTitle() {
    return $this->get('node_title')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setPath($path) {
    $this->set('path', $path);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getPath() {
    return $this->get('path')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setSessionsCount($sessions) {
    $this->set('sessions', $sessions);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getSessionsCount() {
    return $this->get('sessions')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setPageviewsCount($pageviews) {
    $this->set('pageviews', $pageviews);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getPageviewsCount() {
    return $this->get('pageviews')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setUniquePageviewsCount($unique_pageviews) {
    $this->set('unique_pageviews', $unique_pageviews);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getUniquePageviewsCount() {
    return $this->get('unique_pageviews')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setLangcode($langcode) {
    $this->set('langcode', $langcode);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getLangcode() {
    return $this->get('langcode')->value;
  }

  /**
   * {@inheritdoc}
   *
   * Define the field properties here.
   *
   * Field name, type and size determine the table structure.
   *
   * In addition, we can define how the field and its content can be manipulated
   * in the GUI. The behaviour of the widgets used can be determined here.
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {

    // Standard field, used as unique if primary index.
    $fields['prid'] = BaseFieldDefinition::create('integer')
      ->setLabel(t('Popularity Results ID'))
      ->setDescription(t('The ID of the Popularity Results entity.'))
      ->setReadOnly(TRUE);

    // Standard field, unique outside of the scope of the current project.
    $fields['uuid'] = BaseFieldDefinition::create('uuid')
      ->setLabel(t('UUID'))
      ->setDescription(t('The UUID of the Popularity Results entity.'))
      ->setReadOnly(TRUE);

    $fields['node_id'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Node ID'))
      ->setDescription(t('The ID of the node of which this popularity result belongs to.'))
      ->setSetting('target_type', 'node');

    $fields['node_type'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Node type'))
      ->setDescription(t('The node type to which this popularity result is attached.'));

    $fields['node_title'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Title'))
      ->setDescription(t('The node title to which this popularity result is attached.'))
      ->setSettings([
        'max_length' => 255,
        'text_processing' => 0,
      ])
      ->setTranslatable(TRUE);

    $fields['path'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Path'))
      ->setDescription(t('The path of a page on the website for this popularity result as provided by Google Analytics.'))
      ->setSettings([
        'max_length' => 255,
        'text_processing' => 0,
      ])
      ->setTranslatable(TRUE)
      ->setRequired(TRUE);

    $fields['sessions'] = BaseFieldDefinition::create('integer')
      ->setLabel(t('Sessions'))
      ->setDescription(t('The total number of sessions for this popularity result as provided by Google Analytics'))
      ->setSetting('unsigned', TRUE);

    $fields['pageviews'] = BaseFieldDefinition::create('integer')
      ->setLabel(t('Pageviews'))
      ->setDescription(t('The total number of pageviews for this popularity result as provided by Google Analytics'))
      ->setSetting('unsigned', TRUE);

    $fields['unique_pageviews'] = BaseFieldDefinition::create('integer')
      ->setLabel(t('Unique pageviews'))
      ->setDescription(t('The total number of unique pageviews for this popularity result as provided by Google Analytics'))
      ->setSetting('unsigned', TRUE);

    $fields['langcode'] = BaseFieldDefinition::create('language')
      ->setLabel(t('Language code'))
      ->setDescription(t('The popularity results entity language code.'));

    $fields['created'] = BaseFieldDefinition::create('created')
      ->setLabel(t('Created'))
      ->setDescription(t('The time that the entity was created.'));

    $fields['changed'] = BaseFieldDefinition::create('changed')
      ->setLabel(t('Changed'))
      ->setDescription(t('The time that the entity was last edited.'));

    return $fields;
  }

}
