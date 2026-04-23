<?php
/**
 * @file
 * Drupal 7 form for uploading corrected (converted) KiCad files.
 */

/**
 * Form builder.
 */
function pspice_to_kicad_upload_form($form, &$form_state, $fileid) {

  // Load existing record.
  $row = \Drupal::database()->select('custom_kicad_convertor', 'c')
    ->fields('c')
    ->condition('id', $fileid)
    ->execute()
    ->fetchObject();

  $form['pspice_uploadfiles_fieldset'] = array(
    '#type' => 'fieldset',
    '#tree' => TRUE,
    '#prefix' => '<div id="pspice-files-fieldset-wrapper">',
    '#suffix' => '</div>',
  );

  $form['pspice_uploadfiles_fieldset']['file_description'] = array(
    '#type' => 'textarea',
    '#title' => t('Description'),
    '#default_value' => isset($row->description) ? $row->description : '',
    '#required' => TRUE,
    '#description' => t('Brief description about file usage and details (minimum 50 characters).'),
  );

  $form['pspice_uploadfiles_fieldset']['name'] = array(
    '#type' => 'file',
    '#title' => t('Upload File'),
    '#description' => t('Give an appropriate name to the file before uploading.'),
    '#required' => TRUE,
  );

  $form['pspice_files_id'] = array(
    '#type' => 'hidden',
    '#value' => $fileid,
  );

  $form['submit'] = array(
    '#type' => 'submit',
    '#value' => t('Submit'),
  );

  return $form;
}

/**
 * Form validation handler.
 */
function pspice_to_kicad_upload_form_validate($form, &$form_state) {

  if (!empty($_FILES['files']['name'])) {

    foreach ($_FILES['files']['name'] as $file_form_name => $file_name) {
      if ($file_name) {

        // Allowed extensions from variable.
        // @FIXME
// // @FIXME
// // This looks like another module's variable. You'll need to rewrite this call
// // to ensure that it uses the correct configuration object.
// $allowed_extensions_str = variable_get('kicad_corrected_files_extensions', 'zip');

        $allowed_extensions = array_map('trim', explode(',', $allowed_extensions_str));

        $ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

        if (!in_array($ext, $allowed_extensions)) {
          form_set_error($file_form_name, t('Only files with %ext extensions are allowed.', array('%ext' => $allowed_extensions_str)));
        }

        if ($_FILES['files']['size'][$file_form_name] == 0) {
          form_set_error($file_form_name, t('File with 0 byte size cannot be uploaded.'));
        }

        if (!pspice_to_kicad_check_valid_filename($file_name)) {
          form_set_error($file_form_name, t('Invalid file name specified. Spaces are not allowed in file names.'));
        }
      }
    }
  }
}

/**
 * Form submit handler.
 */
function pspice_to_kicad_upload_form_submit($form, &$form_state) {

  $user = \Drupal::currentUser();

  $values = $form_state['values'];
  $id = $values['pspice_files_id'];

  // Validate file presence.
  $uploaded_names = array_values($_FILES['files']['name']);
  if (empty($uploaded_names[0])) {
    \Drupal::messenger()->addError(t('Please upload the corrected file.'));
    return;
  }

  // Load DB row.
  $row = \Drupal::database()->select('custom_kicad_convertor', 'c')
    ->fields('c')
    ->condition('id', $id)
    ->execute()
    ->fetchObject();

  // Prepare directories.
  get_directory($user->uid, $id);
  $converted_rel_path = get_directory_path($user->uid, $id, 4);
  $converted_abs_path = get_directory_path($user->uid, $id, 2);

  foreach ($_FILES['files']['name'] as $file_form_name => $file_name) {

    if (!$file_name) {
      continue;
    }

    $destination = $converted_abs_path . '/' . $file_name;

    // Replace existing file if present.
    if (file_exists($destination)) {
      unlink($destination);
    }

    if (move_uploaded_file($_FILES['files']['tmp_name'][$file_form_name], $destination)) {

      \Drupal::database()->update('custom_kicad_convertor')
        ->fields(array(
          'description' => trim($values['pspice_uploadfiles_fieldset']['file_description']),
          'converted_filename' => $file_name,
          'converted_filepath' => $converted_rel_path . '/' . $file_name,
          'converted_filemime' => file_get_mimetype($file_name),
          'converted_filesize' => $_FILES['files']['size'][$file_form_name],
          'converted_date' => date('Y-m-d H:i:s'),
          'converted_flag' => 1,
        ))
        ->condition('id', $id)
        ->execute();

      \Drupal::messenger()->addStatus(t('@file uploaded successfully.', array('@file' => $file_name)));
    }
    else {
      \Drupal::messenger()->addError(t('Error uploading file @file.', array('@file' => $file_name)));
    }
  }

  drupal_goto('pspice-to-kicad/convert');
}
