<?php

/**
 * @file
 * Contains \Drupal\textbook_companion\Form\TextbookCompanionSettingsForm.
 */

namespace Drupal\textbook_companion\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\Element;

class TextbookCompanionSettingsForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'textbook_companion_settings_form';
  }

  public function buildForm(array $form, \Drupal\Core\Form\FormStateInterface $form_state) {
    $form['emails'] = [
      '#type' => 'textfield',
      '#title' => t('(Bcc) Notification emails'),
      '#description' => t('Specify emails id for Bcc option of mail system with comma separated'),
       // '#size' => 50,
      '#maxlength' => 255,
      '#required' => TRUE,
      '#default_value' => \Drupal::config('textbook_companion.settings')->get('textbook_companion_emails'),
    ];
    /* $form['to_emails'] = array(
    '#type' => 'textfield',
    '#title' => t('Notification emails to all'),
    '#description' => t('A comma separated list of email addresses to receive notifications emails'),
     // '#size' => 50,
    '#maxlength' => 255,
    '#required' => TRUE,
    '#default_value' => variable_get('textbook_companion_emails_all', ''),
  );*/

    $form['cc_emails'] = [
      '#type' => 'textfield',
      '#title' => t('(Cc) Notification emails'),
      '#description' => t('Specify emails id for Cc option of mail system with comma separated'),
       // '#size' => 50,
      '#maxlength' => 255,
      '#required' => TRUE,
      '#default_value' => \Drupal::config('textbook_companion.settings')->get('textbook_companion_cc_emails'),
    ];
    $form['from_email'] = [
      '#type' => 'textfield',
      '#title' => t('Outgoing from email address'),
      '#description' => t('Email address to be display in the from field of all outgoing messages'),
       // '#size' => 50,
      '#maxlength' => 255,
      '#required' => TRUE,
      '#default_value' => \Drupal::config('textbook_companion.settings')->get('textbook_companion_from_email'),
    ];

    $form['extensions']['source'] = [
      '#type' => 'textfield',
      '#title' => t('Allowed source file extensions'),
      '#description' => t('A comma separated list WITHOUT SPACE of source file extensions that are permitted to be uploaded on the server'),
       // '#size' => 50,
      '#maxlength' => 255,
      '#required' => TRUE,
      '#default_value' => \Drupal::config('textbook_companion.settings')->get('textbook_companion_source_extensions'),
    ];
    /* $form['extensions']['dependency'] = array(
    '#type' => 'textfield',
    '#title' => t('Allowed dependency file extensions'),
    '#description' => t('A comma separated list WITHOUT SPACE of dependency file extensions that are permitted to be uploaded on the server'),
     // '#size' => 50,
    '#maxlength' => 255,
    '#required' => TRUE,
    '#default_value' => variable_get('textbook_companion_dependency_extensions', ''),
  );
  $form['extensions']['result'] = array(
    '#type' => 'textfield',
    '#title' => t('Allowed result file extensions'),
    '#description' => t('A comma separated list WITHOUT SPACE of result file extensions that are permitted to be uploaded on the server'),
     // '#size' => 50,
    '#maxlength' => 255,
    '#required' => TRUE,
    '#default_value' => variable_get('textbook_companion_result_extensions', ''),
  );
  $form['extensions']['xcos'] = array(
    '#type' => 'textfield',
    '#title' => t('Allowed xcos file extensions'),
    '#description' => t('A comma separated list WITHOUT SPACE of xcos file extensions that are permitted to be uploaded on the server'),
     // '#size' => 50,
    '#maxlength' => 255,
    '#required' => TRUE,
    '#default_value' => variable_get('textbook_companion_xcos_extensions', ''),
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
    \Drupal::configFactory()->getEditable('textbook_companion.settings')->set('textbook_companion_emails', $form_state->getValue(['emails']))->save();
    \Drupal::configFactory()->getEditable('textbook_companion.settings')->set('textbook_companion_cc_emails', $form_state->getValue(['cc_emails']))->save();
    //variable_set('textbook_companion_emails_all', $form_state['values']['to_emails']);
    \Drupal::configFactory()->getEditable('textbook_companion.settings')->set('textbook_companion_from_email', $form_state->getValue(['from_email']))->save();
    \Drupal::configFactory()->getEditable('textbook_companion.settings')->set('textbook_companion_source_extensions', $form_state->getValue(['source']))->save();
    //variable_set('textbook_companion_dependency_extensions', $form_state['values']['dependency']);
    //variable_set('textbook_companion_result_extensions', $form_state['values']['result']);
    //variable_set('textbook_companion_xcos_extensions', $form_state['values']['xcos']);
    \Drupal::messenger()->addStatus(t('Settings updated'));
  }

}
?>
