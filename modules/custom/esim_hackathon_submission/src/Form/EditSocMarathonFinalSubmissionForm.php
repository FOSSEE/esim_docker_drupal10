<?php

/**
 * @file
 * Contains \Drupal\hackathon_submission\Form\EditSocMarathonFinalSubmissionForm.
 */

namespace Drupal\hackathon_submission\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\Element;

class EditSocMarathonFinalSubmissionForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'edit_soc_marathon_final_submission_form';
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
    // @FIXME
    // // @FIXME
    // // This looks like another module's variable. You'll need to rewrite this call
    // // to ensure that it uses the correct configuration object.
    // $last_date = variable_get('soc_marathon_final_submission_last_date', '');

    $today = date("Y-m-d H:i:s");
    //var_dump($submission_id);die;
    if ($today > $last_date) {
      $literature_survey_action = \Drupal::messenger()->addError(t('Submissions are closed'));
      drupal_goto('mixed-signal-soc-design-marathon/proposed');
      return $literature_survey_action;
    }
    $query = \Drupal::database()->select('mixed_signal_soc_marathon_literature_survey');
    $query->fields('mixed_signal_soc_marathon_literature_survey');
    $query->condition('uid', $user->uid);
    $query->condition('id', $literature_survey_id);
    //$query->range(0, 1);
    $literature_submission_q = $query->execute();
    $literature_submission_data = $literature_submission_q->fetchObject();
    $query = \Drupal::database()->select('mixed_signal_soc_marathon_final_submission');
    $query->fields('mixed_signal_soc_marathon_final_submission');
    $query->condition('literature_survey_id', $literature_survey_id);
    $project_files_data = $query->execute()->fetchObject();
    if (!$project_files_data) {
      \Drupal::messenger()->addMessage('We have not yet received your project files');
      drupal_goto('mixed-signal-design-marathon/add/final-submission');
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
    $form['github_repo_link'] = [
      '#type' => 'textfield',
      '#title' => t('Edit link to your GitHub repository'),
      // '#size' => 255,
      '#maxlength' => 255,
      '#default_value' => $project_files_data->github_repo_link,
      '#required' => TRUE,
    ];
    $form['edit_files'] = [
      '#type' => 'fieldset',
      '#title' => t('Edit Final submission files'),
      '#collapsible' => FALSE,
      '#collapsed' => FALSE,
    ];
    $existing_uploaded_F_file = default_value_for_uploaded_soc_marathon_final_submission("F", $literature_survey_id);
    if (!$existing_uploaded_F_file) {
      $existing_uploaded_F_file = new stdClass();
      $existing_uploaded_F_file->filename = "No file uploaded";
    }
    // @FIXME
    // // @FIXME
    // // This looks like another module's variable. You'll need to rewrite this call
    // // to ensure that it uses the correct configuration object.
    // $form['edit_files']['final_report_pdf'] = array(
    //         '#type' => 'file',
    //         // '#disabled' => TRUE,
    //         '#title' => t('Edit Final report. <br>Click <a href= "https://static.fossee.in/esim/resources/Final-report.pdf" target="_blank">here</a> to view the template of the file.'),
    //         '#size' => 48,
    //         '#description' => t('Upload filenames with allowed extensions only. No spaces or any special characters allowed in filename.') . '<br />' . t('<span style="color:red;">Current File :</span> ') . $existing_uploaded_F_file->filename . '<br />' . t('<span style="color:red;">Allowed file extensions: ') . variable_get('soc_marathon_final_report_extensions', '') . '</span>'
    //     );

    $existing_uploaded_P_file = default_value_for_uploaded_soc_marathon_final_submission("P", $literature_survey_id);
    if (!$existing_uploaded_P_file) {
      $existing_uploaded_P_file = new stdClass();
      $existing_uploaded_P_file->filename = "No file uploaded";
    }
    // @FIXME
    // // @FIXME
    // // This looks like another module's variable. You'll need to rewrite this call
    // // to ensure that it uses the correct configuration object.
    // $form['edit_files']['project_files'] = array(
    //         '#type' => 'file',
    //         '#title' => t('Edit project files. <br>Click <a href= "https://static.fossee.in/esim/resources/Counter_project_template.zip" target="_blank">here</a> to view the template of the file.'),
    //         '#size' => 48,
    //         '#description' => t('Upload filenames with allowed extensions only. No spaces or any special characters allowed in filename.') . '<br />' . t('<span style="color:red;">Current File :</span> ') . $existing_uploaded_P_file->filename . '<br />' . t('<span style="color:red;">Allowed file extensions: ') . variable_get('soc_marathon_project_files_extensions', '') . '</span>'
    //     );

    $form["submit"] = [
      "#type" => "submit",
      "#value" => "Submit",
    ];
    // @FIXME
    // l() expects a Url object, created from a route name or external URI.
    // $form['back'] = array(
    //         '#type' => 'item',
    //         '#markup' => l('Go Back', 'mixed-signal-soc-design-marathon/proposed')
    //     );

    return $form;
  }

  public function validateForm(array &$form, \Drupal\Core\Form\FormStateInterface $form_state) {
    /*if(!preg_match("/^(https\:\/\/github.com\/)/", $form_state['values']['github_repo_link'])){
        form_set_error('github_repo_link', t('Enter link to GitHub repository'));
    }*/
    if (isset($_FILES['files'])) {
      /* check for valid filename extensions */
      foreach ($_FILES['files']['name'] as $file_form_name => $file_name) {
        if ($file_name) {
          /* checking file type */
          if (strstr($file_form_name, 'final_report_pdf')) {
            $file_type = 'F';
          }
          else {
            if (strstr($file_form_name, 'project_files')) {
              $file_type = 'P';
            }
          }
          $allowed_extensions_str = '';
          switch ($file_type) {
            case 'F':
              // @FIXME
              // // @FIXME
              // // This looks like another module's variable. You'll need to rewrite this call
              // // to ensure that it uses the correct configuration object.
              // $allowed_extensions_str = variable_get('soc_marathon_final_report_extensions', '');

              break;
            case 'P':
              // @FIXME
              // // @FIXME
              // // This looks like another module's variable. You'll need to rewrite this call
              // // to ensure that it uses the correct configuration object.
              // $allowed_extensions_str = variable_get('soc_marathon_project_files_extensions', '');

              break;
          }
          $allowed_extensions = explode(',', $allowed_extensions_str);
          $fnames = explode('.', strtolower($_FILES['files']['name'][$file_form_name]));
          $temp_extension = end($fnames);
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
    $root_path = soc_marathon_hackathon_submission_files_path();
    $literature_survey_id = arg(3);
    $query = \Drupal::database()->select('mixed_signal_soc_marathon_literature_survey');
    $query->fields('mixed_signal_soc_marathon_literature_survey');
    $query->condition('uid', $user->uid);
    $query->condition('id', $literature_survey_id);
    //$query->range(0, 1);
    $literature_submission_q = $query->execute();
    $literature_submission_data = $literature_submission_q->fetchObject();
    //$proposal_dir_path = $literature_submission_data->directory_name . '/project_files/';
    //$dest_path1 = $proposal_dir_path . 'literature_survey/';
    $dest_path = $literature_submission_data->directory_name . '/';
    $query = "UPDATE mixed_signal_soc_marathon_final_submission SET 
                github_repo_link = :github_repo_link,
                creation_date = :creation_date
                WHERE literature_survey_id=:literature_survey_id";
    $args = [
      ":github_repo_link" => $v['github_repo_link'],
      ":creation_date" => time(),
      ":literature_survey_id" => $literature_survey_id,
    ];
    $result = \Drupal::database()->query($query, $args);
    foreach ($_FILES['files']['name'] as $file_form_name => $file_name) {
      if ($file_name) {
        /* checking file type */
        if (strstr($file_form_name, 'final_report_pdf')) {
          $file_type = 'F';
        } //strstr($file_form_name, 'upload_circuit_simulation_developed_process')
        else {
          if (strstr($file_form_name, 'project_files')) {
            $file_type = 'P';
          }
        }
        switch ($file_type) {
          case 'F':
            if (file_exists($root_path . $dest_path . $_FILES['files']['name'][$file_form_name])) {
              move_uploaded_file($_FILES['files']['tmp_name'][$file_form_name], $root_path . $dest_path . $_FILES['files']['name'][$file_form_name]);
              $query = "UPDATE mixed_signal_soc_marathon_final_submission_files set 
                         filename = :filename, 
                         filepath = :filepath,
                         filemime = :filemime,
                         filesize = :filesize
                         WHERE literature_survey_id = :literature_survey_id and filetype = :filetype";
              $args = [
                ":filename" => $_FILES['files']['name'][$file_form_name],
                ":filepath" => $dest_path . $_FILES['files']['name'][$file_form_name],
                ":filemime" => mime_content_type($root_path . $dest_path . $_FILES['files']['name'][$file_form_name]),
                ":filesize" => $_FILES['files']['size'][$file_form_name],
                ":literature_survey_id" => $literature_survey_id,
                "filetype" => $file_type,
              ];
              $updateresult = \Drupal::database()->query($query, $args);
              \Drupal::messenger()->addStatus(t("File !filename already exists hence overwirtten the exisitng file ", [
                '!filename' => $_FILES['files']['name'][$file_form_name]
                ]));
              //unlink($root_path . $dest_path . $_FILES['files']['name'][$file_form_name]);
            } //file_exists($root_path . $dest_path . $_FILES['files']['name'][$file_form_name])
                    /* uploading file */
            else {
              if (move_uploaded_file($_FILES['files']['tmp_name'][$file_form_name], $root_path . $dest_path . $_FILES['files']['name'][$file_form_name])) {
                $query_ab_f = "SELECT * FROM mixed_signal_soc_marathon_final_submission_files WHERE literature_survey_id = :literature_survey_id  AND filetype = 
                :filetype";
                $args_ab_f = [
                  ":literature_survey_id" => $literature_survey_id,
                  ":filetype" => $file_type,
                ];
                $query_ab_f_result = \Drupal::database()->query($query_ab_f, $args_ab_f)->fetchObject();
                unlink($root_path . $dest_path . $query_ab_f_result->filename);
                $query = "UPDATE mixed_signal_soc_marathon_final_submission_files set 
                         filename = :filename, 
                         filepath = :filepath,
                         filemime = :filemime,
                         filesize = :filesize
                         WHERE literature_survey_id = :literature_survey_id and filetype = :filetype";
                $args = [
                  ":filename" => $_FILES['files']['name'][$file_form_name],
                  ":filepath" => $dest_path . $_FILES['files']['name'][$file_form_name],
                  ":filemime" => mime_content_type($root_path . $dest_path . $_FILES['files']['name'][$file_form_name]),
                  ":filesize" => $_FILES['files']['size'][$file_form_name],
                  ":literature_survey_id" => $literature_survey_id,
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
          case 'P':
            if (file_exists($root_path . $dest_path . $_FILES['files']['name'][$file_form_name])) {
              move_uploaded_file($_FILES['files']['tmp_name'][$file_form_name], $root_path . $dest_path . $_FILES['files']['name'][$file_form_name]);
              $query = "UPDATE mixed_signal_soc_marathon_final_submission_files set 
                         filename = :filename, 
                         filepath = :filepath,
                         filemime = :filemime,
                         filesize = :filesize
                         WHERE literature_survey_id = :literature_survey_id and filetype = :filetype";
              $args = [
                ":filename" => $_FILES['files']['name'][$file_form_name],
                ":filepath" => $dest_path . $_FILES['files']['name'][$file_form_name],
                ":filemime" => mime_content_type($root_path . $dest_path . $_FILES['files']['name'][$file_form_name]),
                ":filesize" => $_FILES['files']['size'][$file_form_name],
                ":literature_survey_id" => $literature_survey_id,
                "filetype" => $file_type,
              ];
              $updateresult = \Drupal::database()->query($query, $args);
              \Drupal::messenger()->addStatus(t("File !filename already exists hence overwirtten the exisitng file ", [
                '!filename' => $_FILES['files']['name'][$file_form_name]
                ]));
              //unlink($root_path . $dest_path . $_FILES['files']['name'][$file_form_name]);
            } //file_exists($root_path . $dest_path . $_FILES['files']['name'][$file_form_name])
                    /* uploading file */
            else {
              if (move_uploaded_file($_FILES['files']['tmp_name'][$file_form_name], $root_path . $dest_path . $_FILES['files']['name'][$file_form_name])) {
                $query_ab_f = "SELECT * FROM mixed_signal_soc_marathon_final_submission_files WHERE literature_survey_id = :literature_survey_id  AND filetype = :filetype";
                $args_ab_f = [
                  ":literature_survey_id" => $literature_survey_id,
                  ":filetype" => $file_type,
                ];
                $query_ab_f_result = \Drupal::database()->query($query_ab_f, $args_ab_f)->fetchObject();
                unlink($root_path . $dest_path . $query_ab_f_result->filename);
                $query = "UPDATE mixed_signal_soc_marathon_final_submission_files set 
                         filename = :filename, 
                         filepath = :filepath,
                         filemime = :filemime,
                         filesize = :filesize
                         WHERE literature_survey_id = :literature_survey_id and filetype = :filetype";
                $args = [
                  ":filename" => $_FILES['files']['name'][$file_form_name],
                  ":filepath" => $dest_path . $_FILES['files']['name'][$file_form_name],
                  ":filemime" => mime_content_type($root_path . $dest_path . $_FILES['files']['name'][$file_form_name]),
                  ":filesize" => $_FILES['files']['size'][$file_form_name],
                  ":literature_survey_id" => $literature_survey_id,
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
    \Drupal::messenger()->addStatus('Your submission is updated');
    drupal_goto('mixed-signal-soc-design-marathon/proposed');
  }

}
?>
