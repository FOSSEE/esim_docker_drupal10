<?php
/**
 * @file
 * Drupal 7 form to delete PSpice/KiCad files.
 */

/**
 * Form builder.
 */
function pspice_to_kicad_delete_file_form($form, &$form_state, $fileid) {

  // Check record exists.
  $exists = \Drupal::database()->select('custom_kicad_convertor', 'c')
    ->fields('c', array('id'))
    ->condition('id', $fileid)
    ->execute()
    ->rowCount();

  if (!$exists) {
    \Drupal::messenger()->addError(t('Invalid file ID.'));
    return array();
  }

  $form['pspice_deletefile_fieldset'] = array(
    '#type' => 'fieldset',
    '#tree' => TRUE,
    '#prefix' => '<div id="pspice-delete-fieldset-wrapper">',
    '#suffix' => '</div>',
  );

  $form['pspice_deletefile_fieldset']['reason'] = array(
    '#type' => 'textarea',
    '#title' => t('Reason for deletion of file'),
    '#required' => TRUE,
    '#description' => t('Brief reason for deleting the file.'),
  );

  $form['pspice_files_id'] = array(
    '#type' => 'hidden',
    '#value' => $fileid,
  );

  $form['submit'] = array(
    '#type' => 'submit',
    '#value' => t('Submit'),
    '#attributes' => array(
      'onclick' => 'return confirm("Do you really want to delete this file?");',
    ),
  );

  return $form;
}

/**
 * Form validation handler.
 */
function pspice_to_kicad_delete_file_form_validate($form, &$form_state) {

  $reason = trim($form_state['values']['pspice_deletefile_fieldset']['reason']);
  if (strlen($reason) < 5) {
    form_set_error('reason', t('Please provide a valid reason for deletion.'));
  }
}

/**
 * Form submit handler.
 */
function pspice_to_kicad_delete_file_form_submit($form, &$form_state) {

  $user = \Drupal::currentUser();

  $values = $form_state['values'];
  $id = $values['pspice_files_id'];

  // Load DB row.
  $row = \Drupal::database()->select('custom_kicad_convertor', 'c')
    ->fields('c')
    ->condition('id', $id)
    ->execute()
    ->fetchObject();

  if (!$row) {
    \Drupal::messenger()->addError(t('File record not found.'));
    return;
  }

  $upload_root_path = pspice_to_kicad_directroy_path();

  // Delete uploaded schematic.
  if (!empty($row->upload_filepath) && file_exists($upload_root_path . '/' . $row->upload_filepath)) {
    unlink($upload_root_path . '/' . $row->upload_filepath);
  }

  // Delete uploaded PDF.
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
  \Drupal::database()->delete('custom_kicad_convertor')
    ->condition('id', $id)
    ->execute();

  // Send email to file owner.
  $user_data = \Drupal::entityTypeManager()->getStorage('user')->load($row->uid);

  if ($user_data && !empty($user_data->mail)) {

    $to = $user_data->mail;
    // @FIXME
// // @FIXME
// // This looks like another module's variable. You'll need to rewrite this call
// // to ensure that it uses the correct configuration object.
// $from = variable_get('kicad_from_email', '');

    // @FIXME
// // @FIXME
// // This looks like another module's variable. You'll need to rewrite this call
// // to ensure that it uses the correct configuration object.
// $bcc = variable_get('kicad_emails', '');

    // @FIXME
// // @FIXME
// // This looks like another module's variable. You'll need to rewrite this call
// // to ensure that it uses the correct configuration object.
// $cc = variable_get('kicad_cc_emails', '');


    $subject = '[esim.in][PSpice to KiCad Converter] Uploaded file deleted';

    // @FIXME
// // @FIXME
// // This looks like another module's variable. You'll need to rewrite this call
// // to ensure that it uses the correct configuration object.
// $email_body = "
// Dear {$user_data->name},
// 
// Your uploaded schematic/converted file has been deleted.
// 
// Schematic name: {$row->upload_filename}
// Description: {$row->description}
// Reason for deletion: " . trim($values['pspice_deletefile_fieldset']['reason']) . "
// 
// You may upload a new PSpice schematic for conversion.
// 
// Best Wishes,\n" . variable_get('site_name', '') . " Team\nFOSSEE, IIT Bombay";


    send_mail($from, $to, $subject, $email_body, $from, $bcc, $cc);
  }

  \Drupal::messenger()->addStatus(t('File deleted successfully and notification email sent.'));
  drupal_goto('pspice-to-kicad/convert');
}
