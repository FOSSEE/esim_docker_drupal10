<?php

/**
 * @file
 * Contains \Drupal\hackathon_submission\Form\HackathonSubmissionSettingsForm.
 */

namespace Drupal\hackathon_submission\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\Element;

class HackathonSubmissionSettingsForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'hackathon_submission_settings_form';
  }

  public function buildForm(array $form, \Drupal\Core\Form\FormStateInterface $form_state) {
    /************************** SoC Marathon date and extension settings **************************/
    // @FIXME
// // @FIXME
// // This looks like another module's variable. You'll need to rewrite this call
// // to ensure that it uses the correct configuration object.
// $form['extensions']['soc_marathon_literature_report_extensions'] = array(
//         '#type' => 'textfield',
//         '#title' => t('Allowed file extensions for uploading SoC Marathon Literature report'),
//         '#description' => t('A comma separated list WITHOUT SPACE of source file extensions that are permitted to be uploaded on the server'),
//         '#size' => 50,
//         '#maxlength' => 255,
//         '#required' => TRUE,
//         '#default_value' => variable_get('soc_marathon_literature_report_extensions', '')
//     );

    // @FIXME
// // @FIXME
// // This looks like another module's variable. You'll need to rewrite this call
// // to ensure that it uses the correct configuration object.
// $form['extensions']['soc_marathon_literature_report_start_date'] = array(
//         '#type' => 'textfield',
//         '#title' => t('SoC marathon Literature Survey start date'),
//         '#description' => t('For eg: 2022-02-28 23:59:59.0, enter in this format'),
//         '#size' => 50,
//         '#maxlength' => 255,
//         '#required' => TRUE,
//         '#default_value' => variable_get('soc_marathon_literature_report_start_date', '')
//     );

    // @FIXME
// // @FIXME
// // This looks like another module's variable. You'll need to rewrite this call
// // to ensure that it uses the correct configuration object.
// $form['extensions']['soc_marathon_literature_report_last_date'] = array(
//         '#type' => 'textfield',
//         '#title' => t('SoC marathon Literature Survey Last date'),
//         '#description' => t('For eg: 2022-02-28 23:59:59.0, enter in this format'),
//         '#size' => 50,
//         '#maxlength' => 255,
//         '#required' => TRUE,
//         '#default_value' => variable_get('soc_marathon_literature_report_last_date', '')
//     );

    // @FIXME
// // @FIXME
// // This looks like another module's variable. You'll need to rewrite this call
// // to ensure that it uses the correct configuration object.
// $form['extensions']['soc_marathon_final_report_extensions'] = array(
//         '#type' => 'textfield',
//         '#title' => t('Allowed file extensions for uploading SoC Design Marathon Final report'),
//         '#description' => t('A comma separated list WITHOUT SPACE of source file extensions that are permitted to be uploaded on the server'),
//         '#size' => 50,
//         '#maxlength' => 255,
//         '#required' => TRUE,
//         '#default_value' => variable_get('soc_marathon_final_report_extensions', '')
//     );

    // @FIXME
// // @FIXME
// // This looks like another module's variable. You'll need to rewrite this call
// // to ensure that it uses the correct configuration object.
// $form['extensions']['soc_marathon_project_files_extensions'] = array(
//         '#type' => 'textfield',
//         '#title' => t('Allowed file extensions for uploading SoC Design Marathon Project Files'),
//         '#description' => t('A comma separated list WITHOUT SPACE of source file extensions that are permitted to be uploaded on the server'),
//         '#size' => 50,
//         '#maxlength' => 255,
//         '#required' => TRUE,
//         '#default_value' => variable_get('soc_marathon_project_files_extensions', '')
//     );

    // @FIXME
// // @FIXME
// // This looks like another module's variable. You'll need to rewrite this call
// // to ensure that it uses the correct configuration object.
// $form['extensions']['soc_marathon_final_submission_start_date'] = array(
//         '#type' => 'textfield',
//         '#title' => t('SoC Design Marathon Final start date'),
//         '#description' => t('For eg: 2022-02-28 23:59:59.0, enter in this format'),
//         '#size' => 50,
//         '#maxlength' => 255,
//         '#required' => TRUE,
//         '#default_value' => variable_get('soc_marathon_final_submission_start_date', '')
//     );

    // @FIXME
// // @FIXME
// // This looks like another module's variable. You'll need to rewrite this call
// // to ensure that it uses the correct configuration object.
// $form['extensions']['soc_marathon_final_submission_last_date'] = array(
//         '#type' => 'textfield',
//         '#title' => t('SoC Design Marathon Final Submission Last date'),
//         '#description' => t('For eg: 2022-02-28 23:59:59.0, enter in this format'),
//         '#size' => 50,
//         '#maxlength' => 255,
//         '#required' => TRUE,
//         '#default_value' => variable_get('soc_marathon_final_submission_last_date', '')
//     );

    /************************* Settings for SoC marathon ends *****************************************/
    // @FIXME
// // @FIXME
// // This looks like another module's variable. You'll need to rewrite this call
// // to ensure that it uses the correct configuration object.
// $form['extensions']['mscd_literature_report_extensions'] = array(
// 		'#type' => 'textfield',
// 		'#title' => t('Allowed file extensions for uploading MSCD Literature report'),
// 		'#description' => t('A comma separated list WITHOUT SPACE of source file extensions that are permitted to be uploaded on the server'),
// 		'#size' => 50,
// 		'#maxlength' => 255,
// 		'#required' => TRUE,
// 		'#default_value' => variable_get('mscd_literature_report_extensions', '')
// 	);

    // @FIXME
// // @FIXME
// // This looks like another module's variable. You'll need to rewrite this call
// // to ensure that it uses the correct configuration object.
// $form['extensions']['mscd_literature_report_start_date'] = array(
// 		'#type' => 'textfield',
// 		'#title' => t('MSCD Literature Survey start date'),
// 		'#description' => t('For eg: 2022-02-28 23:59:59.0, enter in this format'),
// 		'#size' => 50,
// 		'#maxlength' => 255,
// 		'#required' => TRUE,
// 		'#default_value' => variable_get('mscd_literature_report_start_date', '')
// 	);

    // @FIXME
// // @FIXME
// // This looks like another module's variable. You'll need to rewrite this call
// // to ensure that it uses the correct configuration object.
// $form['extensions']['mscd_literature_report_last_date'] = array(
// 		'#type' => 'textfield',
// 		'#title' => t('MSCD Literature Survey Last date'),
// 		'#description' => t('For eg: 2022-02-28 23:59:59.0, enter in this format'),
// 		'#size' => 50,
// 		'#maxlength' => 255,
// 		'#required' => TRUE,
// 		'#default_value' => variable_get('mscd_literature_report_last_date', '')
// 	);

    // @FIXME
// // @FIXME
// // This looks like another module's variable. You'll need to rewrite this call
// // to ensure that it uses the correct configuration object.
// $form['extensions']['mscd_final_report_extensions'] = array(
// 		'#type' => 'textfield',
// 		'#title' => t('Allowed file extensions for uploading MSCD Final report'),
// 		'#description' => t('A comma separated list WITHOUT SPACE of source file extensions that are permitted to be uploaded on the server'),
// 		'#size' => 50,
// 		'#maxlength' => 255,
// 		'#required' => TRUE,
// 		'#default_value' => variable_get('mscd_final_report_extensions', '')
// 	);

    // @FIXME
// // @FIXME
// // This looks like another module's variable. You'll need to rewrite this call
// // to ensure that it uses the correct configuration object.
// $form['extensions']['mscd_project_files_extensions'] = array(
// 		'#type' => 'textfield',
// 		'#title' => t('Allowed file extensions for uploading MSCD Project Files'),
// 		'#description' => t('A comma separated list WITHOUT SPACE of source file extensions that are permitted to be uploaded on the server'),
// 		'#size' => 50,
// 		'#maxlength' => 255,
// 		'#required' => TRUE,
// 		'#default_value' => variable_get('mscd_project_files_extensions', '')
// 	);

    // @FIXME
// // @FIXME
// // This looks like another module's variable. You'll need to rewrite this call
// // to ensure that it uses the correct configuration object.
// $form['extensions']['mscd_final_submission_start_date'] = array(
// 		'#type' => 'textfield',
// 		'#title' => t('MSCD Final start date'),
// 		'#description' => t('For eg: 2022-02-28 23:59:59.0, enter in this format'),
// 		'#size' => 50,
// 		'#maxlength' => 255,
// 		'#required' => TRUE,
// 		'#default_value' => variable_get('mscd_final_submission_start_date', '')
// 	);

    // @FIXME
// // @FIXME
// // This looks like another module's variable. You'll need to rewrite this call
// // to ensure that it uses the correct configuration object.
// $form['extensions']['mscd_final_submission_last_date'] = array(
// 		'#type' => 'textfield',
// 		'#title' => t('MSCD Final Submission Last date'),
// 		'#description' => t('For eg: 2022-02-28 23:59:59.0, enter in this format'),
// 		'#size' => 50,
// 		'#maxlength' => 255,
// 		'#required' => TRUE,
// 		'#default_value' => variable_get('mscd_final_submission_last_date', '')
// 	);

    // @FIXME
// // @FIXME
// // This looks like another module's variable. You'll need to rewrite this call
// // to ensure that it uses the correct configuration object.
// $form['extensions']['reference_circuit'] = array(
// 		'#type' => 'textfield',
// 		'#title' => t('Allowed file extensions for uploading reference circuit file'),
// 		'#description' => t('A comma separated list WITHOUT SPACE of source file extensions that are permitted to be uploaded on the server'),
// 		'#size' => 50,
// 		'#maxlength' => 255,
// 		'#required' => TRUE,
// 		'#default_value' => variable_get('reference_circuit_extensions', '')
// 	);

    // @FIXME
// // @FIXME
// // This looks like another module's variable. You'll need to rewrite this call
// // to ensure that it uses the correct configuration object.
// $form['extensions']['reference_waveform'] = array(
// 		'#type' => 'textfield',
// 		'#title' => t('Allowed file extensions for uploading reference waveform file'),
// 		'#description' => t('A comma separated list WITHOUT SPACE of source file extensions that are permitted to be uploaded on the server'),
// 		'#size' => 50,
// 		'#maxlength' => 255,
// 		'#required' => TRUE,
// 		'#default_value' => variable_get('reference_waveform_extensions', '')
// 	);

    // @FIXME
// // @FIXME
// // This looks like another module's variable. You'll need to rewrite this call
// // to ensure that it uses the correct configuration object.
// $form['extensions']['main_netlist'] = array(
// 		'#type' => 'textfield',
// 		'#title' => t('Allowed file extensions for uploading Main netlist file'),
// 		'#description' => t('A comma separated list WITHOUT SPACE of source file extensions that are permitted to be uploaded on the server'),
// 		'#size' => 50,
// 		'#maxlength' => 255,
// 		'#required' => TRUE,
// 		'#default_value' => variable_get('main_netlist_file_extensions', '')
// 	);

    // @FIXME
// // @FIXME
// // This looks like another module's variable. You'll need to rewrite this call
// // to ensure that it uses the correct configuration object.
// $form['extensions']['sub_circuit_file'] = array(
// 		'#type' => 'textfield',
// 		'#title' => t('Allowed file extensions for uploading sub circuit file'),
// 		'#description' => t('A comma separated list WITHOUT SPACE of source file extensions that are permitted to be uploaded on the server'),
// 		'#size' => 50,
// 		'#maxlength' => 255,
// 		'#required' => TRUE,
// 		'#default_value' => variable_get('subcircuit_file_extensions', '')
// 	);

    // @FIXME
// // @FIXME
// // This looks like another module's variable. You'll need to rewrite this call
// // to ensure that it uses the correct configuration object.
// $form['extensions']['read_me'] = array(
// 		'#type' => 'textfield',
// 		'#title' => t('Allowed file extensions for uploading Read me file'),
// 		'#description' => t('A comma separated list WITHOUT SPACE of source file extensions that are permitted to be uploaded on the server'),
// 		'#size' => 50,
// 		'#maxlength' => 255,
// 		'#required' => TRUE,
// 		'#default_value' => variable_get('readme_file_extensions', '')
// 	);

    /*$form['no_of_images_allowed_project_submission'] = array(
		'#type' => 'textfield',
		'#title' => t('Enter the number of images that can be uploaded during project submission'),
		'#size' => 50,
		'#default_value' => variable_get('no_of_images_allowed_project_submission', '')
	);
	$form['extensions']['project_design_files'] = array(
		'#type' => 'textfield',
		'#title' => t('Allowed file extensions for uploading project design files'),
		'#description' => t('A comma separated list WITHOUT SPACE of source file extensions that are permitted to be uploaded on the server'),
		'#size' => 50,
		'#maxlength' => 255,
		'#required' => TRUE,
		'#default_value' => variable_get('project_design_files_extensions', '')
	);*/
    $form['submit'] = [
      '#type' => 'submit',
      '#value' => t('Submit'),
    ];
    return $form;
  }

  public function validateForm(array &$form, \Drupal\Core\Form\FormStateInterface $form_state) {
    return;
  }

  public function submitForm(array &$form, \Drupal\Core\Form\FormStateInterface $form_state) {
    // @FIXME
// // @FIXME
// // This looks like another module's variable. You'll need to rewrite this call
// // to ensure that it uses the correct configuration object.
// variable_set('soc_marathon_literature_report_extensions', $form_state['values']['soc_marathon_literature_report_extensions']);

    // @FIXME
// // @FIXME
// // This looks like another module's variable. You'll need to rewrite this call
// // to ensure that it uses the correct configuration object.
// variable_set('soc_marathon_literature_report_start_date', $form_state['values']['soc_marathon_literature_report_start_date']);

    // @FIXME
// // @FIXME
// // This looks like another module's variable. You'll need to rewrite this call
// // to ensure that it uses the correct configuration object.
// variable_set('soc_marathon_literature_report_last_date', $form_state['values']['soc_marathon_literature_report_last_date']);

    // @FIXME
// // @FIXME
// // This looks like another module's variable. You'll need to rewrite this call
// // to ensure that it uses the correct configuration object.
// variable_set('soc_marathon_final_report_extensions', $form_state['values']['soc_marathon_final_report_extensions']);

    // @FIXME
// // @FIXME
// // This looks like another module's variable. You'll need to rewrite this call
// // to ensure that it uses the correct configuration object.
// variable_set('soc_marathon_project_files_extensions', $form_state['values']['soc_marathon_project_files_extensions']);

    // @FIXME
// // @FIXME
// // This looks like another module's variable. You'll need to rewrite this call
// // to ensure that it uses the correct configuration object.
// variable_set('soc_marathon_final_submission_start_date', $form_state['values']['soc_marathon_final_submission_start_date']);

    // @FIXME
// // @FIXME
// // This looks like another module's variable. You'll need to rewrite this call
// // to ensure that it uses the correct configuration object.
// variable_set('soc_marathon_final_submission_last_date', $form_state['values']['soc_marathon_final_submission_last_date']);

    // @FIXME
// // @FIXME
// // This looks like another module's variable. You'll need to rewrite this call
// // to ensure that it uses the correct configuration object.
// variable_set('mscd_literature_report_extensions', $form_state['values']['mscd_literature_report_extensions']);

    // @FIXME
// // @FIXME
// // This looks like another module's variable. You'll need to rewrite this call
// // to ensure that it uses the correct configuration object.
// variable_set('mscd_literature_report_start_date', $form_state['values']['mscd_literature_report_start_date']);

    // @FIXME
// // @FIXME
// // This looks like another module's variable. You'll need to rewrite this call
// // to ensure that it uses the correct configuration object.
// variable_set('mscd_literature_report_last_date', $form_state['values']['mscd_literature_report_last_date']);

    // @FIXME
// // @FIXME
// // This looks like another module's variable. You'll need to rewrite this call
// // to ensure that it uses the correct configuration object.
// variable_set('mscd_final_report_extensions', $form_state['values']['mscd_final_report_extensions']);

    // @FIXME
// // @FIXME
// // This looks like another module's variable. You'll need to rewrite this call
// // to ensure that it uses the correct configuration object.
// variable_set('mscd_project_files_extensions', $form_state['values']['mscd_project_files_extensions']);

    // @FIXME
// // @FIXME
// // This looks like another module's variable. You'll need to rewrite this call
// // to ensure that it uses the correct configuration object.
// variable_set('mscd_final_submission_start_date', $form_state['values']['mscd_final_submission_start_date']);

    // @FIXME
// // @FIXME
// // This looks like another module's variable. You'll need to rewrite this call
// // to ensure that it uses the correct configuration object.
// variable_set('mscd_final_submission_last_date', $form_state['values']['mscd_final_submission_last_date']);

    // @FIXME
// // @FIXME
// // This looks like another module's variable. You'll need to rewrite this call
// // to ensure that it uses the correct configuration object.
// variable_set('reference_circuit_extensions', $form_state['values']['reference_circuit']);

    // @FIXME
// // @FIXME
// // This looks like another module's variable. You'll need to rewrite this call
// // to ensure that it uses the correct configuration object.
// variable_set('reference_waveform_extensions', $form_state['values']['reference_waveform']);

    // @FIXME
// // @FIXME
// // This looks like another module's variable. You'll need to rewrite this call
// // to ensure that it uses the correct configuration object.
// variable_set('main_netlist_file_extensions', $form_state['values']['main_netlist']);

    // @FIXME
// // @FIXME
// // This looks like another module's variable. You'll need to rewrite this call
// // to ensure that it uses the correct configuration object.
// variable_set('subcircuit_file_extensions', $form_state['values']['sub_circuit_file']);

    // @FIXME
// // @FIXME
// // This looks like another module's variable. You'll need to rewrite this call
// // to ensure that it uses the correct configuration object.
// variable_set('readme_file_extensions', $form_state['values']['read_me']);

    \Drupal::messenger()->addStatus(t('Settings updated'));
  }

}
?>
