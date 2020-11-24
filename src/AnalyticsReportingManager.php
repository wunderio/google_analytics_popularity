<?php

namespace Drupal\google_analytics_popularity;

use Drupal\Core\Config\ConfigException;
use Drupal\Core\Config\ConfigFactory;
use Google\Client as Google_Client;

/**
 * Class AnalyticsReportingManager.
 *
 * @package Drupal\google_analytics_popularity
 */
class AnalyticsReportingManager {

  /**
   * The config.factory service.
   *
   * @var \Drupal\Core\Config\Config
   *   The config settings.
   */
  protected $config;

  /**
   * AnalyticsReportingManager constructor.
   *
   * @param \Drupal\Core\Config\ConfigFactory $config_factory
   *   The config factory.
   */
  public function __construct(ConfigFactory $config_factory) {
    $this->config = $config_factory->get('google_analytics_popularity.settings');
  }

  /**
   * Initialize Analytics.
   *
   * @return \Google_Service_AnalyticsReporting
   *   Analytic Reporting client.
   *
   * @throws \Google\Exception
   */
  public function initializeAnalytics() {

    // Create and configure a new client object.
    $client = new Google_Client();
    $client->setApplicationName("Analytics Reporting");
    $client->setAuthConfig($this->getKeyFileLocation());
    $client->setScopes(['https://www.googleapis.com/auth/analytics.readonly']);
    $analytics = new \Google_Service_AnalyticsReporting($client);

    return $analytics;
  }

  /**
   * Get report.
   *
   * @param \Google_Service_AnalyticsReporting $analytics
   *   Analytic Reporting client.
   *
   * @return mixed
   *   Reports.
   */
  public function getReport(\Google_Service_AnalyticsReporting $analytics) {

    // Create the DateRange object.
    $start_date = date("Y-m-d", strtotime($this->config->get('timespan')));
    $dateRange = new \Google_Service_AnalyticsReporting_DateRange();
    $dateRange->setStartDate($start_date);
    $dateRange->setEndDate("today");

    // Create the Metrics object.
    $sessions = new \Google_Service_AnalyticsReporting_Metric();
    $sessions->setExpression("ga:sessions");
    $sessions->setAlias("sessions");

    $pageviews = new \Google_Service_AnalyticsReporting_Metric();
    $pageviews->setExpression("ga:pageviews");
    $pageviews->setAlias("pageviews");

    $unique_pageviews = new \Google_Service_AnalyticsReporting_Metric();
    $unique_pageviews->setExpression("ga:uniquePageviews");
    $unique_pageviews->setAlias("unique_pageviews");

    // Create the Dimensions object.
    $path = new \Google_Service_AnalyticsReporting_Dimension();
    $path->setName("ga:pagePath");

    $title = new \Google_Service_AnalyticsReporting_Dimension();
    $title->setName("ga:pageTitle");

    // Create the Ordering.
    $ordering = new \Google_Service_AnalyticsReporting_OrderBy();
    $ordering->setOrderType("VALUE");
    $ordering->setFieldName("ga:pageviews");
    $ordering->setSortOrder("DESCENDING");

    // Create the ReportRequest object.
    $request = new \Google_Service_AnalyticsReporting_ReportRequest();
    $request->setViewId($this->getViewId());
    $request->setDateRanges($dateRange);
    $request->setMetrics([$pageviews, $sessions, $unique_pageviews]);
    $request->setDimensions([$path]);
    $request->setOrderBys([$ordering]);
    $request->setPageSize($this->config->get('max_items'));

    $body = new \Google_Service_AnalyticsReporting_GetReportsRequest();
    $body->setReportRequests([$request]);
    return $analytics->reports->batchGet($body);
  }

  /**
   * Helper function to print results to command line.
   *
   * @param object|array $reports
   *   Reports.
   */
  public function printResults($reports) {
    for ($reportIndex = 0; $reportIndex < count($reports); $reportIndex++) {
      $report = $reports[$reportIndex];
      $header = $report->getColumnHeader();
      $dimensionHeaders = $header->getDimensions();
      $metricHeaders = $header->getMetricHeader()->getMetricHeaderEntries();
      $rows = $report->getData()->getRows();

      for ($rowIndex = 0; $rowIndex < count($rows); $rowIndex++) {
        $row = $rows[$rowIndex];

        $dimensions = $row->getDimensions();
        $metrics = $row->getMetrics();

        for ($i = 0; $i < count($dimensionHeaders) && $i < count($dimensions); $i++) {
          print($dimensionHeaders[$i] . ": " . $dimensions[$i] . "\n");
        }

        for ($j = 0; $j < count($metrics); $j++) {
          $values = $metrics[$j]->getValues();
          for ($k = 0; $k < count($values); $k++) {
            $entry = $metricHeaders[$k];
            print($entry->getName() . ": " . $values[$k] . "\n");
          }
        }
      }
    }
  }

  /**
   * Get results array.
   *
   * @param object|array $reports
   *   Reports.
   *
   * @return array
   *   Results.
   */
  public function getResults($reports) {
    $results = [];

    foreach ($reports as $report) {
      $header = $report->getColumnHeader();
      $dimensionHeaders = $header->getDimensions();
      $metricHeaders = $header->getMetricHeader()->getMetricHeaderEntries();
      $rows = $report->getData()->getRows();

      foreach ($rows as $row) {
        $row_results = [];
        $dimensions = $row->getDimensions();
        $metrics = $row->getMetrics();

        foreach ($dimensions as $index => $dimension) {
          $row_results[$dimensionHeaders[$index]] = $dimension;
        }

        foreach ($metrics as $metric) {
          $values = $metric->getValues();
          foreach ($values as $key => $value) {
            $entry = $metricHeaders[$key];
            $row_results[$entry->getName()] = $value;
          }
        }

        $results[] = $row_results;
      }
    }

    return $results;
  }

  /**
   * Get key file location.
   *
   * @return string
   *   Key file location.
   */
  protected function getKeyFileLocation() {
    // Get managed file from the settings.
    $fid = $this->config->get('keyfile_uri');

    if (empty($fid)) {
      throw new ConfigException('No configuration set for Json keyfile');
    }

    return $fid;
  }

  /**
   * Get Analytics Reporting view id.
   *
   * @return array|mixed
   *   Analytics Reporting view id.
   */
  protected function getViewId() {
    $view_id = $this->config->get('view_id');

    if (empty($view_id)) {
      throw new ConfigException('No configuration set for Google Analytics View ID');
    }

    return $view_id;
  }

}
