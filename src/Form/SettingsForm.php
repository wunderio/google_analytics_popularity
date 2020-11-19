<?php

namespace Drupal\google_analytics_popularity\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\file\Entity\File;

/**
 * Provide settings for Stage File Proxy.
 */
class SettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'google_analytics_popularity_settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'google_analytics_popularity.settings',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $field_type = NULL) {
    $config = $this->config('google_analytics_popularity.settings');

    $form['#prefix'] = $this->t('<p>This module uses the Google API PHP Client library to pull recent top content from Google Analytics.</p>
      <p>The Google Analytics API query is updated on cron runs and top viewed pages content is stored to a dedicated entity and is accessible via views.</p>'
    );

    // Credentials fieldset.
    $form['credentials'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Google Analytics Development Credentials'),
    ];

    // Google Analytics project keyfile.
    $form['credentials']['keyfile_upload'] = [
      '#type' => 'managed_file',
      '#name' => 'keyfile_upload',
      '#title' => $this->t('Json keyfile'),
      '#size' => 40,
      '#upload_location' => \Drupal::hasService('stream_wrapper.private') ? 'private://' : 'public://',
      '#default_value' => $config->get('keyfile_upload_fid') ?? NULL,
      '#upload_validators' => [
        'file_validate_extensions' => ['json'],
        // Pass the maximum file size in bytes (10 kb).
        'file_validate_size' => [10 * 1024],
      ],
      '#description' => $this->t('Upload a Google API service account .json keyfile.'),
      '#required' => TRUE,
    ];

    if (\Drupal::hasService('stream_wrapper.private') === FALSE) {
      $form['credentials']['keyfile_upload']['#description'] .= "\r\n" . $this->t('<strong>Please enable Drupal private file system for enhanced security.</strong>');
    }

    // Credentials fieldset.
    $form['analytics'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Google Analytics Settings'),
    ];

    // Google Analytics View ID - positive integer.
    $form['analytics']['view_id'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Google Analytics View ID'),
      '#default_value' => $config->get('view_id'),
      '#description' => $this->t('The Google Analytics view ID to use. This comes from <strong>GA->Account->Property->View->View settings</strong>, and is an integer without dashes.'),
      '#required' => TRUE,
    ];

    // Timespan for search.
    $form['analytics']['timespan'] = [
      '#type' => 'select',
      '#title' => $this->t('Data retrieval timespan'),
      '#default_value' => $config->get('timespan'),
      '#options' => [
        '-1 day' => t('past 24 hours'),
        '-2 day' => t('past 2 days'),
        '-3 day' => t('past 3 days'),
        '-4 day' => t('past 4 days'),
        '-5 day' => t('past 5 days'),
        '-1 week' => t('past week'),
        '-2 week' => t('past 2 weeks'),
        '-1 month' => t('past month'),
        '-1 year' => t('past year'),
      ],
      '#description' => $this->t('The timespan for which data should be retrieved, relative to the current day.'),
    ];

    // Max items to request.
    $form['analytics']['max_items'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Number of top pages to request from Google Analytics'),
      '#default_value' => $config->get('max_items'),
      '#description' => $this->t('The number of items the Google Analytics API query should return. Higher settings will require more resources. Hard limit for this setting: 100 000 items.'),
    ];

    // Options fieldset.
    $form['options'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Cron Options'),
    ];

    $form['options']['cron_interval'] = [
      '#type' => 'select',
      '#title' => $this->t('Cron interval'),
      '#options' => $this->getCronIntervalOptions(),
      '#default_value' => $config->get('cron_interval'),
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    // @todo To be added.
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $config = $this->config('google_analytics_popularity.settings');

    $keys = [
      'keyfile_upload_fid',
      'view_id',
      'timespan',
      'max_items',
      'cron_interval',
    ];
    foreach ($keys as $key) {
      $value = $form_state->getValue($key);
      if ($key === 'keyfile_upload_fid') {
        $fid = array_shift($form_state->getValue('keyfile_upload'));
        if (!empty($fid)) {
          $file = File::load($fid);
          /** @var \Drupal\file\FileInterface $file */
          if ($file->isTemporary()) {
            $file->setPermanent();
            $file->save();
          }
        }
        $value = $fid ? [$fid] : [];
      }
      $config->set($key, $value);
    }
    $config->save();
    $this->messenger()->addMessage($this->t('Your settings have been saved.'));
  }

  /**
   * Returns cron interval options.
   *
   * @return array
   *   Cron interval options.
   */
  protected function getCronIntervalOptions() {
    $cronIntervals = [1, 3, 6, 12, 24, 48, 72, 96, 120, 168];

    /** @var \Drupal\Core\Datetime\DateFormatter $formatter */
    $formatter = \Drupal::service('date.formatter');
    $intervals = array_flip($cronIntervals);
    foreach ($intervals as $interval => &$label) {
      $label = $formatter->formatInterval($interval * 60 * 60);
    }

    return [0 => $this->t('On every cron run')] + $intervals;
  }

}
