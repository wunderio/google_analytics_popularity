<?php

namespace Drupal\google_analytics_popularity\Entity;

use Drupal\views\EntityViewsData;

/**
 * Provides Views data for google_analytics_popularity entities.
 */
class PopularityResultsViewsData extends EntityViewsData {

  /**
   * {@inheritdoc}
   */
  public function getViewsData() {
    $data = parent::getViewsData();

    $data['popularity_results_field_data']['table']['wizard_id'] = 'popularity_results';

    $data['popularity_results_field_data']['table']['join'] = [
      'node_field_data' => [
        'left_field' => 'nid',
        'field' => 'node_id',
        'extra' => [
          [
            'field' => 'langcode',
            'left_field' => 'langcode',
          ],
        ],
      ],
    ];

    $data['popularity_results_field_data']['pageviews'] = [
      'title' => $this->t('Pageviews'),
      'help' => $this->t('The total number of pageviews with set retrieval timespan.'),
      'field' => [
        'id' => 'standard',
      ],
      'filter' => [
        'id' => 'numeric',
      ],
      'sort' => [
        'id' => 'standard'
      ]
    ];

    $data['popularity_results_field_data']['unique_pageviews'] = [
      'title' => $this->t('Unique pageviews'),
      'help' => $this->t('The total number of unique pageviews with set retrieval timespan. Unique Pageviews is the number of sessions during which the specified page was viewed at least once.'),
      'field' => [
        'id' => 'standard',
      ],
      'filter' => [
        'id' => 'numeric',
      ],
      'sort' => [
        'id' => 'standard'
      ]
    ];

    $data['popularity_results_field_data']['sessions'] = [
      'title' => $this->t('Sessions'),
      'help' => $this->t('The total number of sessions with set retrieval timespan.'),
      'field' => [
        'id' => 'standard',
      ],
      'filter' => [
        'id' => 'numeric',
      ],
      'sort' => [
        'id' => 'standard'
      ]
    ];

    $data['popularity_results_field_data']['node_id'] = [
      'title' => $this->t('Node id'),
      'help' => $this->t('The node ID to which this popularity result belongs to.'),
      'field' => [
        'id' => 'standard',
      ],
      'filter' => [
        'id' => 'numeric',
      ],
      'sort' => [
        'id' => 'standard'
      ]
    ];

    return $data;
  }

}
