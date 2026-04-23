<?php

namespace Drupal\hackathon_submission\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Database\Connection;
use Drupal\Core\Url;
use Drupal\Core\Link;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class DownloadHackathonFinalSubmissionForm extends FormBase {

   
  public function getFormId(): string {
    return 'download_hackathon_final_submission_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
            $submission_id = \Drupal::routeMatch()->getParameter('submission_id');

    // Get submission ID from route parameter.
    
    if (empty($submission_id) || !is_numeric($submission_id)) {
      $form['error'] = [
        '#markup' => $this->t('Invalid submission ID.'),
      ];
      return $form;
    }

    // Load literature survey data.
    $connection = \Drupal::database();

$literature_data = $connection
  ->select('hackathon_literature_survey', 'hls')
  ->fields('hls')
  ->condition('id', (int) $submission_id)
  ->execute()
  ->fetchObject();

    if (!$literature_data) {
      $form['error'] = [
        '#markup' => $this->t('No submission found.'),
      ];
      return $form;
    }

    // Display fields.
    $form['participant_name'] = [
      '#type' => 'item',
      '#title' => $this->t('Participant Name'),
      '#markup' => $literature_data->participant_name,
    ];

    $form['institute'] = [
      '#type' => 'item',
      '#title' => $this->t('Name of the college/institute'),
      '#markup' => $literature_data->institute,
    ];

    $form['circuit_name'] = [
      '#type' => 'item',
      '#title' => $this->t('Circuit Name'),
      '#markup' => $literature_data->circuit_name,
    ];

    // Download link (use route, not hardcoded internal URI).
    $download_url = Url::fromRoute(
      'hackathon_submission.download_completed_circuit',
      ['submission_id' => (int) $submission_id]
    );

    // $form['download_files'] = [
    //   '#type' => 'item',
    //   '#markup' => Link::fromTextAndUrl(
    //     $this->t('Download Final Report and Project Files'),
    //     $download_url
    //   )->toRenderable(),
    // ];
$form['download_files'] = [
  '#type' => 'item',
  '#markup' => Link::fromTextAndUrl(
    $this->t('Download Final Report and Project Files'),
    $download_url
  )->toString(),
];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state): void {
    // No submit action required.
  }

}
