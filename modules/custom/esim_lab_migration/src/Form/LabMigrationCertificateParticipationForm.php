<?php

/**
 * @file
 * Contains \Drupal\lab_migration\Form\LabMigrationCertificateParticipationForm.
 */

namespace Drupal\lab_migration\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\Element;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Drupal\Core\Url;

class LabMigrationCertificateParticipationForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'lab_migration_certificate_participation_form';
  }

  public function buildForm(array $form, \Drupal\Core\Form\FormStateInterface $form_state) {
    $form['name_title'] = [
      '#type' => 'select',
      '#title' => t('Title'),
      '#options' => [
        'Dr.' => 'Dr.',
        'Prof.' => 'Prof.',
        'Mr.' => 'Mr.',
        'Mrs.' => 'Mrs.',
        'Ms.' => 'Ms.',
      ],
      '#required' => TRUE,
    ];
    $form['name'] = [
      '#type' => 'textfield',
      '#title' => t('Name of Participant'),
      '#maxlength' => 50,
      '#required' => TRUE,
    ];
    $form['email_id'] = [
      '#type' => 'textfield',
      '#title' => t('Email'),
      // '#size' => 50,
      '#default_value' => 'Not availbale',
    ];
    $form['institute_name'] = [
      '#type' => 'textfield',
      '#title' => t('Collage / Institue Name'),
      '#required' => TRUE,
    ];
    $form['institute_address'] = [
      '#type' => 'textfield',
      '#title' => t('Collage / Institue address'),
      '#required' => TRUE,
    ];
    $form['lab_name'] = [
      '#type' => 'textfield',
      '#title' => t('Lab name'),
      '#required' => TRUE,
    ];
    $form['department'] = [
      '#type' => 'textfield',
      '#title' => t('Department'),
      '#required' => TRUE,
    ];
    $form['proposal_id'] = [
      '#type' => 'textfield',
      '#title' => t('Lab Proposal Id'),
      '#description' => 'Note: You can find  the respective Lab Proposal Id from the url for the  completed lab. For example: The Lab Proposal Id is 64 for this completed lab. ( Url - scilab.in/lab_migration_run/64)',
      '#required' => TRUE,
    ];
    $form['submit'] = [
      '#type' => 'submit',
      '#value' => t('Submit'),
    ];
    return $form;
  }


public function submitForm(array &$form, FormStateInterface $form_state) {

  $user = \Drupal::currentUser();
  $values = $form_state->getValues();

  // ✅ Insert using Drupal 10 DB API
  \Drupal::database()->insert('lab_migration_certificate')
    ->fields([
      'uid' => $user->id(), // ✅ FIXED
      'name_title' => trim($values['name_title']),
      'name' => trim($values['name']),
      'email_id' => trim($values['email_id']),
      'institute_name' => trim($values['institute_name']),
      'institute_address' => trim($values['institute_address']),
      'lab_name' => trim($values['lab_name']),
      'department' => trim($values['department']),
      'proposal_id' => (int) $values['proposal_id'], // ✅ ensure integer
      'creation_date' => time(),
    ])
    ->execute();

  // ✅ Redirect (Drupal 10 way)
  $form_state->setRedirectUrl(
    Url::fromUri('internal:/lab-migration/certificate')
  );
}
}
?>
