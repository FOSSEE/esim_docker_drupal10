<?php

namespace Drupal\pspice_to_kicad\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Database\Database;
use Drupal\file\Entity\File;
use Drupal\Core\Url;
use Symfony\Component\HttpFoundation\RedirectResponse;

class PspiceToKicadUploadForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'pspice_to_kicad_upload_form';
  }

  /**
   * Form builder.
   */
  public function buildForm(array $form, FormStateInterface $form_state, $fileid = NULL) {

    // Load existing record.
    $row = Database::getConnection()
      ->select('custom_kicad_convertor', 'c')
      ->fields('c')
      ->condition('id', $fileid)
      ->execute()
      ->fetchObject();

    $form['pspice_uploadfiles_fieldset'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Upload corrected KiCad file'),
    ];

    $form['pspice_uploadfiles_fieldset']['file_description'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Description'),
      '#default_value' => $row->description ?? '',
      '#required' => TRUE,
      '#description' => $this->t('Brief description about file usage and details (minimum 50 characters).'),
    ];

    $form['pspice_uploadfiles_fieldset']['file'] = [
      '#type' => 'managed_file',
      '#title' => $this->t('Upload File'),
      '#upload_location' => 'temporary://pspice_kicad/',
      '#required' => TRUE,
      '#upload_validators' => [
        'file_validate_extensions' => ['zip'],
      ],
      '#description' => $this->t('Give an appropriate name to the file before uploading.'),
    ];

    $form['pspice_files_id'] = [
      '#type' => 'hidden',
      '#value' => $fileid,
    ];

    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Submit'),
    ];

    return $form;
  }

  /**
   * Validation handler.
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {

    $description = $form_state->getValue(['pspice_uploadfiles_fieldset', 'file_description']);
    if (strlen(trim($description)) < 50) {
      $form_state->setErrorByName(
        'pspice_uploadfiles_fieldset][file_description',
        $this->t('Description must be at least 50 characters long.')
      );
    }

    $fids = $form_state->getValue(['pspice_uploadfiles_fieldset', 'file']);
    if (empty($fids)) {
      $form_state->setErrorByName(
        'pspice_uploadfiles_fieldset][file',
        $this->t('Please upload the corrected file.')
      );
    }
  }

  /**
   * Submit handler.
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {

    $current_user = $this->currentUser();
    $uid = $current_user->id();
    $id = $form_state->getValue('pspice_files_id');

    $fids = $form_state->getValue(['pspice_uploadfiles_fieldset', 'file']);
    $file = File::load(reset($fids));

    if (!$file) {
      $this->messenger()->addError($this->t('File upload failed.'));
      return;
    }

    // Make file permanent.
    $file->setPermanent();
    $file->save();

    // Prepare directories (your existing helper functions).
    get_directory($uid, $id);
    $converted_rel_path = get_directory_path($uid, $id, 4);
    $converted_abs_path = get_directory_path($uid, $id, 2);

    $filename = $file->getFilename();
    $destination = $converted_abs_path . '/' . $filename;

    // Move file.
    file_unmanaged_copy(
      $file->getFileUri(),
      $destination,
      FILE_EXISTS_REPLACE
    );

    // Update database.
    Database::getConnection()
      ->update('custom_kicad_convertor')
      ->fields([
        'description' => trim($form_state->getValue(['pspice_uploadfiles_fieldset', 'file_description'])),
        'converted_filename' => $filename,
        'converted_filepath' => $converted_rel_path . '/' . $filename,
        'converted_filemime' => $file->getMimeType(),
        'converted_filesize' => $file->getSize(),
        'converted_date' => date('Y-m-d H:i:s'),
        'converted_flag' => 1,
      ])
      ->condition('id', $id)
      ->execute();

    $this->messenger()->addStatus(
      $this->t('@file uploaded successfully.', ['@file' => $filename])
    );

    // Redirect.
    $response = new RedirectResponse(Url::fromUserInput('/pspice-to-kicad/convert')->toString());
    $response->send();
  }

}
