<?php

/**
 * @file
 * Contains \Drupal\hackathon_submission\Form\EditMscdLiteratureReportSubmissionForm.
 */

namespace Drupal\hackathon_submission\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\Element;

class EditMscdLiteratureReportSubmissionForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'edit_mscd_literature_report_submission_form';
  }

  public function buildForm(array $form, \Drupal\Core\Form\FormStateInterface $form_state, $no_js_use = NULL) {
    $user = \Drupal::currentUser();
    $submission_id = arg(3);
    if ($user->uid == 0) {
      $msg = \Drupal::messenger()->addError(t('It is mandatory to log in on this website to edit your submission. If you are new user please create a new account first.'));
      //drupal_goto('esim-circuit-simulation-project');
      drupal_goto('user/login', [
        'query' => drupal_get_destination()
        ]);
      return $msg;
    } //$user->uid == 0
    $query = \Drupal::database()->select('mixed_signal_marathon_literature_survey');
    $query->fields('mixed_signal_marathon_literature_survey');
    $query->condition('uid', $user->uid);
    $query->condition('id', $submission_id);
    //$query->range(0, 1);
    $submission_q = $query->execute();
    $submission_data = $submission_q->fetchObject();
    $form = [];
    $form['participant_name'] = [
      '#title' => t('Participant Name'),
      '#type' => 'textfield',
      // '#size' => 70,
      '#maxlength' => 70,
      '#default_value' => $submission_data->participant_name,
    ];
    $form['participant_email'] = [
      '#type' => 'textfield',
      '#title' => t('Email'),
      // '#size' => 30,
      '#value' => $user->mail,
      '#disabled' => TRUE,
    ];
    $form['institute'] = [
      '#type' => 'textfield',
      '#title' => t('Name of the college/institute'),
      // '#size' => 70,
      '#maxlength' => 70,
      '#required' => TRUE,
      '#default_value' => $submission_data->institute,
    ];
    $form['circuit_name'] = [
      '#title' => t('Circuit Name'),
      '#type' => 'textfield',
      // '#size' => 70,
      '#maxlength' => 70,
      '#required' => TRUE,
      '#default_value' => $submission_data->circuit_name,
    ];
    $form['circuit_type'] = [
      '#title' => t('Circuit Type'),
      '#type' => 'select',
      '#title' => t('Type of the circuit'),
      '#options' => [
        'Analog' => 'Analog',
        'Digital' => 'Digital',
        'Mixed' => 'Mixed',
      ],
      '#required' => TRUE,
      '#default_value' => $submission_data->circuit_type,
    ];
    $form['reference_files'] = [
      '#type' => 'fieldset',
      '#title' => t('Edit literature report'),
      '#collapsible' => FALSE,
      '#collapsed' => FALSE,
    ];
    if (!$submission_data->report_file) {
      $report_file = "Not Uploaded";
    }
    else {
      $report_file = $submission_data->report_file;
    }
    // @FIXME
    // // @FIXME
    // // This looks like another module's variable. You'll need to rewrite this call
    // // to ensure that it uses the correct configuration object.
    // $form['reference_files']['literature_report'] = array(
    //         '#type' => 'file',
    //         '#size' => 48,
    //         '#title' => t('Click <a href= "https://static.fossee.in/esim/resources/Literature-survey.pdf" target="_blank">here</a> to view the template of the file.'),
    //         '#upload_validators' => array(
    //                 'file_validate_extensions' => array(variable_get('mscd_literature_report_extensions', '')),
    //                 // Pass the maximum file size in bytes
    //                 /*'file_validate_size' => array(5*1024*1024),*/
    //               ),
    //         '#description' => t('No spaces or any special characters allowed in filename.') . '<br />' . t('<span style="color:red;">Current File:</span> ') . $report_file . '<br />' . t('<span style="color:red;">Allowed file extensions: ') . variable_get('mscd_literature_report_extensions', '') . '</span>'
    //     );


    $form["submit"] = [
      "#type" => "submit",
      '#weight' => '6',
      "#value" => "Submit",
    ];
    return $form;
  }

  public function validateForm(array &$form, \Drupal\Core\Form\FormStateInterface $form_state) {
    if (isset($_FILES['files'])) {
      /* check if atleast one source or result file is uploaded */
      /* if (!($_FILES['files']['name']['literature_report']))
            form_set_error('literature_report', t('Please upload the literature report'));
       */ /* check for valid filename extensions */
      foreach ($_FILES['files']['name'] as $file_form_name => $file_name) {
        if ($file_name) {
          /* checking file type */
          /*if (strstr($file_form_name, 'literature_report'))
                        $file_type = 'L';
                $allowed_extensions_str = '';
                switch ($file_type)
                {
                    case 'L':
                        $allowed_extensions_str = variable_get('mscd_literature_report_extensions', '');
                        break;
                }
                $allowed_extensions = explode(',', $allowed_extensions_str);
                $fnames = explode('.', strtolower($_FILES['files']['name'][$file_form_name]));
                $temp_extension = end($fnames);
                if (!in_array($temp_extension, $allowed_extensions))
                    form_set_error($file_form_name, t('Only file with ' . $allowed_extensions_str . ' extensions can be uploaded.'));*/
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
    $submission_id = arg(3);
    $user = \Drupal::currentUser();
    $v = $form_state->getValues();
    $root_path = mscd_hackathon_submission_files_path();
    $query = "UPDATE mixed_signal_marathon_literature_survey SET 
                participant_name = :participant_name,
                institute = :institute,
                circuit_name = :circuit_name,
                circuit_type=:circuit_type
                WHERE id=:submission_id";
    $args = [
      ":participant_name" => $v['participant_name'],
      ":institute" => $v['institute'],
      ":circuit_name" => $v['circuit_name'],
      ":circuit_type" => $v['circuit_type'],
      ":submission_id" => $submission_id,
    ];
    $result = \Drupal::database()->query($query, $args);
    /*$submission_id = db_query($query, $args, array(
        'return' => Database::RETURN_INSERT_ID
    ));*/
    $query = \Drupal::database()->select('mixed_signal_marathon_literature_survey');
    $query->fields('mixed_signal_marathon_literature_survey');
    $query->condition('uid', $user->uid);
    $query->condition('id', $submission_id);
    //$query->range(0, 1);
    $submission_q = $query->execute();
    $submission_data = $submission_q->fetchObject();
    $dest_path = $submission_data->directory_name . '/';
    foreach ($_FILES['files']['name'] as $file_form_name => $file_name) {
      if ($file_name) {
        /* checking file type */
        if (strstr($file_form_name, 'literature_report')) {
          $file_type = 'L';
        } //strstr($file_form_name, 'upload_circuit_simulation_developed_process')
        switch ($file_type) {
          case 'L':
            if (file_exists($root_path . $dest_path . $_FILES['files']['name'][$file_form_name])) {
              \Drupal::messenger()->addError(t("Error uploading file. File !filename already exists.", [
                '!filename' => $_FILES['files']['name'][$file_form_name]
                ]));
              //unlink($root_path . $dest_path . $_FILES['files']['name'][$file_form_name]);
            } //file_exists($root_path . $dest_path . $_FILES['files']['name'][$file_form_name])
                    /* uploading file */
            else {
              if (move_uploaded_file($_FILES['files']['tmp_name'][$file_form_name], $root_path . $dest_path . $_FILES['files']['name'][$file_form_name])) {
                $query_ab_f = "SELECT * FROM mixed_signal_marathon_literature_survey WHERE id = :submission_id";
                $args_ab_f = [":submission_id" => $submission_id];
                $query_ab_f_result = \Drupal::database()->query($query_ab_f, $args_ab_f)->fetchObject();
                unlink($root_path . $dest_path . $query_ab_f_result->report_file);
                $query = "UPDATE mixed_signal_marathon_literature_survey set 
                         report_file = :report_file
                         WHERE id = :submission_id";
                $args = [
                  ":report_file" => $_FILES['files']['name'][$file_form_name],
                  ":submission_id" => $submission_id,
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
    \Drupal::messenger()->addStatus('Updated successfully');
    drupal_goto('mixed-signal-design-marathon/proposed');
  }

}
?>
