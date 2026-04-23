<?php

namespace Drupal\pspice_to_kicad\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Config\ConfigFactoryInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class PspiceToKicadSettingsForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'pspice_to_kicad_settings_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $config = $this->config('pspice_to_kicad.settings');

    $form['emails'] = [
      '#type' => 'textfield',
      '#title' => $this->t('(Bcc) Notification emails'),
      '#description' => $this->t('Specify email IDs for Bcc option, comma separated.'),
      // ' // '#size'' => 50,
      '#maxlength' => 255,
      '#required' => TRUE,
      '#default_value' => $config->get('kicad_emails') ?? '',
    ];

    $form['cc_emails'] = [
      '#type' => 'textfield',
      '#title' => $this->t('(Cc) Notification emails'),
      '#description' => $this->t('Specify email IDs for Cc option, comma separated.'),
      //  '#size'' => 50,
      '#maxlength' => 255,
      '#required' => TRUE,
      '#default_value' => $config->get('kicad_cc_emails') ?? '',
    ];

    $form['from_email'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Outgoing from email address'),
      '#description' => $this->t('Email address shown in the "From" field.'),
      // ' // '#size'' => 50,
      '#maxlength' => 255,
      '#required' => TRUE,
      '#default_value' => $config->get('kicad_from_email') ?? '',
    ];

    $form['extensions'] = [
      '#type' => 'details',
      '#title' => $this->t('Allowed file extensions'),
      '#open' => TRUE,
    ];

    $form['extensions']['pspicefile'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Allowed PSPICE file extensions'),
      '#description' => $this->t('Comma separated list WITHOUT spaces.'),
      // ' // '#size'' => 50,
      '#maxlength' => 255,
      '#required' => TRUE,
      '#default_value' => $config->get('pspice_files_extensions') ?? '',
    ];

    $form['extensions']['pdfpspicefile'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Allowed PDF file extensions'),
      '#description' => $this->t('Comma separated list WITHOUT spaces.'),
      // ' // '#size'' => 50,
      '#maxlength' => 255,
      '#required' => TRUE,
      '#default_value' => $config->get('pdfpspicefile_files_extensions') ?? '',
    ];

    $form['extensions']['kicadcorrectedfile'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Allowed KiCad corrected file extensions'),
      '#description' => $this->t('Comma separated list WITHOUT spaces.'),
      // ' // '#size'' => 50,
      '#maxlength' => 255,
      '#required' => TRUE,
      '#default_value' => $config->get('kicad_corrected_files_extensions') ?? '',
    ];

    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Submit'),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    // Optional: add email/extension validation here.
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {

    $this->configFactory()->getEditable('pspicefile.settings')
      ->set('kicad_emails', $form_state->getValue('emails'))
      ->set('kicad_cc_emails', $form_state->getValue('cc_emails'))
      ->set('kicad_from_email', $form_state->getValue('from_email'))
      ->set('pspice_files_extensions', $form_state->getValue(['extensions', 'pspicefile']))
      ->set('pdfpspicefile_files_extensions', $form_state->getValue(['extensions', 'pdfpspicefile']))
      ->set('kicad_corrected_files_extensions', $form_state->getValue(['extensions', 'kicadcorrectedfile']))
      ->save();

    $this->messenger()->addStatus($this->t('Settings updated.'));
  }

}
