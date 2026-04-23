<?php

/**
 * @file
 * Contains \Drupal\hackathon_submission\Form\EditProjectFilesForm.
 */

namespace Drupal\hackathon_submission\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\Element;

class EditProjectFilesForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'edit_project_files_form';
  }

  public function buildForm(array $form, \Drupal\Core\Form\FormStateInterface $form_state, $no_js_use = NULL) {
    $user = \Drupal::currentUser();
    $literature_survey_id = arg(3);
    if ($user->uid == 0) {
      $msg = \Drupal::messenger()->addError(t('It is mandatory to log in on this website to edit your submission. If you are new user please create a new account first.'));
      //drupal_goto('esim-circuit-simulation-project');
      drupal_goto('user/login', [
        'query' => drupal_get_destination()
        ]);
      return $msg;
    }
    $query = \Drupal::database()->select('hackathon_literature_survey');
    $query->fields('hackathon_literature_survey');
    $query->condition('uid', $user->uid);
    $query->condition('id', $literature_survey_id);
    //$query->range(0, 1);
    $literature_submission_q = $query->execute();
    $literature_submission_data = $literature_submission_q->fetchObject();
    $query = \Drupal::database()->select('hackathon_final_submission_project_files');
    $query->fields('hackathon_final_submission_project_files');
    $query->condition('literature_submission_id', $literature_survey_id);
    $project_files_data = $query->execute()->fetchObject();
    if (!$project_files_data) {
      \Drupal::messenger()->addMessage('We have not yet received your project files');
      drupal_goto('hackathon-submission/add/project-files');
    }

    $form = [];
    $form['participant_name'] = [
      '#title' => t('Participant Name'),
      '#type' => 'textfield',
      '#disabled' => TRUE,
      // '#size' => 70,
      '#maxlength' => 70,
      '#default_value' => $literature_submission_data->participant_name,
    ];
    $form['circuit_name'] = [
      '#title' => t('Circuit Name'),
      '#type' => 'textfield',
      // '#size' => 70,
      '#maxlength' => 70,
      '#disabled' => TRUE,
      '#default_value' => $literature_submission_data->circuit_name,
    ];
    $form['edit_source_file'] = [
      '#type' => 'fieldset',
      '#title' => t('Edit Main Netlist file'),
      '#collapsible' => FALSE,
      '#collapsed' => FALSE,
    ];
    $existing_uploaded_N_file = default_value_for_uploaded_project_files("N", $literature_survey_id);
    if (!$existing_uploaded_N_file) {
      $existing_uploaded_N_file = new stdClass();
      $existing_uploaded_N_file->filename = "No file uploaded";
    }
    // @FIXME
    // // @FIXME
    // // This looks like another module's variable. You'll need to rewrite this call
    // // to ensure that it uses the correct configuration object.
    // $form['edit_source_file']['main_netlist'] = array(
    //         '#type' => 'file',
    //         //'#title' => t('Upload circuit diagram'),
    //         '#size' => 48,
    //         '#description' => t('Upload filenames with allowed extensions only. No spaces or any special characters allowed in filename.') . '<br />' . t('<span style="color:red;">Current File :</span> ') . $existing_uploaded_N_file->filename . '<br />' . t('<span style="color:red;">Allowed file extensions: ') . variable_get('main_netlist_file_extensions', '') . '</span>'
    //     );

    $form['edit_final_project_files'] = [
      '#type' => 'fieldset',
      '#title' => t('Edit Subcircuit File'),
      '#collapsible' => FALSE,
      '#collapsed' => FALSE,
    ];
    $existing_uploaded_S_file = default_value_for_uploaded_project_files("S", $literature_survey_id);
    if (!$existing_uploaded_S_file) {
      $existing_uploaded_S_file = new stdClass();
      $existing_uploaded_S_file->filename = "No file uploaded";
    }
    // @FIXME
    // // @FIXME
    // // This looks like another module's variable. You'll need to rewrite this call
    // // to ensure that it uses the correct configuration object.
    // $form['edit_final_project_files']['subcircuit_files'] = array(
    //         '#type' => 'file',
    //         //'#title' => t('Edit Subcircuit file'),
    //         '#size' => 48,
    //         '#description' => t('Upload filenames with allowed extensions only. No spaces or any special characters allowed in filename.') . '<br />' . t('<span style="color:red;">Current File :</span> ') . $existing_uploaded_S_file->filename . '<br />' . t('<span style="color:red;">Allowed file extensions: ') . variable_get('subcircuit_file_extensions', '') . '</span>'
    //     );

    $form['edit_project_files'] = [
      '#type' => 'fieldset',
      '#title' => t('Edit Readme file'),
      '#collapsible' => FALSE,
      '#collapsed' => FALSE,
    ];
    $existing_uploaded_R_file = default_value_for_uploaded_project_files("R", $literature_survey_id);
    if (!$existing_uploaded_R_file) {
      $existing_uploaded_R_file = new stdClass();
      $existing_uploaded_R_file->filename = "No file uploaded";
    }
    // @FIXME
    // // @FIXME
    // // This looks like another module's variable. You'll need to rewrite this call
    // // to ensure that it uses the correct configuration object.
    // $form['edit_project_files']['readme'] = array(
    //         '#type' => 'file',
    //         //'#title' => t('Upload circuit diagram'),
    //         '#size' => 48,
    //         '#description' => t('Upload filenames with allowed extensions only. No spaces or any special characters allowed in filename.') . '<br />' . t('<span style="color:red;">Current File :</span> ') . $existing_uploaded_R_file->filename . '<br />' . t('<span style="color:red;">Allowed file extensions: ') . variable_get('readme_file_extensions', '') . '</span>'
    //     );

    $form["submit"] = [
      "#type" => "submit",
      "#value" => "Submit",
    ];
    return $form;
  }

  public function validateForm(array &$form, \Drupal\Core\Form\FormStateInterface $form_state) {
    if (isset($_FILES['files'])) {
      /* check for valid filename extensions */
      foreach ($_FILES['files']['name'] as $file_form_name => $file_name) {
        if ($file_name) {
          /* checking file type */
          if (strstr($file_form_name, 'main_netlist')) {
            $file_type = 'N';
            $fnames = substr($_FILES['files']['name'][$file_form_name], strpos($_FILES['files']['name'][$file_form_name], ".") + 1);
            $temp_extension = $fnames;
          }
          else {
            if (strstr($file_form_name, 'readme')) {
              $file_type = 'R';
              $fnames = explode('.', strtolower($_FILES['files']['name'][$file_form_name]));
              $temp_extension = end($fnames);
            }
            else {
              if (strstr($file_form_name, 'subcircuit_files')) {
                $file_type = 'S';
                $fnames = explode('.', strtolower($_FILES['files']['name'][$file_form_name]));
                $temp_extension = end($fnames);
              }
            }
          }
          $allowed_extensions_str = '';
          switch ($file_type) {
            case 'N':
              // @FIXME
              // // @FIXME
              // // This looks like another module's variable. You'll need to rewrite this call
              // // to ensure that it uses the correct configuration object.
              // $allowed_extensions_str = variable_get('main_netlist_file_extensions', '');

              break;
            case 'S':
              // @FIXME
              // // @FIXME
              // // This looks like another module's variable. You'll need to rewrite this call
              // // to ensure that it uses the correct configuration object.
              // $allowed_extensions_str = variable_get('subcircuit_file_extensions', '');

              break;
            case 'R':
              // @FIXME
              // // @FIXME
              // // This looks like another module's variable. You'll need to rewrite this call
              // // to ensure that it uses the correct configuration object.
              // $allowed_extensions_str = variable_get('readme_file_extensions', '');

              break;
          }
          $allowed_extensions = explode(',', $allowed_extensions_str);
          //$fnames = substr($_FILES['files']['name'][$file_form_name], strpos($_FILES['files']['name'][$file_form_name], ".") + 1);
          //$temp_extension = end($fnames);
          if (!in_array($temp_extension, $allowed_extensions)) {
            $form_state->setErrorByName($file_form_name, t('Only file with ' . $allowed_extensions_str . ' extensions can be uploaded.'));
          }
          if ($_FILES['files']['size'][$file_form_name] <= 0) {
            $form_state->setErrorByName($file_form_name, t('File size cannot be zero.'));
          }
          /* check if valid file name */
          if (!hackathon_submission_check_valid_filename($_FILES['files']['name'][$file_form_name])) {
            $form_state->setErrorByName($file_form_name, t('Invalid file name specified. Only alphabets and numbers are allowed as a valid filename.'));
          }
        } //$file_name
      } //$_FILES['files']['name'] as $file_form_name => $file_name
    }
    return $form_state;
  }

  public function submitForm(array &$form, \Drupal\Core\Form\FormStateInterface $form_state) {
    $user = \Drupal::currentUser();
    $v = $form_state->getValues();
    $root_path = hackathon_submission_files_path();
    $literature_survey_id = arg(3);
    $query = \Drupal::database()->select('hackathon_literature_survey');
    $query->fields('hackathon_literature_survey');
    $query->condition('uid', $user->uid);
    $query->condition('id', $literature_survey_id);
    //$query->range(0, 1);
    $literature_submission_q = $query->execute();
    $literature_submission_data = $literature_submission_q->fetchObject();
    //$proposal_dir_path = $literature_submission_data->directory_name . '/project_files/';
    //$dest_path1 = $proposal_dir_path . 'literature_survey/';
    $dest_path = $literature_submission_data->directory_name . '/project_files/';
    foreach ($_FILES['files']['name'] as $file_form_name => $file_name) {
      if ($file_name) {
        /* checking file type */
        if (strstr($file_form_name, 'main_netlist')) {
          $file_type = 'N';
        } //strstr($file_form_name, 'upload_circuit_simulation_developed_process')
        else {
          if (strstr($file_form_name, 'readme')) {
            $file_type = 'R';
          }
          else {
            if (strstr($file_form_name, 'subcircuit_files')) {
              $file_type = 'S';
            }
          }
        }
        switch ($file_type) {
          case 'N':
            if (file_exists($root_path . $dest_path . $_FILES['files']['name'][$file_form_name])) {
              \Drupal::messenger()->addError(t("Error uploading file. File !filename already exists.", [
                '!filename' => $_FILES['files']['name'][$file_form_name]
                ]));
              //unlink($root_path . $dest_path . $_FILES['files']['name'][$file_form_name]);
            } //file_exists($root_path . $dest_path . $_FILES['files']['name'][$file_form_name])
                    /* uploading file */
            else {
              if (move_uploaded_file($_FILES['files']['tmp_name'][$file_form_name], $root_path . $dest_path . $_FILES['files']['name'][$file_form_name])) {
                $query_ab_f = "SELECT * FROM hackathon_final_submission_project_files WHERE literature_submission_id = :literature_submission_id  AND filetype = 
                :filetype";
                $args_ab_f = [
                  ":literature_submission_id" => $literature_survey_id,
                  ":filetype" => $file_type,
                ];
                $query_ab_f_result = \Drupal::database()->query($query_ab_f, $args_ab_f)->fetchObject();
                unlink($root_path . $dest_path . $query_ab_f_result->filename);
                $query = "UPDATE {hackathon_final_submission_project_files} set 
                         filename = :filename, 
                         filepath = :filepath,
                         filemime = :filemime,
                         filesize = :filesize
                         WHERE literature_submission_id = :literature_submission_id and filetype = :filetype";
                $args = [
                  ":filename" => $_FILES['files']['name'][$file_form_name],
                  ":filepath" => $dest_path . $_FILES['files']['name'][$file_form_name],
                  ":filemime" => mime_content_type($root_path . $dest_path . $_FILES['files']['name'][$file_form_name]),
                  ":filesize" => $_FILES['files']['size'][$file_form_name],
                  ":literature_submission_id" => $literature_survey_id,
                  "filetype" => $file_type,
                ];
                $updateresult = \Drupal::database()->query($query, $args);
                \Drupal::messenger()->addStatus($file_name . ' uploaded successfully.');
              } //move_uploaded_file($_FILES['files']['tmp_name'][$file_form_name], $root_path . $dest_path . $_FILES['files']['name'][$file_form_name])
              else {
                \Drupal::messenger()->addError('Error uploading file : ' . $dest_path . $file_name);
              }
            }
            break;
          case 'R':
            if (file_exists($root_path . $dest_path . $_FILES['files']['name'][$file_form_name])) {
              \Drupal::messenger()->addError(t("Error uploading file. File !filename already exists.", [
                '!filename' => $_FILES['files']['name'][$file_form_name]
                ]));
              //unlink($root_path . $dest_path . $_FILES['files']['name'][$file_form_name]);
            } //file_exists($root_path . $dest_path . $_FILES['files']['name'][$file_form_name])
                    /* uploading file */
            else {
              if (move_uploaded_file($_FILES['files']['tmp_name'][$file_form_name], $root_path . $dest_path . $_FILES['files']['name'][$file_form_name])) {
                $query_ab_f = "SELECT * FROM hackathon_final_submission_project_files WHERE literature_submission_id = :literature_submission_id AND filetype = 
                :filetype";
                $args_ab_f = [
                  ":literature_submission_id" => $literature_survey_id,
                  ":filetype" => $file_type,
                ];
                $query_ab_f_result = \Drupal::database()->query($query_ab_f, $args_ab_f)->fetchObject();
                unlink($root_path . $dest_path . $query_ab_f_result->filename);
                $query = "UPDATE {hackathon_final_submission_project_files} set 
                         filename = :filename, 
                         filepath = :filepath,
                         filemime = :filemime,
                         filesize = :filesize
                         WHERE literature_submission_id = :literature_submission_id and filetype = :filetype";
                $args = [
                  ":filename" => $_FILES['files']['name'][$file_form_name],
                  ":filepath" => $dest_path . $_FILES['files']['name'][$file_form_name],
                  ":filemime" => mime_content_type($root_path . $dest_path . $_FILES['files']['name'][$file_form_name]),
                  ":filesize" => $_FILES['files']['size'][$file_form_name],
                  ":literature_submission_id" => $literature_survey_id,
                  "filetype" => $file_type,
                ];
                $updateresult = \Drupal::database()->query($query, $args);
                \Drupal::messenger()->addStatus($file_name . ' uploaded successfully.');
              } //move_uploaded_file($_FILES['files']['tmp_name'][$file_form_name], $root_path . $dest_path . $_FILES['files']['name'][$file_form_name])
              else {
                \Drupal::messenger()->addError('Error uploading file : ' . $dest_path . $file_name);
              }
            }
            break;
          case 'S':
            if (file_exists($root_path . $dest_path . $_FILES['files']['name'][$file_form_name])) {
              \Drupal::messenger()->addError(t("Error uploading file. File !filename already exists.", [
                '!filename' => $_FILES['files']['name'][$file_form_name]
                ]));
              //unlink($root_path . $dest_path . $_FILES['files']['name'][$file_form_name]);
            } //file_exists($root_path . $dest_path . $_FILES['files']['name'][$file_form_name])
                    /* uploading file */
            else {
              if (move_uploaded_file($_FILES['files']['tmp_name'][$file_form_name], $root_path . $dest_path . $_FILES['files']['name'][$file_form_name])) {
                $query_ab_f = "SELECT * FROM hackathon_final_submission_project_files WHERE literature_submission_id = :literature_submission_id  AND filetype = 
                :filetype";
                $args_ab_f = [
                  ":literature_submission_id" => $literature_survey_id,
                  ":filetype" => $file_type,
                ];
                $query_ab_f_result = \Drupal::database()->query($query_ab_f, $args_ab_f)->fetchObject();
                unlink($root_path . $dest_path . $query_ab_f_result->filename);
                $query = "UPDATE {hackathon_final_submission_project_files} set 
                         filename = :filename, 
                         filepath = :filepath,
                         filemime = :filemime,
                         filesize = :filesize
                         WHERE literature_submission_id = :literature_submission_id and filetype = :filetype";
                $args = [
                  ":filename" => $_FILES['files']['name'][$file_form_name],
                  ":filepath" => $dest_path . $_FILES['files']['name'][$file_form_name],
                  ":filemime" => mime_content_type($root_path . $dest_path . $_FILES['files']['name'][$file_form_name]),
                  ":filesize" => $_FILES['files']['size'][$file_form_name],
                  ":literature_submission_id" => $literature_survey_id,
                  "filetype" => $file_type,
                ];
                $updateresult = \Drupal::database()->query($query, $args);
                \Drupal::messenger()->addStatus($file_name . ' uploaded successfully.');
              } //move_uploaded_file($_FILES['files']['tmp_name'][$file_form_name], $root_path . $dest_path . $_FILES['files']['name'][$file_form_name])
              else {
                \Drupal::messenger()->addError('Error uploading file : ' . $dest_path . $file_name);
              }
            }
            break;
        }

      }
    }
    drupal_goto('hackathon-submission/proposed');
  }

}
?>
