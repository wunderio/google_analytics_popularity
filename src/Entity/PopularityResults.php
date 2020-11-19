<?php

namespace Drupal\google_analytics_popularity\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Entity\EntityChangedTrait;

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
 *     "id" = "id",
 *     "uuid" = "uuid",
 *     "label" = "path",
 *     "langcode" = "langcode",
 *     "created" = "created",
 *     "changed" = "changed",
 *   },
 * )
 */
class PopularityResults extends ContentEntityBase implements ContentEntityInterface {

  use EntityChangedTrait;

  /**
   * {@inheritdoc}
   */
  public function setNid($nid) {
    $this->set('nid', $nid);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getNid() {
    return $this->get('nid')->value();
  }

  /**
   * {@inheritdoc}
   */
  public function setBundle($type) {
    $this->set('bundle', $type);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getBundle() {
    return $this->get('bundle')->value();
  }

  /**
   * {@inheritdoc}
   */
  public function setTitle($title) {
    $this->set('title', $title);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getTitle() {
    return $this->get('title')->value();
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
    return $this->get('path')->value();
  }

  /**
   * {@inheritdoc}
   */
  public function setPageviews($pageviews) {
    $this->set('pageviews', $pageviews);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getPageviews() {
    return $this->get('pageviews')->value;
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
    $fields['id'] = BaseFieldDefinition::create('integer')
      ->setLabel(t('ID'))
      ->setDescription(t('The ID of the Contact entity.'))
      ->setReadOnly(TRUE);

    // Standard field, unique outside of the scope of the current project.
    $fields['uuid'] = BaseFieldDefinition::create('uuid')
      ->setLabel(t('UUID'))
      ->setDescription(t('The UUID of the Contact entity.'))
      ->setReadOnly(TRUE);

    // Standard field, used as unique if primary index.
    $fields['nid'] = BaseFieldDefinition::create('integer')
      ->setLabel(t('NID'))
      ->setDescription(t('An associated NID for this result, if available.'))
      ->setSetting('unsigned', TRUE);

    $fields['bundle'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Node type'))
      ->setDescription(t('An associated Node type for this result, if available.'));

    $fields['title'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Title'))
      ->setDescription(t('The title for this item as provided by Google Analytics.'))
      ->setSettings([
        'max_length' => 255,
        'text_processing' => 0,
      ])
      ->setTranslatable(TRUE);

    $fields['path'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Path'))
      ->setDescription(t('The path for this item as provided by Google Analytics.'))
      ->setSettings([
        'max_length' => 255,
        'text_processing' => 0,
      ])
      ->setTranslatable(TRUE);

    $fields['pageviews'] = BaseFieldDefinition::create('integer')
      ->setLabel(t('Pageviews'))
      ->setDescription(t('The pageviews for this item as provided by Google Analytics'))
      ->setSetting('unsigned', TRUE);

    $fields['langcode'] = BaseFieldDefinition::create('language')
      ->setLabel(t('Language code'))
      ->setDescription(t('The language code of Popularity Results entity.'));

    $fields['created'] = BaseFieldDefinition::create('created')
      ->setLabel(t('Created'))
      ->setDescription(t('The time that the entity was created.'));

    $fields['changed'] = BaseFieldDefinition::create('changed')
      ->setLabel(t('Changed'))
      ->setDescription(t('The time that the entity was last edited.'));

    return $fields;
  }

}
