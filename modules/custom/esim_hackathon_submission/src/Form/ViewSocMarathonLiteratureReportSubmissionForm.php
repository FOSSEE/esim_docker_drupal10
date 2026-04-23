<?php

/**
 * @file
 * Contains \Drupal\hackathon_submission\Form\ViewSocMarathonLiteratureReportSubmissionForm.
 */

namespace Drupal\hackathon_submission\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\Element;
use Drupal\Core\Url;

class ViewSocMarathonLiteratureReportSubmissionForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'view_soc_marathon_literature_report_submission_form';
  }

  public function buildForm(array $form, \Drupal\Core\Form\FormStateInterface $form_state) {
    $user = \Drupal::currentUser();
    // $submission_id = arg(3);
        $submission_id = \Drupal::routeMatch()->getParameter('submission_id');

    // if ($user->uid == 0) {
    //   $msg = \Drupal::messenger()->addError(t('It is mandatory to log in on this website to edit your submission. If you are new user please create a new account first.'));
    //   //drupal_goto('esim-circuit-simulation-project');
    //   drupal_goto('user/login', [
    //     'query' => drupal_get_destination()
    //     ]);
    //   return $msg;
    // } //$user->uid == 0
    $query = \Drupal::database()->select('mixed_signal_soc_marathon_literature_survey');
    $query->fields('mixed_signal_soc_marathon_literature_survey');
    $query->condition('id', $submission_id);
    //$query->range(0, 1);
    $submission_q = $query->execute();
    $submission_data = $submission_q->fetchObject();
    $form = [];
    $form['participant_name'] = [
      '#title' => t('Participant Name'),
      '#type' => 'item',
      '#markup' => $submission_data->participant_name,
    ];
    $form['participant_email'] = [
      '#type' => 'item',
      '#title' => t('Email'),
      '#markup' => $submission_data->participant_email,
    ];
    $form['institute'] = [
      '#type' => 'item',
      '#title' => t('Name of the college/institute'),
      '#markup' => $submission_data->institute,
    ];
    $form['circuit_name'] = [
      '#title' => t('Circuit Name'),
      '#type' => 'item',
      '#markup' => $submission_data->circuit_name,
    ];
    // @FIXME
    // l() expects a Url object, created from a route name or external URI.
    // $form['reference_files']['literature_report'] = array(
    //         '#type' => 'item',
    //         '#markup' => l('Download Report', 'mixed-signal-soc-design-marathon/download/literature-report/' . $submission_data->id)
    //     );
//    

$form['reference_files']['literature_report'] = [
  '#type' => 'link',
  '#title' => $this->t('Download Report'),
  '#url' => Url::fromUri('internal:/mixed-signal-soc-design-marathon/download/literature-report/' . $submission_data->id),
'#suffix' => '<br>',

];


$form['go_back'] = [
  '#type' => 'link',
  '#title' => $this->t('Go Back'),
  '#url' => Url::fromUri('internal:/mixed-signal-soc-design-marathon/proposed'),
];

    // @FIXME
    // l() expects a Url object, created from a route name or external URI.
    // $form['go_back'] = array(
    //         '#type' => 'item',
    //         '#markup' => l('Go Back', 'mixed-signal-soc-design-marathon/proposed')
    //     );


    return $form;
  }

      public function submitForm(array &$form, \Drupal\Core\Form\FormStateInterface $form_state) {
      }
}
?>
