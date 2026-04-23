<?php

/**
 * @file
 * Contains \Drupal\pspice_to_kicad\Form\PspiceToKicadAddForm.
 */

namespace Drupal\pspice_to_kicad\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\Element;

class PspiceToKicadAddForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'pspice_to_kicad_add_form';
  }

  public function buildForm(array $form, \Drupal\Core\Form\FormStateInterface $form_state) {

    $form['codefilename'] = [
      '#type' => 'file',
      '#title' => t('PSpice File'),
      '#description' => t('<span style="color:red">Note: Give relevant name before uploading. Only .sch files allowed.</span>'),
      '#required' => TRUE,
    ];

    $form['description'] = [
      '#type' => 'textarea',
      '#title' => t('Description'),
      '#required' => TRUE,
      '#description' => t('<span style="color:red">Minimum 50 and maximum 200 characters</span>'),
    ];

    $form['pdffilename'] = [
      '#type' => 'file',
      '#title' => t('PDF File'),
      '#description' => t('<span style="color:red">Optional supplementary PDF document</span>'),
    ];

    $form['termsandcondition'] = [
      '#type' => 'checkbox',
      '#title' => t('I agree to the Terms and Conditions'),
      '#required' => TRUE,
    ];

    $form['terms_text'] = [
      '#type' => 'markup',
      '#markup' => t('<ul>
      <li>I confirm the uploaded PSpice file is created by me.</li>
      <li>I temporarily transfer ownership for conversion.</li>
      <li>I agree to release converted files under CC license.</li>
    </ul>'),
    ];

    $form['submit'] = [
      '#type' => 'submit',
      '#value' => t('Submit'),
    ];

    return $form;
  }

  public function validateForm(array &$form, \Drupal\Core\Form\FormStateInterface $form_state) {

    $user = \Drupal::currentUser();

    if (!$user->uid) {
      $form_state->setErrorByName('codefilename', t('You must be logged in to upload files.'));
    }

    $description = trim($form_state->getValue(['description']));
    if (strlen($description) < 50 || strlen($description) > 200) {
      $form_state->setErrorByName('description', t('Description must be between 50 and 200 characters.'));
    }

    if (!empty($_FILES['files']['name']['codefilename'])) {
      $ext = pathinfo($_FILES['files']['name']['codefilename'], PATHINFO_EXTENSION);
      if (!in_array(strtolower($ext), ['sch'])) {
        $form_state->setErrorByName('codefilename', t('Only .sch files are allowed.'));
      }
    }

    if (!empty($_FILES['files']['name']['pdffilename'])) {
      $ext = pathinfo($_FILES['files']['name']['pdffilename'], PATHINFO_EXTENSION);
      if (strtolower($ext) != 'pdf') {
        $form_state->setErrorByName('pdffilename', t('Only PDF files are allowed.'));
      }
    }
  }

  public function submitForm(array &$form, \Drupal\Core\Form\FormStateInterface $form_state) {

    $user = \Drupal::currentUser();

    $description = trim($form_state->getValue(['description']));
    $upload_dir = 'private://pspice_uploads';
    file_prepare_directory($upload_dir, FILE_CREATE_DIRECTORY | FILE_MODIFY_PERMISSIONS);

    // Insert initial DB record.
    $id = \Drupal::database()->insert('custom_kicad_convertor')
      ->fields([
      'uid' => $user->uid,
      'caption' => 'None',
      'description' => $description,
      'upload_date' => date('Y-m-d H:i:s'),
      'converted_flag' => 0,
      'download_counter' => 0,
    ])
      ->execute();

    // Handle PSpice file.
    if (!empty($_FILES['files']['name']['codefilename'])) {
      $file = file_save_upload('codefilename', [
        'file_validate_extensions' => [
          'sch'
          ]
        ], $upload_dir);
      if ($file) {
        $file->status = FILE_STATUS_PERMANENT;
        file_save($file);

        \Drupal::database()->update('custom_kicad_convertor')
          ->fields([
          'upload_filename' => $file->filename,
          'upload_filepath' => $file->uri,
          'upload_filemime' => $file->filemime,
          'upload_filesize' => $file->filesize,
        ])
          ->condition('id', $id)
          ->execute();
      }
    }

    // Handle PDF file.
    if (!empty($_FILES['files']['name']['pdffilename'])) {
      $pdf = file_save_upload('pdffilename', [
        'file_validate_extensions' => [
          'pdf'
          ]
        ], $upload_dir);
      if ($pdf) {
        $pdf->status = FILE_STATUS_PERMANENT;
        file_save($pdf);

        \Drupal::database()->update('custom_kicad_convertor')
          ->fields(['description_pdf_path' => $pdf->uri])
          ->condition('id', $id)
          ->execute();
      }
    }

    \Drupal::messenger()->addMessage(t('File uploaded successfully. You will be notified after conversion.'));
  }

}
?>
