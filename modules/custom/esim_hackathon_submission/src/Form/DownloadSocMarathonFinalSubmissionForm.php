<?php

namespace Drupal\hackathon_submission\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\Core\Link;

/**
 * Form to display SOC marathon final submission details.
 */
class DownloadSocMarathonFinalSubmissionForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'download_soc_marathon_final_submission_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    // Retrieve submission ID from route parameter instead of arg(3)
    $submission_id = \Drupal::routeMatch()->getParameter('submission_id');

    if (!$submission_id) {
      return [
        '#markup' => 'Invalid submission ID.',
      ];
    }

    $connection = \Drupal::database();

    // Fetch final submission data
    $query = $connection->select('mixed_signal_soc_marathon_final_submission', 'f');
    $query->fields('f');
    $query->condition('f.literature_survey_id', $submission_id);
    $final_submission_data = $query->execute()->fetchObject();

    if (!$final_submission_data) {
      return ['#markup' => 'Submission not found.'];
    }

    // Fetch literature survey data
    $query = $connection->select('mixed_signal_soc_marathon_literature_survey', 'l');
    $query->fields('l');
    $query->condition('l.id', $final_submission_data->literature_survey_id);
    $literature_submission_data = $query->execute()->fetchObject();

    if (!$literature_submission_data) {
      return ['#markup' => 'Literature survey details missing.'];
    }

    // FORM ELEMENTS
    $form['participant_name'] = [
      '#type' => 'item',
      '#title' => $this->t('Participant Name'),
      '#markup' => $literature_submission_data->participant_name,
    ];

    $form['institute'] = [
      '#type' => 'item',
      '#title' => $this->t('Name of the college/institute'),
      '#markup' => $literature_submission_data->institute,
    ];

    $form['circuit_name'] = [
      '#type' => 'item',
      '#title' => $this->t('Circuit Name'),
      '#markup' => $literature_submission_data->circuit_name,
    ];

    $form['github_repo_link'] = [
      '#type' => 'item',
      '#title' => $this->t('Link to the GitHub repository'),
      '#markup' => $final_submission_data->github_repo_link,
    ];

    // Download link (converted from deprecated l())
    $url = Url::fromRoute('hackathon_submission.soc_marathon_download_final_submission', [
      'submission_id' => $literature_submission_data->id,
    ]);

    $form['download_files'] = [
      '#type' => 'item',
      // '#title' => $this->t('Download Final Report & Project Files'),
      '#markup' => Link::fromTextAndUrl('Download Final Report & Project Files', $url)->toString(),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // No submit action required.
  }

}
