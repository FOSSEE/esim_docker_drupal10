<?php

/**
 * @file
 * Contains \Drupal\hackathon_submission\Form\EditLiteratureReportSubmissionForm.
 */

namespace Drupal\hackathon_submission\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\Element;

class EditLiteratureReportSubmissionForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'edit_literature_report_submission_form';
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
    $query = \Drupal::database()->select('hackathon_literature_survey');
    $query->fields('hackathon_literature_survey');
    $query->condition('uid', $user->uid);
    $query->condition('id', $submission_id);
    //$query->range(0, 1);
    $submission_q = $query->execute();
    $submission_data = $submission_q->fetchObject();
    $form = [];
    $form['participant_name'] = [
      '#title' => t('Participant Name'),
      '#type' => 'textfield',
      '#disabled' => TRUE,
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
      '#disabled' => TRUE,
      // '#size' => 70,
      '#maxlength' => 70,
      '#required' => TRUE,
      '#default_value' => $submission_data->institute,
    ];
    /* $form['circuit'] = array(
        '#type' => 'fieldset',
        '#title' => t('Circuits'),
        '#collapsible' => FALSE,
        '#collapsed' => FALSE
    );*/
    $form['circuit_name'] = [
      '#title' => t('Circuit Name'),
      '#type' => 'textfield',
      // '#size' => 70,
      '#maxlength' => 70,
      '#required' => TRUE,
      '#default_value' => $submission_data->circuit_name,
    ];
    $form['abstract'] = [
      '#title' => t('Abstract'),
      '#type' => 'textarea',
      '#description' => t('The abstract should contain minimum 600 characters and not exceed more than 725 characters'),
      '#rows' => 5,
      '#minlength' => 600,
      '#maxlength' => 725,
      '#required' => TRUE,
      '#default_value' => $submission_data->abstract,
      /*'#ajax' => array(
        'callback' => 'update_length_ajax_callback',
        'wrapper' => 'length-div',
        'method' => 'replace',
        'effect' => 'fade',
        ),*/
    ];
    $form['circuit_details'] = [
      '#title' => t('Circuit Details'),
      '#type' => 'textarea',
      '#description' => t('The circuit details should contain minimum 1334 characters and not exceed more than 1600 characters'),
      '#rows' => 5,
      '#minlength' => 1334,
      '#maxlength' => 1600,
      '#required' => TRUE,
      '#default_value' => $submission_data->circuit_details,
    ];
    $form['reference_files'] = [
      '#type' => 'fieldset',
      '#title' => t('Upload Reference Files'),
      '#collapsible' => FALSE,
      '#collapsed' => FALSE,
    ];
    //var_dump($submission_id);die;
    $existing_uploaded_C_file = default_value_for_uploaded_reference_files("C", $submission_id);
    //var_dump($existing_uploaded_C_file);die;
    if (!$existing_uploaded_C_file) {
      $existing_uploaded_C_file = new stdClass();
      $existing_uploaded_C_file->filename = "No file uploaded";
    }
    // @FIXME
    // // @FIXME
    // // This looks like another module's variable. You'll need to rewrite this call
    // // to ensure that it uses the correct configuration object.
    // $form['reference_files']['reference_circuit'] = array(
    //         '#type' => 'file',
    //         '#title' => t('Upload reference circuit'),
    //         '#size' => 48,
    //         '#upload_validators' => array(
    //                 'file_validate_extensions' => array(variable_get('reference_circuit_extensions', '')),
    //                 // Pass the maximum file size in bytes
    //                 /*'file_validate_size' => array(5*1024*1024),*/
    //               ),
    //         '#description' => t('Upload image(900x600 pixels) with allowed extensions only. No spaces or any special characters allowed in filename.') . '<br />' . t('<span style="color:red;">Current File :</span> ') . $existing_uploaded_C_file->filename . '<br />' . t('<span style="color:red;">Allowed file extensions: ') . variable_get('reference_circuit_extensions', '') . '</span>'
    //     );

    $existing_uploaded_W_file = default_value_for_uploaded_reference_files("W", $submission_id);
    if (!$existing_uploaded_W_file) {
      $existing_uploaded_W_file = new stdClass();
      $existing_uploaded_W_file->filename = "No file uploaded";
    }
    // @FIXME
    // // @FIXME
    // // This looks like another module's variable. You'll need to rewrite this call
    // // to ensure that it uses the correct configuration object.
    // $form['reference_files']['reference_waveform'] = array(
    //         '#type' => 'file',
    //         '#title' => t('Upload reference waveform'),
    //         '#size' => 48,
    //         '#upload_validators' => array(
    //                 'file_validate_extensions' => array(variable_get('reference_circuit_extensions', '')),
    //                 // Pass the maximum file size in bytes
    //                 /*'file_validate_size' => array(5*1024*1024),*/
    //               ),
    //         '#description' => t('Upload image(900x600 pixels) with allowed extensions only. No spaces or any special characters allowed in filename.') . '<br />' . t('<span style="color:red;">Current File :</span> ') . $existing_uploaded_W_file->filename . '<br />' . t('<span style="color:red;">Allowed file extensions: ') . variable_get('reference_waveform_extensions', '') . '</span>'
    //     );

    $query_bib_ref = \Drupal::database()->select('hackathon_literature_survey_bib_references');
    $query_bib_ref->fields('hackathon_literature_survey_bib_references');
    $query_bib_ref->condition('submission_id', $submission_id);
    $result_bib_ref = $query_bib_ref->execute();
    $num_of_fellowresults = $result_bib_ref->rowCount();
    //var_dump($num_of_bib_ref);die;
    $form['existing_bib_reference_fieldset'] = [
      '#type' => 'fieldset',
      '#title' => t('References'),
      '#tree' => TRUE,
      '#prefix' => '<div id="existing_bib_reference-fieldset-wrapper">',
      '#suffix' => '</div>',
    ];
    $i = 0;
    while ($row_s = $result_bib_ref->fetchObject()) {

      $temp = $i;
      $form['existing_bib_reference_fieldset'][$i]["s_text"] = [
        "#type" => "item",
        "#markup" => "<h4><label>Resource : " . ($temp + 1) . "</label></h4>",
      ];
      $form['existing_bib_reference_fieldset'][$i]["id"] = [
        "#type" => "hidden",
        "#default_value" => $row_s->id,
      ];
      $form['existing_bib_reference_fieldset'][$i]["resource_link"] = [
        "#type" => "textfield",
        "#title" => "Resource Link",
        "#default_value" => $row_s->resource_link,
      ];
      $form['existing_bib_reference_fieldset'][$i]["resource_title"] = [
        "#type" => "textfield",
        "#title" => "Resource Title",
        "#default_value" => $row_s->resource_title,
      ];
      $form['existing_bib_reference_fieldset'][$i]["resource_author"] = [
        "#type" => "textfield",
        "#title" => "Resource Author",
        "#default_value" => $row_s->resource_author,
      ];
      $i++;
    }
    $form['bib_reference_fieldset'] = [
      '#type' => 'fieldset',
      '#title' => t('References'),
      '#tree' => TRUE,
      '#prefix' => '<div id="bib_reference-fieldset-wrapper">',
      '#suffix' => '</div>',
    ];
    /*  if (empty($form_state['num_bib_reference'])) {
                $form_state['num_bib_reference'] = 1;
            }*/
    $temp = 0;
    for ($i = $num_of_fellowresults; $i < 3; $i++) {
      $temp = $i;
      $form['bib_reference_fieldset'][$i]["s_text"] = [
        "#type" => "item",
        "#markup" => "<h4><label>Reference: " . ($temp + 1) . "</label></h4>",
      ];
      $form['bib_reference_fieldset'][$i]["resource_link"] = [
        "#type" => "textfield",
        "#title" => "Link to the resource",
        "#default_value" => "",
      ];
      $form['bib_reference_fieldset'][$i]["resource_title"] = [
        "#type" => "textfield",
        "#title" => "Resource Paper title",
        "#default_value" => "",
      ];
      $form['bib_reference_fieldset'][$i]["resource_author"] = [
        "#type" => "textfield",
        "#title" => "Author of the resource",
        "#default_value" => "",
      ];

    }
    $form["bib_reference_count"] = [
      "#type" => "hidden",
      "#value" => $num_of_fellowresults,
    ];
    /*if($i <3){
            $form['bib_reference_fieldset']['add_bib_reference'] = array(
                '#type' => 'submit',
                '#value' => t('Add more'),
                '#limit_validation_errors' => array(),
                '#submit' => array(
                    'edit_form_bib_reference_add_more_add_one'
                ),
                '#ajax' => array(
                    'callback' => 'edit_form_bib_reference_add_more_callback',
                    'wrapper' => 'bib_reference-fieldset-wrapper'
                )
            );
        }
            if ($form_state['num_bib_reference'] > 1) {
                $form['bib_reference_fieldset']['remove_bib_reference'] = array(
                    '#type' => 'submit',
                    '#value' => t('Remove'),
                    '#limit_validation_errors' => array(),
                    '#submit' => array(
                        'edit_form_bib_reference_add_more_remove_one'
                    ),
                    '#ajax' => array(
                        'callback' => 'bib_reference_add_more_callback',
                        'wrapper' => 'bib_reference-fieldset-wrapper'
                    )
                );
            }
            if ($no_js_use) {
                if (!empty($form['bib_reference_fieldset']['remove_bib_reference']['#ajax'])) {
                    unset($form['bib_reference_fieldset']['remove_bib_reference']['#ajax']);
                }
                unset($form['bib_reference_fieldset']['add_bib_reference']['#ajax']);
            }*/

    $form["submit"] = [
      "#type" => "submit",
      '#weight' => '6',
      "#value" => "Submit",
    ];
    return $form;
  }

  public function validateForm(array &$form, \Drupal\Core\Form\FormStateInterface $form_state) {
    if (strlen($form_state->getValue(['abstract'])) < 600) {
      $form_state->setErrorByName('abstract', t('Minimum charater limit for abstract is 600 charaters'));
    }
    if (strlen($form_state->getValue(['circuit_details'])) < 1334) {
      $form_state->setErrorByName('circuit_details', t('Minimum charater limit for circuit details is 1334 charaters'));
    }
    if (isset($_FILES['files'])) {
      /* check if atleast one source or result file is uploaded */
      /*if (!($_FILES['files']['name']['reference_circuit']))
            form_set_error('reference_circuit', t('Please upload the circuit diagram'));
        if(!($_FILES['files']['name']['reference_waveform']))
            form_set_error('reference_waveform', t('Please upload the waveform'));*/
      /* check for valid filename extensions */
      foreach ($_FILES['files']['name'] as $file_form_name => $file_name) {
        if ($file_name) {
          /* checking file type */
          if (strstr($file_form_name, 'reference_circuit')) {
            $file_type = 'C';
          }
          else {
            if (strstr($file_form_name, 'reference_waveform')) {
              $file_type = 'W';
            }
          }
          $allowed_extensions_str = '';
          switch ($file_type) {
            case 'C':
              // @FIXME
              // // @FIXME
              // // This looks like another module's variable. You'll need to rewrite this call
              // // to ensure that it uses the correct configuration object.
              // $allowed_extensions_str = variable_get('reference_circuit_extensions', '');

              break;
            case 'W':
              // @FIXME
              // // @FIXME
              // // This looks like another module's variable. You'll need to rewrite this call
              // // to ensure that it uses the correct configuration object.
              // $allowed_extensions_str = variable_get('reference_waveform_extensions', '');

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
    $submission_id = arg(3);
    $user = \Drupal::currentUser();
    $v = $form_state->getValues();
    $root_path = hackathon_submission_files_path();
    $query = "UPDATE hackathon_literature_survey SET 
				circuit_name = :circuit_name,
				abstract=:abstract,
				circuit_details=:circuit_details
				WHERE id=:submission_id";
    $args = [
      ":circuit_name" => $v['circuit_name'],
      ':abstract' => $v['abstract'],
      ':circuit_details' => $v['circuit_details'],
      ':submission_id' => $submission_id,
    ];
    $result = \Drupal::database()->query($query, $args);
    /*$submission_id = db_query($query, $args, array(
        'return' => Database::RETURN_INSERT_ID
    ));*/
    $query = \Drupal::database()->select('hackathon_literature_survey');
    $query->fields('hackathon_literature_survey');
    $query->condition('uid', $user->uid);
    $query->condition('id', $submission_id);
    $query->range(0, 1);
    $proposal_q = $query->execute();
    $proposal_data = $proposal_q->fetchObject();
    $proposal_dir_path = $proposal_data->directory_name . '/';
    $dest_path1 = $proposal_dir_path . 'literature_survey/';
    $dest_path = $dest_path1 . 'reference_files/';
    $bib_reference_upload = 0;
    for ($i = 0; $i < 3; $i++) {
      //$f_id=$v['bib_reference_fieldset'][$i]["f_id"];
      if ($v['bib_reference_fieldset'][$i]["resource_link"] != "") {
        $bib_referencequery = "INSERT INTO hackathon_literature_survey_bib_references (submission_id,resource_link,resource_title,resource_author) VALUES (:submission_id,:resource_link,:resource_title,:resource_author)";
        $bib_referenceargs = [
          ":submission_id" => $submission_id,
          ":resource_link" => trim($v['bib_reference_fieldset'][$i]["resource_link"]),
          ":resource_title" => trim($v['bib_reference_fieldset'][$i]["resource_title"]),
          ":resource_author" => trim($v['bib_reference_fieldset'][$i]["resource_author"]),
        ];
        /* storing the row id in $result */
        $bib_referenceresult = \Drupal::database()->query($bib_referencequery, $bib_referenceargs, $bib_referencequery);
        if ($bib_referenceresult != 0) {
          $bib_reference_upload++;
        }
      }
    }
    $existing_bib_reference_upload = 0;
    for ($i = 0; $i <= $v["bib_reference_count"]; $i++) {
      //$f_id=$v['bib_reference_fieldset'][$i]["f_id"];
                //if ($v['existing_bib_reference_fieldset'][$i]["resource_link"] != "") {
      $bib_referencequery = "UPDATE hackathon_literature_survey_bib_references set
                    resource_link = :resource_link,
                    resource_title = :resource_title,
                    resource_author = :resource_author
                    WHERE id =:id";
      $bib_referenceargs = [
        ":resource_link" => trim($v['existing_bib_reference_fieldset'][$i]["resource_link"]),
        ":resource_title" => trim($v['existing_bib_reference_fieldset'][$i]["resource_title"]),
        ":resource_author" => trim($v['existing_bib_reference_fieldset'][$i]["resource_author"]),
        ":id" => $v['existing_bib_reference_fieldset'][$i]["id"],
      ];
      /* storing the row id in $result */
      $bib_referenceresult = \Drupal::database()->query($bib_referencequery, $bib_referenceargs);
      if ($bib_referenceresult != 0) {
        $existing_bib_reference_upload++;
      }
      //}
    }

    foreach ($_FILES['files']['name'] as $file_form_name => $file_name) {
      if ($file_name) {
        /* checking file type */
        if (strstr($file_form_name, 'reference_circuit')) {
          $file_type = 'C';
        } //strstr($file_form_name, 'upload_circuit_simulation_developed_process')
        else {
          if (strstr($file_form_name, 'reference_waveform')) {
            $file_type = 'W';
          }
        }
        switch ($file_type) {
          case 'C':
            if (file_exists($root_path . $dest_path . $_FILES['files']['name'][$file_form_name])) {
              \Drupal::messenger()->addError(t("Error uploading file. File !filename already exists.", [
                '!filename' => $_FILES['files']['name'][$file_form_name]
                ]));
              //unlink($root_path . $dest_path . $_FILES['files']['name'][$file_form_name]);
            } //file_exists($root_path . $dest_path . $_FILES['files']['name'][$file_form_name])
                    /* uploading file */
            else {
              if (move_uploaded_file($_FILES['files']['tmp_name'][$file_form_name], $root_path . $dest_path . $_FILES['files']['name'][$file_form_name])) {
                $query_ab_f = "SELECT * FROM hackathon_literature_survey_files WHERE submission_id = :submission_id AND filetype = 
				:filetype";
                $args_ab_f = [
                  ":submission_id" => $submission_id,
                  ":filetype" => $file_type,
                ];
                $query_ab_f_result = \Drupal::database()->query($query_ab_f, $args_ab_f)->fetchObject();
                unlink($root_path . $dest_path . $query_ab_f_result->filename);
                $query = "UPDATE {hackathon_literature_survey_files} set 
                         filename = :filename, 
                         filepath = :filepath,
                         filemime = :filemime,
                         filesize = :filesize
                         WHERE submission_id = :submission_id and filetype = :filetype";
                $args = [
                  ":filename" => $_FILES['files']['name'][$file_form_name],
                  ":filepath" => $dest_path . $_FILES['files']['name'][$file_form_name],
                  ":filemime" => mime_content_type($root_path . $dest_path . $_FILES['files']['name'][$file_form_name]),
                  ":filesize" => $_FILES['files']['size'][$file_form_name],
                  ":submission_id" => $submission_id,
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
          case 'W':
            if (file_exists($root_path . $dest_path . $_FILES['files']['name'][$file_form_name])) {
              \Drupal::messenger()->addError(t("Error uploading file. File !filename already exists.", [
                '!filename' => $_FILES['files']['name'][$file_form_name]
                ]));
              //unlink($root_path . $dest_path . $_FILES['files']['name'][$file_form_name]);
            } //file_exists($root_path . $dest_path . $_FILES['files']['name'][$file_form_name])
                    /* uploading file */
            else {
              if (move_uploaded_file($_FILES['files']['tmp_name'][$file_form_name], $root_path . $dest_path . $_FILES['files']['name'][$file_form_name])) {
                $query_ab_f = "SELECT * FROM hackathon_literature_survey_files WHERE submission_id = :submission_id AND filetype = 
				:filetype";
                $args_ab_f = [
                  ":submission_id" => $submission_id,
                  ":filetype" => $file_type,
                ];
                $query_ab_f_result = \Drupal::database()->query($query_ab_f, $args_ab_f)->fetchObject();
                unlink($root_path . $dest_path . $query_ab_f_result->filename);
                $query = "UPDATE {hackathon_literature_survey_files} set 
                         filename = :filename, 
                         filepath = :filepath,
                         filemime = :filemime,
                         filesize = :filesize
                         WHERE submission_id = :submission_id and filetype = :filetype";
                $args = [
                  ":filename" => $_FILES['files']['name'][$file_form_name],
                  ":filepath" => $dest_path . $_FILES['files']['name'][$file_form_name],
                  ":filemime" => mime_content_type($root_path . $dest_path . $_FILES['files']['name'][$file_form_name]),
                  ":filesize" => $_FILES['files']['size'][$file_form_name],
                  ":submission_id" => $submission_id,
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
    \Drupal::messenger()->addStatus('Updated successfully');
    drupal_goto('hackathon-submission/proposed');
  }

}
?>
