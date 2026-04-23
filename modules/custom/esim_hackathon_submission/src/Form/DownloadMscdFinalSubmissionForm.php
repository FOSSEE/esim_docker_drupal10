<?php

namespace Drupal\hackathon_submission\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;

class DownloadMscdFinalSubmissionForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'download_mscd_final_submission_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $submission_id = \Drupal::routeMatch()->getParameter('submission_id'); 
    // Adjust parameter name based on your route definition

    if (!$submission_id) {
      $form['error'] = [
        '#markup' => '<p style="color:red;">Invalid submission ID.</p>',
      ];
      return $form;
    }

    // Fetch final submission data
    $connection = \Drupal::database();

    $query = $connection->select('mixed_signal_marathon_final_submission', 'm');
    $query->fields('m');
    $query->condition('literature_survey_id', $submission_id);
    $final_submission_data = $query->execute()->fetchObject();

    if (!$final_submission_data) {
      $form['error'] = [
        '#markup' => '<p style="color:red;">Final submission not found.</p>',
      ];
      return $form;
    }

    // Fetch literature survey details
    $query = $connection->select('mixed_signal_marathon_literature_survey', 'l');
    $query->fields('l');
    $query->condition('id', $final_submission_data->literature_survey_id);
    $literature_submission_data = $query->execute()->fetchObject();

    if (!$literature_submission_data) {
      $form['error'] = [
        '#markup' => '<p style="color:red;">Literature survey data not found.</p>',
      ];
      return $form;
    }

    // Display fields
    $form['participant_name'] = [
      '#title' => $this->t('Participant Name'),
      '#type' => 'item',
      '#markup' => $literature_submission_data->participant_name,
    ];

    $form['institute'] = [
      '#type' => 'item',
      '#title' => $this->t('Name of the college/institute'),
      '#markup' => $literature_submission_data->institute,
    ];

    $form['circuit_name'] = [
      '#title' => $this->t('Circuit Name'),
      '#type' => 'item',
      '#markup' => $literature_submission_data->circuit_name,
    ];

    $form['github_repo_link'] = [
      '#type' => 'item',
      '#title' => $this->t('Link to the GitHub repository'),
      '#markup' => $final_submission_data->github_repo_link,
    ];

    // Drupal 10 link replacement for l()
    $download_url = Url::fromRoute(
      'hackathon_submission.mscd_download_final_submission',
      ['submission_id' => $literature_submission_data->id]
    );

    $form['final_report'] = [
      '#type' => 'link',
      '#title' => $this->t('Download Final Report and Project Files'),
      '#url' => $download_url,
      // '#attributes' => ['class' => ['button', 'button--primary']],
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // No submit action required
  }

}
