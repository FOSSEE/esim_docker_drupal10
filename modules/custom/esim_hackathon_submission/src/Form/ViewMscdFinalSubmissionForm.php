<?php

/**
 * @file
 * Contains \Drupal\hackathon_submission\Form\ViewMscdFinalSubmissionForm.
 */

namespace Drupal\hackathon_submission\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\Element;
use Drupal\Core\Url;

class ViewMscdFinalSubmissionForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'view_mscd_final_submission_form';
  }

  public function buildForm(array $form, \Drupal\Core\Form\FormStateInterface $form_state) {
    $user = \Drupal::currentUser();
        $submission_id = \Drupal::routeMatch()->getParameter('submission_id');
    // $submission_id = arg(3);
    // if ($user->uid == 0) {
    //   $msg = \Drupal::messenger()->addError(t('It is mandatory to log in on this website to edit your submission. If you are new user please create a new account first.'));
    //   //drupal_goto('esim-circuit-simulation-project');
    //   drupal_goto('user/login', [
    //     'query' => drupal_get_destination()
    //     ]);
    //   return $msg;
    // } //$user->uid == 0
    $query = \Drupal::database()->select('mixed_signal_marathon_final_submission');
    $query->fields('mixed_signal_marathon_final_submission');
    $query->condition('literature_survey_id', $submission_id);
    //$query->range(0, 1);
    $submission_q = $query->execute();
    $final_submission_data = $submission_q->fetchObject();
    $query = \Drupal::database()->select('mixed_signal_marathon_literature_survey');
    $query->fields('mixed_signal_marathon_literature_survey');
    $query->condition('id', $final_submission_data->literature_survey_id);
    //$query->range(0, 1);
    $submission_q = $query->execute();
    $literature_submission_data = $submission_q->fetchObject();
    $form = [];
    $form['participant_name'] = [
      '#title' => t('Participant Name'),
      '#type' => 'item',
      '#markup' => $literature_submission_data->participant_name,
    ];
    $form['participant_email'] = [
      '#type' => 'item',
      '#title' => t('Email'),
      '#markup' => $literature_submission_data->participant_email,
    ];
    $form['institute'] = [
      '#type' => 'item',
      '#title' => t('Name of the college/institute'),
      '#markup' => $literature_submission_data->institute,
    ];
    $form['circuit_name'] = [
      '#title' => t('Circuit Name'),
      '#type' => 'item',
      '#markup' => $literature_submission_data->circuit_name,
    ];
    $form['circuit_type'] = [
      '#type' => 'item',
      '#title' => t('Type of the circuit'),
      '#markup' => $literature_submission_data->circuit_type,
    ];
    $form['github_repo_link'] = [
      '#type' => 'item',
      '#title' => t('Link to the GitHub repository'),
      '#markup' => $final_submission_data->github_repo_link,
    ];
    // @FIXME
    // l() expects a Url object, created from a route name or external URI.
    // $form['reference_files']['final_report'] = array(
    //         '#type' => 'item',
    //         // '#markup' => l('Download Final Report and Project Files', 'mixed-signal-design-marathon/download/final-submission/' . $literature_submission_data->id)
    //     );

        $form['reference_files']['final_report'] = [
      '#type' => 'link',
      '#title' => $this->t('Download Final Report and Project Files'),
      '#url' => Url::fromUri(
        'internal:/mixed-signal-design-marathon/download/final-submission/' . $literature_submission_data->id
      ),
      '#prefix' => '<div>',
      '#suffix' => '</div>',
    ];

//     if ($user->uid == $literature_submission_data->uid) {
//       // @FIXME
// // l() expects a Url object, created from a route name or external URI.
// $form['back'] = array(
//         '#type' => 'item',
//         // '#markup' => l('Edit submission', 'mixed-signal-design-marathon/edit/final-submission/' . $literature_submission_data->id) . l(' | Go Back', 'mixed-signal-design-marathon/proposed')
//     );

//     }
//     else {
//       // @FIXME
// // l() expects a Url object, created from a route name or external URI.
// $form['all_submissions'] = array(
//         '#type' => 'item',
//         mixed-signal-design-marathon/all-submissions/final-submissions
//         // '#markup' => l('Go Back', 'mixed-signal-design-marathon/all-submissions')
//     );

//     }

      if ($user->id() == $literature_submission_data->uid) {

      $form['actions'] = [
        '#type' => 'container',
      ];

      $form['actions']['edit'] = [
        '#type' => 'link',
        '#title' => $this->t('Edit submission'),
        '#url' => Url::fromUri(
          'internal:/mixed-signal-design-marathon/edit/final-submission/' . $literature_submission_data->id
        ),
        '#prefix' => '<div>',
        '#suffix' => '</div>',
      ];

      $form['actions']['go_back'] = [
        '#type' => 'link',
        '#title' => $this->t('Go Back'),
        '#url' => Url::fromUri('internal:/mixed-signal-design-marathon/proposed'),
        '#prefix' => '<div>',
        '#suffix' => '</div>',
      ];
    }
    else {

      $form['go_back'] = [
        '#type' => 'link',
        '#title' => $this->t('Go Back'),
        '#url' => Url::fromUri('internal:/mixed-signal-design-marathon/all-submissions/final-submissions'),
        '#prefix' => '<div>',
        '#suffix' => '</div>',
      ];
    }


  
    return $form;
  }

  public function submitForm(array &$form, \Drupal\Core\Form\FormStateInterface $form_state) {
    }
}
?>
