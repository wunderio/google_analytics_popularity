<?php

namespace Drupal\google_analytics_popularity\Plugin\QueueWorker;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Queue\QueueWorkerBase;
use Drupal\path_alias\Entity\PathAlias;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Store results as Popularity results entity using a queue.
 *
 * @QueueWorker(
 *   id = "google_analytics_popularity_queue",
 *   title = @Translation("Store results"),
 *   cron = {"time" = 30}
 * )
 */
class ResultsQueueWorker extends QueueWorkerBase implements ContainerFactoryPluginInterface {

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  private $entityTypeManager;

  /**
   * IndexingQueueWorker constructor.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, EntityTypeManagerInterface $entity_type_manager) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);

    $this->entityTypeManager = $entity_type_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('entity_type.manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function processItem($data) {
    $entity_type = 'popularity_results';
    $url_alias = $data['ga:pagePath'];

    // Node id.
    $node_id = $this->getNodeIdByUrlAlias($url_alias);

    if ($node_id) {
      $entities = $this->entityTypeManager->getStorage($entity_type)->loadByProperties(
        [
          'nid' => $node_id,
          'langcode' => $this->getLanguageByUrlAlias($url_alias),
        ]
      );
      /** @var \Drupal\google_analytics_popularity\Entity\PopularityResults $entity */
      $entity = reset($entities);

      if ($entity) {
        // Popularity results entity exists.
        $entity->setPath($url_alias);
        $entity->setPageviews($data['pageviews']);
        $entity->save();
      }
      else {
        /** @var \Drupal\node\Entity\Node $node */
        $node = $this->entityTypeManager->getStorage('node')->load($node_id);

        // Create new entity item.
        $values = [
          'path' => $url_alias,
          'langcode' => $this->getLanguageByUrlAlias($url_alias),
          'pageviews' => $data['pageviews'],
          'nid' => $node_id,
        ];
        if ($node) {
          $values['title'] = $node->getTranslation($values['langcode'])->getTitle();
          $values['bundle'] = $node->bundle();
        }

        $entity = $this->entityTypeManager->getStorage($entity_type)->create($values);
        $entity->save();
      }
    }
  }

  /**
   * Get normalized url alias.
   *
   * @param string $url_alias
   *   Url alias.
   *
   * @return string|string[]
   *   Normalized url alias.
   */
  protected function getNormalizedUrlAlias($url_alias) {
    // Language prefixes.
    $prefixes = \Drupal::config('language.negotiation')->get('url.prefixes');

    foreach ($prefixes as $prefix) {
      $url_alias = str_replace("/{$prefix}/", '/', $url_alias);
    }

    return $url_alias;
  }

  /**
   * Get node id by url alias.
   *
   * @param string $url_alias
   *   Url alias.
   *
   * @return int|null
   *   Node id.
   */
  protected function getNodeIdByUrlAlias($url_alias) {
    // Normalize.
    $alias = $this->getNormalizedUrlAlias($url_alias);

    $id = NULL;

    try {
      $query = \Drupal::entityQuery('path_alias');
      $query->condition('alias', $alias, '=');
      $aliasIds = $query->execute();
      if ($aliasIds) {
        $aliasId = array_shift($aliasIds);
        $path = PathAlias::load($aliasId)->getPath();

        if (strpos($path, '/node/', 0) !== FALSE) {
          $id = (int) str_replace('/node/', '', $path);
        }
      }
    }
    catch (\Exception $e) {
      watchdog_exception('google_analytics_popularity', $e);
    }
    return $id;
  }

  /**
   * Get language prefix by url alias.
   *
   * @param string $url_alias
   *   Url alias.
   *
   * @return mixed|string
   *   Language prefix.
   */
  protected function getLanguageByUrlAlias($url_alias) {
    $language = \Drupal::languageManager()->getCurrentLanguage()->getId();

    // Language prefixes.
    $prefixes = \Drupal::config('language.negotiation')->get('url.prefixes');

    foreach ($prefixes as $prefix) {
      if ($prefix != '') {
        $needle = "/{$prefix}/";
        if (strpos($url_alias, $needle, 0) !== FALSE) {
          $language = $prefix;
        }
      }
    }

    return $language;
  }

}
