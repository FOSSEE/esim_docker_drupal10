<?php

/**
 * @file
 * Contains \Drupal\hackathon_submission\Form\HackathonSubmissionViewFinalSubmissionsDetailsForm.
 */

namespace Drupal\hackathon_submission\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\Element;
use Drupal\Core\Link;
use Drupal\Core\Url;
use Symfony\Component\HttpFoundation\RedirectResponse;


class HackathonSubmissionViewFinalSubmissionsDetailsForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'hackathon_submission_view_final_submissions_details_form';
  }

  // public function buildForm(array $form, \Drupal\Core\Form\FormStateInterface $form_state) {
  //   $user = \Drupal::currentUser();
  //   /* get current proposal */
  //   $submission_id = (int) arg(3);
  //   $submission_q = \Drupal::database()->query("SELECT * FROM {hackathon_literature_survey} WHERE id = :id", [
  //     ':id' => $submission_id
  //     ]);
  //   $submission_data = $submission_q->fetchObject();
  //   $final_submission_q = \Drupal::database()->query("SELECT * FROM {hackathon_final_submission} WHERE literature_survey_id = :literature_survey_id", [
  //     ':literature_survey_id' => $submission_id
  //     ]);
  //   $final_submission_data = $final_submission_q->fetchObject();

  //   $form['contributor_name'] = [
  //     '#type' => 'item',
  //     '#markup' => $submission_data->participant_name,
  //     '#title' => t('Student name'),
  //   ];
  //   $form['student_email_id'] = [
  //     '#title' => t('Student Email'),
  //     '#type' => 'item',
  //     '#markup' => $submission_data->participant_email,
  //     '#title' => t('Email'),
  //   ];
  //   $form['university'] = [
  //     '#type' => 'item',
  //     '#markup' => $submission_data->institute,
  //     '#title' => t('Institute'),
  //   ];
  //   $form['abstract'] = [
  //     '#type' => 'item',
  //     '#markup' => $final_submission_data->abstract,
  //     '#title' => t('Abstract'),
  //   ];
  //   $form['circuit_details'] = [
  //     '#type' => 'item',
  //     '#markup' => $final_submission_data->circuit_details,
  //     '#title' => t('Circuit Details'),
  //   ];
  //   // @FIXME
  //   // l() expects a Url object, created from a route name or external URI.
  //   // $form['cancel'] = array(
  //   //         '#type' => 'markup',
  //   //         '#markup' => l(t('Cancel'), 'hackathon-submission/all-submissions')
  //   //     );

  //   return $form;
  // }

  //     public function submitForm(array &$form, \Drupal\Core\Form\FormStateInterface $form_state) {
      // }
     

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $submission_id = NULL) {
    $connection = \Drupal::database();

    // Load literature survey.
    $submission = $connection->query(
      "SELECT * FROM {hackathon_literature_survey} WHERE id = :id",
      [':id' => (int) $submission_id]
    )->fetchObject();

    if (!$submission) {
      $this->messenger()->addError($this->t('Invalid submission.'));
      return new RedirectResponse(Url::fromRoute('<front>')->toString());
    }

    // Load final submission.
    $final_submission = $connection->query(
      "SELECT * FROM {hackathon_final_submission}
       WHERE literature_survey_id = :id",
      [':id' => (int) $submission_id]
    )->fetchObject();

    if (!$final_submission) {
      $this->messenger()->addError($this->t('Final submission not found.'));
      return new RedirectResponse(Url::fromRoute('<front>')->toString());
    }

    $form['contributor_name'] = [
      '#type' => 'item',
      '#title' => $this->t('Student Name'),
      '#markup' => $submission->participant_name,
    ];

    $form['student_email_id'] = [
      '#type' => 'item',
      '#title' => $this->t('Email'),
      '#markup' => $submission->participant_email,
    ];

    $form['university'] = [
      '#type' => 'item',
      '#title' => $this->t('Institute'),
      '#markup' => $submission->institute,
    ];

    $form['abstract'] = [
      '#type' => 'item',
      '#title' => $this->t('Abstract'),
      '#markup' => $final_submission->abstract,
    ];

    $form['circuit_details'] = [
      '#type' => 'item',
      '#title' => $this->t('Circuit Details'),
      '#markup' => $final_submission->circuit_details,
    ];

    // Cancel link.
    $form['cancel'] = [
      '#type' => 'markup',
      '#markup' => Link::fromTextAndUrl(
        $this->t('Back to all submissions'),
        Url::fromRoute('hackathon_submission.display_final_submissions')
      )->toString(),
    ];

    return $form;
  }

  /**
   * No submit handler – view-only form.
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {}

}


?>
