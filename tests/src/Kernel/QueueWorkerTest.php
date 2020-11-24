<?php

namespace Drupal\Tests\google_analytics_popularity\Kernel;

use Drupal\KernelTests\KernelTestBase;
use Drupal\language\Entity\ConfigurableLanguage;
use Drupal\node\Entity\NodeType;
use Drupal\pathauto\PathautoState;

/**
 * Tests processing popularity results using a queue.
 *
 * @group google_analytics_popularity
 */
class QueueWorkerTest extends KernelTestBase {

  /**
   * The modules to load to run the test.
   *
   * @var array
   */
  public static $modules = [
    'node',
    'field',
    'text',
    'path',
    'path_alias',
    'pathauto',
    'token',
    'system',
    'user',
    'language',
    'google_analytics_popularity',
  ];

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();
    $this->installEntitySchema('popularity_results');

    $this->installEntitySchema('user');
    $this->installEntitySchema('node');
    if ($this->container->get('entity_type.manager')->hasDefinition('path_alias')) {
      $this->installEntitySchema('path_alias');
    }
    $this->installConfig(['pathauto', 'system', 'node']);

    ConfigurableLanguage::createFromLangcode('fi')->save();
    ConfigurableLanguage::createFromLangcode('sv')->save();

    $type = NodeType::create(['type' => 'news']);
    $type->save();
    node_add_body_field($type);
  }

  /**
   * Test queue worker functionality.
   */
  public function testIndexing() {
    // Create test nodes.
    $tests = self::getTestNodes();

    foreach ($tests as $test) {
      $entity = \Drupal::entityTypeManager()->getStorage($test['entity'])->create($test['values']);
      $entity->save();
    }

    // Return google_analytics_popularity related queue and queue worker.
    $queue_name = 'google_analytics_popularity_queue';
    $queue = \Drupal::queue($queue_name);
    $queue_worker = \Drupal::service('plugin.manager.queue_worker')->createInstance($queue_name);

    // Add test results to queue.
    $results = self::getTestResults();
    foreach ($results as $result) {
      $queue->createItem($result);
    }

    // Check number of items in the queue.
    $this->assertEquals(count($results), $queue->numberOfItems());

    // Process the queue items and ensure that index was updated too.
    foreach ($results as $result) {
      $item = $queue->claimItem();
      $this->assertEqual($result['ga:pagePath'], $item->data['ga:pagePath'], 'Item in the queue is not same as provided popularity result');
      $queue_worker->processItem($item->data);
      $queue->deleteItem($item);
    }

    // Check number of items in the queue.
    $this->assertEquals(0, $queue->numberOfItems());

    // Check popularity results entities.
    foreach ($tests as $test) {
      /** @var \Drupal\google_analytics_popularity\PopularityResultsInterface $result */
      $results = \Drupal::entityTypeManager()->getStorage('popularity_results')->loadByProperties(['path' => $test['results_path']]);
      $result = reset($results);
      $this->assertEquals($test['values']['title'], $result->getReferencedNodeTitle());
      $this->assertEquals($test['values']['type'], $result->getReferencedNodeType());
      $this->assertEquals($test['values']['langcode'], $result->getLangcode());
    }

  }

  /**
   * Get test node definitions.
   *
   * @return array[]
   *   List of test nodes.
   */
  protected function getTestNodes() {
    $tests = [
      [
        'entity' => 'node',
        'values' => [
          'title' => 'Monday',
          'type' => 'news',
          'langcode' => 'en',
          'path' => [
            'alias' => '/news/monday',
            'pathauto' => PathautoState::SKIP,
          ],
        ],
        'results_path' => '/news/monday',
      ],
      [
        'entity' => 'node',
        'values' => [
          'title' => 'Mondag',
          'type' => 'news',
          'langcode' => 'sv',
          'path' => [
            'alias' => '/nyheter/mondag',
            'pathauto' => PathautoState::SKIP,
          ],
        ],
        'results_path' => '/sv/nyheter/mondag',
      ],
      [
        'entity' => 'node',
        'values' => [
          'title' => 'Maanantai',
          'type' => 'news',
          'langcode' => 'fi',
          'path' => [
            'alias' => '/ajankohtaista/maanantai',
            'pathauto' => PathautoState::SKIP,
          ],
        ],
        'results_path' => '/fi/ajankohtaista/maanantai',
      ],
    ];

    return $tests;
  }

  /**
   * Get test results.
   *
   * @return mixed
   *   List of test results.
   */
  protected function getTestResults() {
    $results[] = [
      'ga:pagePath' => '/news/monday',
      'pageviews' => '5586',
      'sessions' => '4207',
      'unique_pageviews' => '4613',
    ];
    $results[] = [
      'ga:pagePath' => '/sv/nyheter/mondag',
      'pageviews' => '3293',
      'sessions' => '1842',
      'unique_pageviews' => '2595',
    ];
    $results[] = [
      'ga:pagePath' => '/fi/ajankohtaista/maanantai',
      'pageviews' => '2903',
      'sessions' => '2067',
      'unique_pageviews' => '2343',
    ];

    return $results;
  }

}
