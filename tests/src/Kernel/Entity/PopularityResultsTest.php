<?php

namespace Drupal\Tests\google_analytics_popularity\Kernel\Entity;

use Drupal\google_analytics_popularity\Entity\PopularityResults;
use Drupal\KernelTests\Core\Entity\EntityKernelTestBase;

/**
 * Tests the Popularity Results entity.
 *
 * @coversDefaultClass \Drupal\google_analytics_popularity\Entity\PopularityResults
 *
 * @group google_analytics_popularity
 */
class PopularityResultsTest extends EntityKernelTestBase {

  /**
   * The modules to load to run the test.
   *
   * @var array
   */
  public static $modules = [
    'node',
    'google_analytics_popularity',
  ];

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();

    $this->installEntitySchema('popularity_results');
  }

  /**
   * @covers ::getReferencedNodeId
   * @covers ::setReferencedNodeId
   * @covers ::getReferencedNodeType
   * @covers ::setReferencedNodeType
   * @covers ::getReferencedNodeTitle
   * @covers ::setReferencedNodeTitle
   * @covers ::getPath
   * @covers ::setPath
   * @covers ::getSessionsCount
   * @covers ::setSessionsCount
   * @covers ::getPageviewsCount
   * @covers ::setPageviewsCount
   * @covers ::setUniquePageviewsCount
   * @covers ::getUniquePageviewsCount
   * @covers ::getLangcode
   * @covers ::setLangcode
   */
  public function testPopularityResults() {
    /** @var \Drupal\google_analytics_popularity\PopularityResultsInterface $result */
    $result = PopularityResults::create([
      'path' => '/news/at-home',
    ]);
    $result->save();

    $this->assertEquals(NULL, $result->getReferencedNodeId());
    $result->setReferencedNodeId(21);
    $this->assertEquals(21, $result->getReferencedNodeId());

    $this->assertEquals(NULL, $result->getReferencedNodeType());
    $result->setReferencedNodeType('news');
    $this->assertEquals('news', $result->getReferencedNodeType());

    $this->assertEquals(NULL, $result->getReferencedNodeTitle());
    $result->setReferencedNodeTitle('At home');
    $this->assertEquals('At home', $result->getReferencedNodeTitle());

    $this->assertEquals('/news/at-home', $result->getPath());
    $result->setPath('/news/at-home-last');
    $this->assertEquals('/news/at-home-last', $result->getPath());

    $this->assertEquals(NULL, $result->getSessionsCount());
    $result->setSessionsCount(101);
    $this->assertEquals(101, $result->getSessionsCount());

    $this->assertEquals(NULL, $result->getPageviewsCount());
    $result->setPageviewsCount(102);
    $this->assertEquals(102, $result->getPageviewsCount());

    $this->assertEquals(NULL, $result->getUniquePageviewsCount());
    $result->setUniquePageviewsCount(103);
    $this->assertEquals(103, $result->getUniquePageviewsCount());

    $result->setLangcode('fi');
    $this->assertEquals('fi', $result->getLangcode());
  }

}
