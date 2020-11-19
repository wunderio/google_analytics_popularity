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
        'field' => 'nid',
        'extra' => [
          [
            'field' => 'langcode',
            'left_field' => 'langcode',
          ],
        ],
      ],
    ];

    $data['popularity_results_field_data']['pageviews'] = [
      'title' => t('Pageviews'),
      'help' => t('Pageviews count per set period of time'),
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
