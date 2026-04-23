<?php

namespace Drupal\hackathon_submission\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Link;
use Drupal\Core\Url;
use Symfony\Component\HttpFoundation\RedirectResponse;

class ViewSocMarathonFinalSubmissionForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'view_soc_marathon_final_submission_form';
  }

  public function buildForm(array $form, \Drupal\Core\Form\FormStateInterface $form_state) {
    $user = \Drupal::currentUser();
    // $submission_id = arg(3);
    $submission_id = \Drupal::routeMatch()->getParameter('submission_id');

    // if ($user->uid == 0) {
    //   $msg = \Drupal::messenger()->addError(t('It is mandatory to log in on this website to edit your submission. If you are new user please create a new account first.'));
    //   //drupal_goto('esim-circuit-simulation-project');
// return new RedirectResponse(
//     Url::fromRoute('user.login', [], [
//       'query' => ['destination' => $destination],
//     ])->toString()
//   );      return $msg;
    // }

// $current_user = $this->currentUser();



    //  //$user->uid == 0
    $query = \Drupal::database()->select('mixed_signal_soc_marathon_final_submission');
    $query->fields('mixed_signal_soc_marathon_final_submission');
    $query->condition('literature_survey_id', $submission_id);
    //$query->range(0, 1);
    $submission_q = $query->execute();
    $final_submission_data = $submission_q->fetchObject();
    $query = \Drupal::database()->select('mixed_signal_soc_marathon_literature_survey');
    $query->fields('mixed_signal_soc_marathon_literature_survey');
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
    $form['github_repo_link'] = [
      '#type' => 'item',
      '#title' => t('Link to the GitHub repository'),
      '#markup' => $final_submission_data->github_repo_link,
    ];
    // @FIXME
    // l() expects a Url object, created from a route name or external URI.
    // $form['reference_files']['final_report'] = array(
    //         '#type' => 'item',
    //         '#markup' => l('Download Final Report and Project Files', 'mixed-signal-soc-design-marathon/download/final-submission/' . $literature_submission_data->id)
    //     );
    $form['reference_files']['final_report'] = Link::fromTextAndUrl(
  $this->t('Download Final Report and Project Files'),
  Url::fromUri('internal:/mixed-signal-soc-design-marathon/download/final-submission/' . $literature_submission_data->id)
)->toRenderable();


// $current_user = $this->currentUser();

// if ($current_user->id() == $literature_submission_data->uid) {

//   $edit_link = Link::fromTextAndUrl(
//     $this->t('Edit submission'),
//     Url::fromUri('internal:/mixed-signal-soc-design-marathon/edit/final-submission/' . $literature_submission_data->id)
//   )->toRenderable();

//   $back_link = Link::fromTextAndUrl(
//     $this->t('Go Back'),
//     Url::fromUri('internal:/mixed-signal-soc-design-marathon/proposed')
//   )->toRenderable();

//   $form['actions_links'] = [
//     '#type' => 'container',
//     '#attributes' => ['class' => ['submission-links']],
//     'edit' => $edit_link,
//     'separator' => [
//       '#markup' => ' | ',
//     ],
//     'back' => $back_link,
//   ];
// }
// else {

//   $form['all_submissions'] = [
//     '#type' => 'markup',
//     '#markup' => Link::fromTextAndUrl(
//       $this->t('Go Back'),
//       Url::fromUri('internal:/mixed-signal-soc-design-marathon/view/final-submissions/')
//     )->toString(),
//   ];
// }

// $current_user = $this->currentUser();

// if ($current_user->id() == $literature_submission_data->uid) {

//   $edit_link = Link::fromTextAndUrl(
//     $this->t('Edit submission'),
//     Url::fromUri('internal:/mixed-signal-soc-design-marathon/edit/final-submission/' . $literature_submission_data->id)
//   )->toRenderable();

//   $back_link = Link::fromTextAndUrl(
//     $this->t('Go Back'),
//     Url::fromUri('internal:/mixed-signal-soc-design-marathon/proposed')
//   )->toRenderable();

//   $form['actions_links'] = [
//     '#type' => 'container',
//     '#attributes' => ['class' => ['submission-links']],
//     'edit' => [
//       '#type' => 'container',
//       '#attributes' => ['style' => 'margin-bottom: 8px;'],
//       'link' => $edit_link,
//     ],
//     'back' => [
//       '#type' => 'container',
//       'link' => $back_link,
//     ],
//   ];
// }
// else {

//   $form['all_submissions'] = [
//     '#type' => 'container',
//     '#attributes' => ['style' => 'margin-bottom:8px;'],
//     'link' => Link::fromTextAndUrl(
//       $this->t('Go Back'),
//       Url::fromUri('internal:/mixed-signal-design-marathon/all-submissions/final-submissions/')
//     )->toRenderable(),
//   ];
// }


   return $form;
  }

      public function submitForm(array &$form, \Drupal\Core\Form\FormStateInterface $form_state) {
      }
 




}
?>
