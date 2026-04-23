<?php
/**
 * @file
 * Drupal 7 form for PSpice to KiCad upload.
 */

/**
 * Form builder.
 */
function pspice_to_kicad_add_form($form, &$form_state) {

  $form['codefilename'] = array(
    '#type' => 'file',
    '#title' => t('PSpice File'),
    '#description' => t('<span style="color:red">Note: Give relevant name before uploading. Only .sch files allowed.</span>'),
    '#required' => TRUE,
  );

  $form['description'] = array(
    '#type' => 'textarea',
    '#title' => t('Description'),
    '#required' => TRUE,
    '#description' => t('<span style="color:red">Minimum 50 and maximum 200 characters</span>'),
  );

  $form['pdffilename'] = array(
    '#type' => 'file',
    '#title' => t('PDF File'),
    '#description' => t('<span style="color:red">Optional supplementary PDF document</span>'),
  );

  $form['termsandcondition'] = array(
    '#type' => 'checkbox',
    '#title' => t('I agree to the Terms and Conditions'),
    '#required' => TRUE,
  );

  $form['terms_text'] = array(
    '#type' => 'markup',
    '#markup' => t('<ul>
      <li>I confirm the uploaded PSpice file is created by me.</li>
      <li>I temporarily transfer ownership for conversion.</li>
      <li>I agree to release converted files under CC license.</li>
    </ul>'),
  );

  $form['submit'] = array(
    '#type' => 'submit',
    '#value' => t('Submit'),
  );

  return $form;
}

/**
 * Form validation.
 */
function pspice_to_kicad_add_form_validate($form, &$form_state) {

  $user = \Drupal::currentUser();

  if (!$user->uid) {
    form_set_error('codefilename', t('You must be logged in to upload files.'));
  }

  $description = trim($form_state['values']['description']);
  if (strlen($description) < 50 || strlen($description) > 200) {
    form_set_error('description', t('Description must be between 50 and 200 characters.'));
  }

  if (!empty($_FILES['files']['name']['codefilename'])) {
    $ext = pathinfo($_FILES['files']['name']['codefilename'], PATHINFO_EXTENSION);
    if (!in_array(strtolower($ext), array('sch'))) {
      form_set_error('codefilename', t('Only .sch files are allowed.'));
    }
  }

  if (!empty($_FILES['files']['name']['pdffilename'])) {
    $ext = pathinfo($_FILES['files']['name']['pdffilename'], PATHINFO_EXTENSION);
    if (strtolower($ext) != 'pdf') {
      form_set_error('pdffilename', t('Only PDF files are allowed.'));
    }
  }
}

/**
 * Form submit handler.
 */
function pspice_to_kicad_add_form_submit($form, &$form_state) {

  $user = \Drupal::currentUser();

  $description = trim($form_state['values']['description']);
  $upload_dir = 'private://pspice_uploads';
  file_prepare_directory($upload_dir, FILE_CREATE_DIRECTORY | FILE_MODIFY_PERMISSIONS);

  // Insert initial DB record.
  $id = \Drupal::database()->insert('custom_kicad_convertor')
    ->fields(array(
      'uid' => $user->uid,
      'caption' => 'None',
      'description' => $description,
      'upload_date' => date('Y-m-d H:i:s'),
      'converted_flag' => 0,
      'download_counter' => 0,
    ))
    ->execute();

  // Handle PSpice file.
  if (!empty($_FILES['files']['name']['codefilename'])) {
    $file = file_save_upload('codefilename', array('file_validate_extensions' => array('sch')), $upload_dir);
    if ($file) {
      $file->status = FILE_STATUS_PERMANENT;
      file_save($file);

      \Drupal::database()->update('custom_kicad_convertor')
        ->fields(array(
          'upload_filename' => $file->filename,
          'upload_filepath' => $file->uri,
          'upload_filemime' => $file->filemime,
          'upload_filesize' => $file->filesize,
        ))
        ->condition('id', $id)
        ->execute();
    }
  }

  // Handle PDF file.
  if (!empty($_FILES['files']['name']['pdffilename'])) {
    $pdf = file_save_upload('pdffilename', array('file_validate_extensions' => array('pdf')), $upload_dir);
    if ($pdf) {
      $pdf->status = FILE_STATUS_PERMANENT;
      file_save($pdf);

      \Drupal::database()->update('custom_kicad_convertor')
        ->fields(array('description_pdf_path' => $pdf->uri))
        ->condition('id', $id)
        ->execute();
    }
  }

  \Drupal::messenger()->addMessage(t('File uploaded successfully. You will be notified after conversion.'));
}
