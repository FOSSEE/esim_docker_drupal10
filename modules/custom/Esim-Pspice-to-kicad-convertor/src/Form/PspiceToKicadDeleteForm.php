<?php

namespace Drupal\pspice_to_kicad\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Database\Database;
use Drupal\Core\Messenger\MessengerTrait;
use Drupal\Core\Url;

class PspiceToKicadDeleteForm extends FormBase {

  use MessengerTrait;

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'pspice_to_kicad_delete_form';
  }

  /**
   * Build form.
   */
  public function buildForm(array $form, FormStateInterface $form_state, $id = NULL) {

    // Validate file exists.
    $exists = Database::getConnection()
      ->select('custom_kicad_convertor', 'c')
      ->fields('c', ['id'])
      ->condition('id', $id)
      ->execute()
      ->fetchField();

    if (!$exists) {
      $this->messenger()->addError($this->t('Invalid file ID.'));
      return [];
    }

    $form['pspice_deletefile_fieldset'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Delete file'),
    ];

    $form['pspice_deletefile_fieldset']['reason'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Reason for deletion of file'),
      '#required' => TRUE,
      '#description' => $this->t('Brief reason for deleting the file.'),
    ];

    $form['file_id'] = [
      '#type' => 'hidden',
      '#value' => $id,
    ];

    $form['actions'] = [
      '#type' => 'actions',
    ];

    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Delete'),
      '#attributes' => [
        'onclick' => 'return confirm("Do you really want to delete this file?");',
      ],
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    $reason = trim($form_state->getValue(['pspice_deletefile_fieldset', 'reason']));
    if (strlen($reason) < 5) {
      $form_state->setErrorByName(
        'pspice_deletefile_fieldset][reason',
        $this->t('Please provide a valid reason for deletion.')
      );
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {

    $id = $form_state->getValue('file_id');

    $connection = Database::getConnection();

    $row = $connection->select('custom_kicad_convertor', 'c')
      ->fields('c')
      ->condition('id', $id)
      ->execute()
      ->fetchObject();

    if (!$row) {
      $this->messenger()->addError($this->t('File record not found.'));
      return;
    }

    $upload_root_path = pspice_to_kicad_directroy_path();

    // Delete uploaded schematic.
    if (!empty($row->upload_filepath) && file_exists($upload_root_path . '/' . $row->upload_filepath)) {
      unlink($upload_root_path . '/' . $row->upload_filepath);
    }

    // Delete description PDF.
    if (!empty($row->description_pdf_path) && file_exists($upload_root_path . '/' . $row->description_pdf_path)) {
      unlink($upload_root_path . '/' . $row->description_pdf_path);
    }

    // Delete converted file.
    if (!empty($row->converted_filepath) && file_exists($upload_root_path . '/' . $row->converted_filepath)) {
      unlink($upload_root_path . '/' . $row->converted_filepath);
    }

    // Remove directories (deepest first).
    @rmdir(get_directory_path($row->uid, $id, 1));
    @rmdir(get_directory_path($row->uid, $id, 2));
    @rmdir($upload_root_path . '/' . $row->uid . '/' . $id);

    // Delete DB record.
    $connection->delete('custom_kicad_convertor')
      ->condition('id', $id)
      ->execute();

    // Send notification email (optional – hook up MailManager properly later).
    $user = \Drupal::entityTypeManager()
      ->getStorage('user')
      ->load($row->uid);

    if ($user && $user->getEmail()) {
      // Replace this with MailManagerInterface later.
      // mail sending intentionally left as-is placeholder.
    }

    $this->messenger()->addStatus($this->t('File deleted successfully.'));

    // Redirect.
    $form_state->setRedirectUrl(Url::fromRoute('pspice_to_kicad.convert_list'));
  }

}
