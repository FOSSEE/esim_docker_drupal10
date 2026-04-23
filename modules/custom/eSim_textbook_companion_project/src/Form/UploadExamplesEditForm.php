<?php

/**
 * @file
 * Contains \Drupal\textbook_companion\Form\UploadExamplesEditForm.
 */

namespace Drupal\textbook_companion\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\Element;

class UploadExamplesEditForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'upload_examples_edit_form';
  }

  public function buildForm(array $form, \Drupal\Core\Form\FormStateInterface $form_state) {
    $user = \Drupal::currentUser();
    $example_id = arg(3);

    /* get example details */

    /*$example_q = db_query("SELECT * FROM {textbook_companion_example} WHERE id = %d LIMIT 1", $example_id);
  $example_data = db_fetch_object($example_q);*/

    $query = \Drupal::database()->select('textbook_companion_example');
    $query->fields('textbook_companion_example');
    $query->condition('id', $example_id);
    $query->range(0, 1);
    $example_q = $query->execute();
    $example_data = $example_q->fetchObject();

    if (!$example_q) {
      \Drupal::messenger()->addError(t("Invalid example selected."));
      drupal_goto('');
      return;
    }
    if ($example_data->approval_status != 0) {
      \Drupal::messenger()->addError(t("You cannot edit an example after it has been approved or dis-approved. Please contact site administrator if you want to edit this example."));
      drupal_goto('');
      return;
    }

    /* get examples files */
    $source_file = "";
    $source_id = 0;
    $result1_file = "";
    $result1_id = 0;
    $result2_file = "";
    $result2_id = 0;
    $xcos1_file = "";
    $xcos1_id = 0;
    $xcos2_file = "";
    $xcos2_id = 0;

    /*$example_files_q = db_query("SELECT * FROM {textbook_companion_example_files} WHERE example_id = %d", $example_id);*/
    $query = \Drupal::database()->select('textbook_companion_example_files');
    $query->fields('textbook_companion_example_files');
    $query->condition('example_id', $example_id);
    $example_files_q = $query->execute();

    while ($example_files_data = $example_files_q->fetchObject()) {
      if ($example_files_data->filetype == "S") {
        // @FIXME
// l() expects a Url object, created from a route name or external URI.
// $source_file = l($example_files_data->filename, 'download/file/' . $example_files_data->id);

        $source_file_id = $example_files_data->id;
      }
      if ($example_files_data->filetype == "R") {
        if (strlen($result1_file) == 0) {
          // @FIXME
// l() expects a Url object, created from a route name or external URI.
// $result1_file = l($example_files_data->filename, 'download/file/' . $example_files_data->id);

          $result1_file_id = $example_files_data->id;
        }
        else {
          // @FIXME
// l() expects a Url object, created from a route name or external URI.
// $result2_file = l($example_files_data->filename, 'download/file/' . $example_files_data->id);

          $result2_file_id = $example_files_data->id;
        }
      }
      if ($example_files_data->filetype == "X") {
        if (strlen($xcos1_file) <= 0) {
          // @FIXME
// l() expects a Url object, created from a route name or external URI.
// $xcos1_file = l($example_files_data->filename, 'download/file/' . $example_files_data->id);

          $xcos1_file_id = $example_files_data->id;
        }
        else {
          // @FIXME
// l() expects a Url object, created from a route name or external URI.
// $xcos2_file = l($example_files_data->filename, 'download/file/' . $example_files_data->id);

          $xcos2_file_id = $example_files_data->id;
        }
      }
    }

    /* get chapter details */

    /*$chapter_q = db_query("SELECT * FROM {textbook_companion_chapter} WHERE id = %d", $example_data->chapter_id);
  $chapter_data = db_fetch_object($chapter_q);*/
    $query = \Drupal::database()->select('textbook_companion_chapter');
    $query->fields('textbook_companion_chapter');
    $query->condition('id', $example_data->chapter_id);
    $result = $query->execute();
    $chapter_data = $result->fetchObject();

    if (!$chapter_data) {
      \Drupal::messenger()->addError(t("Invalid chapter selected."));
      drupal_goto('');
      return;
    }

    /* get preference details */

    /*$preference_q = db_query("SELECT * FROM {textbook_companion_preference} WHERE id = %d", $chapter_data->preference_id);
  $preference_data = db_fetch_object($preference_q);*/
    $query = \Drupal::database()->select('textbook_companion_preference');
    $query->fields('textbook_companion_preference');
    $query->condition('id', $chapter_data->preference_id);
    $result = $query->execute();
    $preference_data = $result->fetchObject();


    if (!$preference_data) {
      \Drupal::messenger()->addError(t("Invalid book selected."));
      drupal_goto('');
      return;
    }
    if ($preference_data->approval_status != 1) {
      \Drupal::messenger()->addError(t("Cannot edit example. Either the book proposal has not been approved or it has been rejected."));
      drupal_goto('');
      return;
    }

    /* get proposal details */

    /*$proposal_q = db_query("SELECT * FROM {textbook_companion_proposal} WHERE id = %d", $preference_data->proposal_id);
  $proposal_data = db_fetch_object($proposal_q);*/
    $query = \Drupal::database()->select('textbook_companion_proposal');
    $query->fields('textbook_companion_proposal');
    $query->condition('id', $preference_data->proposal_id);
    $result = $query->execute();
    $proposal_data = $result->fetchObject();

    if (!$proposal_data) {
      \Drupal::messenger()->addError(t("Invalid proposal selected."));
      drupal_goto('');
      return;
    }
    if ($proposal_data->uid != $user->uid) {
      \Drupal::messenger()->addError(t("You do not have permissions to edit this example."));
      drupal_goto('');
      return;
    }

    $user_data = \Drupal::entityTypeManager()->getStorage('user')->load($proposal_data->uid);

    /* add javascript for automatic book title, check if example uploaded, dependency selection effects */
    /* $chapter_name_js = " $(document).ready(function() {
    $('#edit-existing-depfile-dep-book-title').change(function() {
      var dep_selected = ''; 
      /* showing and hiding relevant files */
    /*  $('.form-checkboxes .option').hide();
      $('.form-checkboxes .option').each(function(index) {
        var activeClass = $('#edit-existing-depfile-dep-book-title').val();
        if ($(this).children().hasClass(activeClass)) {
          $(this).show();
        }
        if ($(this).children().attr('checked') == true) {
          dep_selected += $(this).children().next().text() + '<br />';
        }
      });
      /* showing list of already existing dependencies */
    /*  $('#existing_depfile_selected').html(dep_selected);
    });

    $('.form-checkboxes .option').change(function() {
      $('#edit-existing-depfile-dep-book-title').trigger('change');
    });
    $('#edit-existing-depfile-dep-book-title').trigger('change');
  });";
  drupal_add_js($chapter_name_js, 'inline', 'header');
*/
    $form['#redirect'] = 'textbook_companion/code';
    $form['#attributes'] = ['enctype' => "multipart/form-data"];

    $form['book_details']['book'] = [
      '#type' => 'item',
      '#markup' => $preference_data->book,
      '#title' => t('Title of the Book'),
    ];
    $form['contributor_name'] = [
      '#type' => 'item',
      '#markup' => $proposal_data->full_name,
      '#title' => t('Contributor Name'),
    ];
    $form['number'] = [
      '#type' => 'item',
      '#title' => t('Chapter No'),
      '#markup' => $chapter_data->number,
    ];
    $form['name'] = [
      '#type' => 'item',
      '#title' => t('Title of the Chapter'),
      '#markup' => $chapter_data->name,
    ];
    $form['example_number'] = [
      '#type' => 'item',
      '#title' => t('Example No'),
      '#markup' => $example_data->number,
    ];
    $form['example_caption'] = [
      '#type' => 'textfield',
      '#title' => t('Caption'),
       // '#size' => 40,
      '#maxlength' => 255,
      '#required' => TRUE,
      '#default_value' => $example_data->caption,
    ];
    $form['example_warning'] = [
      '#type' => 'item',
      '#title' => t('You should upload all the files (main or source files, result files, executable file if any)'),
      '#prefix' => '<div style="color:red">',
      '#suffix' => '</div>',
    ];

    $form['sourcefile'] = [
      '#type' => 'fieldset',
      '#title' => t('Main or Source Files'),
      '#collapsible' => FALSE,
      '#collapsed' => FALSE,
    ];
    if ($source_file) {
      $form['sourcefile']['cur_source'] = [
        '#type' => 'item',
        '#title' => t('Existing Main or Source File'),
        '#markup' => $source_file,
      ];
      $form['sourcefile']['cur_source_checkbox'] = [
        '#type' => 'checkbox',
        '#title' => t('Delete Existing Main or Source File'),
        '#description' => 'Check to delete the existing Main or Source file.',
      ];
      $form['sourcefile']['sourcefile1'] = [
        '#type' => 'file',
        '#title' => t('Upload New Main or Source File'),
         // '#size' => 48,
        '#description' => t("Upload new Main or Source file above if you want to replace the existing file. Leave blank if you want to keep using the existing file. <br />") . t('Allowed file extensions : ') . \Drupal::config('textbook_companion.settings')->get('textbook_companion_source_extensions'),
      ];
      $form['sourcefile']['cur_source_file_id'] = [
        '#type' => 'hidden',
        '#value' => $source_file_id,
      ];
    }
    else {
      $form['sourcefile']['sourcefile1'] = [
        '#type' => 'file',
        '#title' => t('Upload New Main or Source File'),
         // '#size' => 48,
        '#description' => t('Allowed file extensions : ') . \Drupal::config('textbook_companion.settings')->get('textbook_companion_source_extensions'),
      ];
    }

    /*  $form['dep_files'] = array(
    '#type' => 'item',
    '#title' => t('Dependency Files'),
  );*/

    /************ START OF EXISTING DEPENDENCIES **************/

    // $dependency_files = array();

    /*$dependency_q = db_query("SELECT * FROM {textbook_companion_example_dependency} WHERE example_id = %d", $example_data->id);*/

    /*	$query = db_select('textbook_companion_example_dependency');
	$query->fields('textbook_companion_example_dependency');
	$query->condition('example_id', $example_data->id);
	$dependency_q = $query->execute();

  while ($dependency_data = $dependency_q->fetchObject())
  {
    $dependency_files[] = $dependency_data->dependency_id;
  }
*/
    /* existing dependencies */
    /*$form['existing_depfile'] = array(
    '#type' => 'fieldset',
    '#title' => t('Use Already Existing Dependency Files'),
    '#collapsible' => FALSE,
    '#collapsed' => FALSE,
    '#prefix' => '<div id="existing-depfile-wrapper">',
    '#suffix' => '</div>',
    '#tree' => TRUE,
  );*/

    /* existing dependencies */
    /* $form['existing_depfile']['selected'] = array(
    '#type' => 'item',
    '#title' => t('Existing Dependency Files Selected'),
    '#markup' => '<div id="existing_depfile_selected"></div>',
  );
  
  $form['existing_depfile']['dep_book_title'] = array(
    '#type' => 'select',
    '#title' => t('Title of the Book'),
    '#options' => _list_of_book_titles(),
  );

  list($files_options, $files_options_class) = _list_of_book_dependency_files();
  $form['existing_depfile']['dep_chapter_example_files'] = array(
    '#type' => 'checkboxes',
    '#title' => t('Dependency Files'),
    '#options' => $files_options,
    '#options_class' => $files_options_class,
    '#multiple' => TRUE,
    '#default_value' => $dependency_files,
  );

  $form['existing_depfile']['dep_upload'] = array(
    '#type' => 'item',
    '#markup' => l('Upload New Depedency Files', 'textbook_companion/code/upload_dep'),
  );*/
    /************ END OF EXISTING DEPENDENCIES **************/

    /* $form['result'] = array(
    '#type' => 'fieldset',
    '#title' => t('Result Files'),
    '#collapsible' => FALSE,
    '#collapsed' => FALSE,
  );
  if ($result1_file)
  {
    $form['result']['cur_result1'] = array(
      '#type' => 'item',
      '#title' => t('Existing Result File 1'),
      '#markup' => $result1_file,
    );
    $form['result']['cur_result1_checkbox'] = array(
      '#type' => 'checkbox',
      '#title' => t('Delete Existing Result File 1'),
      '#description' => 'Check to delete the existing Result file.',
    );
    $form['result']['result1'] = array(
      '#type' => 'file',
      '#title' => t('Upload New Result File 1'),
       // '#size' => 48,
      '#description' => t("Upload new Result file above if you want to replace the existing file, leave blank if you want to keep using the existing file. <br />") .
        t('Allowed file extensions : ') . variable_get('textbook_companion_result_extensions', ''),
    );
    $form['result']['cur_result1_file_id'] = array(
      '#type' => 'hidden',
      '#value' => $result1_file_id,
    );
  } else {
    $form['result']['result1'] = array(
      '#type' => 'file',
      '#title' => t('Upload New Result File 1'),
       // '#size' => 48,
      '#description' => t('Allowed file extensions : ') . variable_get('textbook_companion_result_extensions', ''),
    );
  }
  
  $form['result']['br'] = array(
  '#type' => 'item',
  '#markup' => "<br />",
  );
  
  if ($result2_file)
  {
    $form['result']['cur_result2'] = array(
      '#type' => 'item',
      '#title' => t('Existing Result File 2'),
      '#markup' => $result2_file,
    );
    $form['result']['cur_result2_checkbox'] = array(
      '#type' => 'checkbox',
      '#title' => t('Delete Existing Result File 2'),
      '#description' => 'Check to delete the existing Result file.',
    );
    $form['result']['result2'] = array(
      '#type' => 'file',
      '#title' => t('Upload New Result file 2'),
       // '#size' => 48,
      '#description' => t("Upload new Result file above if you want to replace the existing file. Leave blank if you want to keep using the existing file. <br />") . 
        t('Allowed file extensions : ') . variable_get('textbook_companion_result_extensions', ''),
    );
    $form['result']['cur_result2_file_id'] = array(
      '#type' => 'hidden',
      '#value' => $result2_file_id,
    );
  } else {
    $form['result']['result2'] = array(
      '#type' => 'file',
      '#title' => t('Upload New Result file 2'),
       // '#size' => 48,
      '#description' => t('Allowed file extensions : ') . variable_get('textbook_companion_result_extensions', ''),
    );
  }
  $form['xcos'] = array(
    '#type' => 'fieldset',
    '#title' => t('XCOS Files'),
    '#collapsible' => FALSE,
    '#collapsed' => FALSE,
  );
  if ($xcos1_file)
  {
    $form['xcos']['cur_xcos1'] = array(
      '#type' => 'item',
      '#title' => t('Existing xcos File 1'),
      '#markup' => $xcos1_file,
    );
    $form['xcos']['cur_xcos1_checkbox'] = array(
      '#type' => 'checkbox',
      '#title' => t('Delete Existing xcos File 1'),
      '#description' => 'Check to delete the existing xcos file.',
    );
    $form['xcos']['xcos1'] = array(
      '#type' => 'file',
      '#title' => t('Upload New xcos file 1'),
       // '#size' => 48,
      '#description' => t("Upload new xcos file above if you want to replace the existing file. Leave blank if you want to keep using the existing file. <br />") .
        t('Allowed file extensions : ') . variable_get('textbook_companion_xcos_extensions', ''),
    );
    $form['sourcefile']['cur_xcos1_file_id'] = array(
      '#type' => 'hidden',
      '#value' => $xcos1_file_id,
    );
  } else {
    $form['xcos']['xcos1'] = array(
      '#type' => 'file',
      '#title' => t('Upload New xcos file 1'),
       // '#size' => 48,
      '#description' => t('Allowed file extensions : ') . variable_get('textbook_companion_xcos_extensions', ''),
    );
  }

  $form['xcos']['br'] = array(
    '#type' => 'item',
    '#markup' => "<br />",
  );

  if ($xcos2_file)
  {
    $form['xcos']['cur_xcos2'] = array(
      '#type' => 'item',
      '#title' => t('Existing xcos File 2'),
      '#markup' => $xcos2_file,
    );
    $form['xcos']['cur_xcos2_checkbox'] = array(
      '#type' => 'checkbox',
      '#title' => t('Delete Existing xcos File 2'),
      '#description' => 'Check to delete the existing xcos file.',
    );
    $form['xcos']['xcos2'] = array(
      '#type' => 'file',
      '#title' => t('Upload New xcos file 2'),
       // '#size' => 48,
      '#description' =>  t("Upload new xcos file above if you want to replace the existing file. Leave blank if you want to keep using the existing file. <br />") . 
        t('Allowed file extensions : ') . variable_get('textbook_companion_xcos_extensions', ''),
    );
    $form['xcos']['cur_xcos2_file_id'] = array(
      '#type' => 'hidden',
      '#value' => $xcos2_file_id,
    );
  } else {
    $form['xcos']['xcos2'] = array(
      '#type' => 'file',
      '#title' => t('Upload New xcos file 2'),
       // '#size' => 48,
      '#description' => t('Allowed file extensions : ') . variable_get('textbook_companion_xcos_extensions', ''),
    );
  }
*/
    $form['submit'] = [
      '#type' => 'submit',
      '#value' => t('Submit'),
    ];

    // @FIXME
    // l() expects a Url object, created from a route name or external URI.
    // $form['cancel'] = array(
    //     '#type' => 'markup',
    //     '#value' => l(t('Cancel'), 'textbook_companion/code'),
    //   );

    return $form;
  }

  public function validateForm(array &$form, \Drupal\Core\Form\FormStateInterface $form_state) {
    if (!check_name($form_state->getValue(['example_caption']))) {
      $form_state->setErrorByName('example_caption', t('Example Caption can contain only alphabets, numbers and spaces.'));
    }

    if (isset($_FILES['files'])) {
      /* check for valid filename extensions */
      foreach ($_FILES['files']['name'] as $file_form_name => $file_name) {
        if ($file_name) {
          /* checking file type */
          if (strstr($file_form_name, 'source')) {
            $file_type = 'S';
          }
          else {
            if (strstr($file_form_name, 'result')) {
              $file_type = 'R';
            }
            else {
              if (strstr($file_form_name, 'xcos')) {
                $file_type = 'X';
              }
              else {
                $file_type = 'U';
              }
            }
          }

          $allowed_extensions_str = '';
          switch ($file_type) {
            case 'S':
              $allowed_extensions_str = \Drupal::config('textbook_companion.settings')->get('textbook_companion_source_extensions');
              break;
            case 'R':
              $allowed_extensions_str = \Drupal::config('textbook_companion.settings')->get('textbook_companion_result_extensions');
              break;
            case 'X':
              $allowed_extensions_str = \Drupal::config('textbook_companion.settings')->get('textbook_companion_xcos_extensions');
              break;
          }

          $allowed_extensions = explode(',', $allowed_extensions_str);
          $temp_extension = end(explode('.', strtolower($_FILES['files']['name'][$file_form_name])));
          if (!in_array($temp_extension, $allowed_extensions)) {
            $form_state->setErrorByName($file_form_name, t('Only file with ' . $allowed_extensions_str . ' extensions can be uploaded.'));
          }
          if ($_FILES['files']['size'][$file_form_name] <= 0) {
            $form_state->setErrorByName($file_form_name, t('File size cannot be zero.'));
          }

          /* check if valid file name */
          if (!textbook_companion_check_valid_filename($_FILES['files']['name'][$file_form_name])) {
            $form_state->setErrorByName($file_form_name, t('Invalid file name specified. Only alphabets, numbers and underscore is allowed as a valid filename.'));
          }
        }
      }
    }

    /* add javascript again for automatic book title, check if example uploaded, dependency selection effects */
    $chapter_name_js = " $(document).ready(function() {
    $('#edit-number').change(function() {
      $.get('" . base_path() . "textbook_companion/ajax/chapter_title/' + $('#edit-number').val() + '/' + " . $row->pre_id . ", function(data) {
        $('#edit-name').val(data);
      });
    });
    $('#edit-example-number').change(function() {
      $.get('" . base_path() . "textbook_companion/ajax/example_exists/' + $('#edit-number').val() + '/' + $('#edit-example-number').val(), function(data) {
        if (data) {
          alert(data);
        }
      });
    });
    $('#edit-existing-depfile-dep-book-title').change(function() {
      var dep_selected = ''; 
      /* showing and hiding relevant files */
      $('.form-checkboxes .option').hide();
      $('.form-checkboxes .option').each(function(index) {
        var activeClass = $('#edit-existing-depfile-dep-book-title').val();
        if ($(this).children().hasClass(activeClass)) {
          $(this).show();
        }
        if ($(this).children().attr('checked') == true) {
          dep_selected += $(this).children().next().text() + '<br />';
        }
      });
      /* showing list of already existing dependencies */
      $('#existing_depfile_selected').html(dep_selected);
    });

    $('.form-checkboxes .option').change(function() {
      $('#edit-existing-depfile-dep-book-title').trigger('change');
    });
    $('#edit-existing-depfile-dep-book-title').trigger('change');
  });";
    // @FIXME
    // The Assets API has totally changed. CSS, JavaScript, and libraries are now
    // attached directly to render arrays using the #attached property.
    // 
    // 
    // @see https://www.drupal.org/node/2169605
    // @see https://www.drupal.org/node/2408597
    // drupal_add_js($chapter_name_js, 'inline', 'header');

  }

  public function submitForm(array &$form, \Drupal\Core\Form\FormStateInterface $form_state) {
    $user = \Drupal::currentUser();
    $example_id = arg(3);

    /* get example details */

    /*$example_q = db_query("SELECT * FROM {textbook_companion_example} WHERE id = %d LIMIT 1", $example_id);
  $example_data = db_fetch_object($example_q);*/

    $query = \Drupal::database()->select('textbook_companion_example');
    $query->fields('textbook_companion_example');
    $query->condition('id', $example_id);
    $query->range(0, 1);
    $example_q = $query->execute();
    $example_data = $example_q->fetchObject();

    if (!$example_q) {
      \Drupal::messenger()->addError(t("Invalid example selected."));
      drupal_goto('');
      return;
    }
    if ($example_data->approval_status != 0) {
      \Drupal::messenger()->addError(t("You cannot edit an example after it has been approved or dis-approved. Please contact site administrator if you want to edit this example."));
      drupal_goto('');
      return;
    }

    /* get chapter details */

    /*$chapter_q = db_query("SELECT * FROM {textbook_companion_chapter} WHERE id = %d", $example_data->chapter_id);
  $chapter_data = db_fetch_object($chapter_q);*/

    $query = \Drupal::database()->select('textbook_companion_chapter');
    $query->fields('textbook_companion_chapter');
    $query->condition('id', $example_data->chapter_id);
    $result = $query->execute();
    $chapter_data = $result->fetchObject();

    if (!$chapter_data) {
      \Drupal::messenger()->addError(t("Invalid chapter selected."));
      drupal_goto('');
      return;
    }

    /* get preference details */

    /*$preference_q = db_query("SELECT * FROM {textbook_companion_preference} WHERE id = %d", $chapter_data->preference_id);
  $preference_data = db_fetch_object($preference_q);*/
    $query = \Drupal::database()->select('textbook_companion_preference');
    $query->fields('textbook_companion_preference');
    $query->condition('id', $chapter_data->preference_id);
    $result = $query->execute();
    $preference_data = $result->fetchObject();

    if (!$preference_data) {
      \Drupal::messenger()->addError(t("Invalid book selected."));
      drupal_goto('');
      return;
    }
    if ($preference_data->approval_status != 1) {
      \Drupal::messenger()->addError(t("Cannot edit example. Either the book proposal has not been approved or it has been rejected."));
      drupal_goto('');
      return;
    }

    /* get proposal details */

    /*$proposal_q = db_query("SELECT * FROM {textbook_companion_proposal} WHERE id = %d", $preference_data->proposal_id);
  $proposal_data = db_fetch_object($proposal_q);*/

    $query = \Drupal::database()->select('textbook_companion_proposal');
    $query->fields('textbook_companion_proposal');
    $query->condition('id', $preference_data->proposal_id);
    $result = $query->execute();
    $proposal_data = $result->fetchObject();

    if (!$proposal_data) {
      \Drupal::messenger()->addError(t("Invalid proposal selected."));
      drupal_goto('');
      return;
    }
    if ($proposal_data->uid != $user->uid) {
      \Drupal::messenger()->addError(t("You do not have permissions to edit this example."));
      drupal_goto('');
      return;
    }

    /* creating directories */
    $root_path = textbook_companion_path();

    $dest_path = $preference_data->id . '/';
    if (!is_dir($root_path . $dest_path)) {
      mkdir($root_path . $dest_path);
    }

    $dest_path .= 'CH' . $chapter_data->number . '/';
    if (!is_dir($root_path . $dest_path)) {
      mkdir($root_path . $dest_path);
    }

    $dest_path .= 'EX' . $example_data->number . '/';
    if (!is_dir($root_path . $dest_path)) {
      mkdir($root_path . $dest_path);
    }

    /* updating example caption */

    /*db_query("UPDATE {textbook_companion_example} SET caption = '%s' WHERE id = %d", $form_state['values']['example_caption'], $example_id);*/

    $query = \Drupal::database()->update('textbook_companion_example');
    $query->fields(['caption' => $form_state->getValue(['example_caption'])]);
    $query->condition('id', $example_id);
    $num_updated = $query->execute();

    /* handling dependencies */

    /*db_query("DELETE FROM {textbook_companion_example_dependency} WHERE example_id = %d", $example_data->id);*/

    /*$query = db_delete('textbook_companion_example_dependency');
	$query->condition('example_id', $example_data->id);
	$num_deleted = $query->execute();

  foreach ($form_state['values']['existing_depfile']['dep_chapter_example_files'] as $row)
  {
    if ($row > 0)
    {*/
    /* insterting into database */
    /*db_query("INSERT INTO {textbook_companion_example_dependency} (example_id, dependency_id, approval_status, timestamp)
        VALUES (%d, %d, %d, %d)",
        $example_data->id,
        $row,
        0,
        time()
      );*/
    /*$query = "INSERT INTO {textbook_companion_example_dependency} (example_id, dependency_id, approval_status, timestamp)
        VALUES (:example_id, :dependency_id, :approval_status, :timestamp)";
			$args = array(
			":example_id"=> $example_data->id, 
			":dependency_id"=> $row,
			":approval_status"=> 0,
			":timestamp"=> time(),
			);
			$result = db_query($query, $args, array('return' => Database::RETURN_INSERT_ID));
    }
  }*/

    /* handle source file */
    $cur_file_id = $form_state->getValue(['cur_source_file_id']);
    if ($cur_file_id > 0) {
      /*$file_q = db_query("SELECT * FROM  {textbook_companion_example_files} WHERE id = %d AND example_id = %d", $cur_file_id, $example_data->id);
    $file_data = db_fetch_object($file_q);*/
      $query = \Drupal::database()->select('textbook_companion_example_files');
      $query->fields('textbook_companion_example_files');
      $query->condition('id', $cur_file_id);
      $query->condition('example_id', $example_data->id);
      $result = $query->execute();
      $file_data = $result->fetchObject();

      if (!$file_data) {
        \Drupal::messenger()->addError("Error deleting example source file. File not present in database.");
        return;
      }
      if (($form_state->getValue(['cur_source_checkbox']) == 1) && (!$_FILES['files']['name']['sourcefile1'])) {
        if (!delete_file($cur_file_id)) {
          \Drupal::messenger()->addError("Error deleting example source file.");
          return;
        }
      }
    }
    if ($_FILES['files']['name']['sourcefile1']) {
      if ($cur_file_id > 0) {
        if (!delete_file($cur_file_id)) {
          \Drupal::messenger()->addError("Error removing previous example source file.");
          return;
        }
      }
      if (file_exists($root_path . $dest_path . $_FILES['files']['name']['sourcefile1'])) {
        \Drupal::messenger()->addError(t("Error uploading source file. File !filename already exists.", [
          '!filename' => $_FILES['files']['name']['sourcefile1']
          ]));
        return;
      }
      /* uploading file */
      if (move_uploaded_file($_FILES['files']['tmp_name']['sourcefile1'], $root_path . $dest_path . $_FILES['files']['name']['sourcefile1'])) {
        /* for uploaded files making an entry in the database */

        /*db_query("INSERT INTO {textbook_companion_example_files} (example_id, filename, filepath, filemime, filesize, filetype, timestamp)
        VALUES (%d, '%s', '%s', '%s', %d, '%s', %d)",
        $example_data->id,
        $_FILES['files']['name']['sourcefile1'],
        $dest_path . $_FILES['files']['name']['sourcefile1'],
        $_FILES['files']['type']['sourcefile1'],
        $_FILES['files']['size']['sourcefile1'],
        'S',
        time()
        );*/

        $query = "INSERT INTO {textbook_companion_example_files} (example_id, filename, filepath, filemime, filesize, filetype, 		timestamp) VALUES (:example_id, :filename, :filepath, :filemime, :filesize, :filetype,:timestamp)";
        $args = [
          ":example_id" => $example_data->id,
          ":filename" => $_FILES['files']['name']['sourcefile1'],
          ":filepath" => $dest_path . $_FILES['files']['name']['sourcefile1'],
          ":filemime" => $_FILES['files']['type']['sourcefile1'],
          ":filesize" => $_FILES['files']['size']['sourcefile1'],
          ":filetype" => 'S',
          ":timestamp" => time(),
        ];
        $result = \Drupal::database()->query($query, $args, $query);


        \Drupal::messenger()->addStatus($_FILES['files']['name']['sourcefile1'] . ' uploaded successfully.');
      }
      else {
        \Drupal::messenger()->addError('Error uploading file : ' . $dest_path . '/' . $_FILES['files']['name']['sourcefile1']);
      }
    }

    /* handle result1 file */
    $cur_file_id = $form_state->getValue(['cur_result1_file_id']);
    if ($cur_file_id > 0) {
      /*$file_q = db_query("SELECT * FROM  {textbook_companion_example_files} WHERE id = %d AND example_id = %d", $cur_file_id, $example_data->id);
    $file_data = db_fetch_object($file_q);*/
      $query = \Drupal::database()->select('textbook_companion_example_files');
      $query->fields('textbook_companion_example_files');
      $query->condition('id', $cur_file_id);
      $query->condition('example_id', $example_data->id);
      $result = $query->execute();
      $file_data = $result->fetchObject();

      if (!$file_data) {
        \Drupal::messenger()->addError("Error deleting example result 1 file. File not present in database.");
        return;
      }
      if (($form_state->getValue(['cur_result1_checkbox']) == 1) && (!$_FILES['files']['name']['result1'])) {
        if (!delete_file($cur_file_id)) {
          \Drupal::messenger()->addError("Error deleting example result 1 file.");
          return;
        }
      }
    }
    if ($_FILES['files']['name']['result1']) {
      if ($cur_file_id > 0) {
        if (!delete_file($cur_file_id)) {
          \Drupal::messenger()->addError("Error removing previous example result 1 file.");
          return;
        }
      }
      if (file_exists($root_path . $dest_path . $_FILES['files']['name']['result1'])) {
        \Drupal::messenger()->addError(t("Error uploading result 1 file. File !filename already exists.", [
          '!filename' => $_FILES['files']['name']['result1']
          ]));
        return;
      }
      /* uploading file */
      if (move_uploaded_file($_FILES['files']['tmp_name']['result1'], $root_path . $dest_path . $_FILES['files']['name']['result1'])) {
        /* for uploaded files making an entry in the database */

        /*db_query("INSERT INTO {textbook_companion_example_files} (example_id, filename, filepath, filemime, filesize, filetype, timestamp)
        VALUES (%d, '%s', '%s', '%s', %d, '%s', %d)",
        $example_data->id,
        $_FILES['files']['name']['result1'],
        $dest_path . $_FILES['files']['name']['result1'],
        $_FILES['files']['type']['result1'],
        $_FILES['files']['size']['result1'],
        'R',
        time()
        );*/

        $query = "INSERT INTO {textbook_companion_example_files} (example_id, filename, filepath, filemime, filesize, filetype, 		timestamp) VALUES (:example_id, :filename, :filepath, :filemime, :filesize, :filetype,:timestamp)";
        $args = [
          ":example_id" => $example_data->id,
          ":filename" => $_FILES['files']['name']['result1'],
          ":filepath" => $dest_path . $_FILES['files']['name']['result1'],
          ":filemime" => $_FILES['files']['type']['result1'],
          ":filesize" => $_FILES['files']['size']['result1'],
          ":filetype" => 'R',
          ":timestamp" => time(),
        ];
        $result = \Drupal::database()->query($query, $args, $query);

        \Drupal::messenger()->addStatus($_FILES['files']['name']['result1'] . ' uploaded successfully.');
      }
      else {
        \Drupal::messenger()->addError('Error uploading file : ' . $dest_path . '/' . $_FILES['files']['name']['result1']);
      }
    }

    /* handle result2 file */
    $cur_file_id = $form_state->getValue(['cur_result2_file_id']);
    if ($cur_file_id > 0) {
      /*$file_q = db_query("SELECT * FROM  {textbook_companion_example_files} WHERE id = %d AND example_id = %d", $cur_file_id, $example_data->id);
    $file_data = db_fetch_object($file_q);*/

      $query = \Drupal::database()->select('textbook_companion_example_files');
      $query->fields('textbook_companion_example_files');
      $query->condition('id', $cur_file_id);
      $query->condition('example_id', $example_data->id);
      $result = $query->execute();
      $file_data = $result->fetchObject();

      if (!$file_data) {
        \Drupal::messenger()->addError("Error deleting example result 2 file. File not present in database.");
        return;
      }
      if (($form_state->getValue(['cur_result2_checkbox']) == 1) && (!$_FILES['files']['name']['result2'])) {
        if (!delete_file($cur_file_id)) {
          \Drupal::messenger()->addError("Error deleting example result 2 file.");
          return;
        }
      }
    }
    if ($_FILES['files']['name']['result2']) {
      if ($cur_file_id > 0) {
        if (!delete_file($cur_file_id)) {
          \Drupal::messenger()->addError("Error removing previous example result 2 file.");
          return;
        }
      }
      if (file_exists($root_path . $dest_path . $_FILES['files']['name']['result2'])) {
        \Drupal::messenger()->addError(t("Error uploading result 2 file. File !filename already exists.", [
          '!filename' => $_FILES['files']['name']['result2']
          ]));
        return;
      }
      /* uploading file */
      if (move_uploaded_file($_FILES['files']['tmp_name']['result2'], $root_path . $dest_path . $_FILES['files']['name']['result2'])) {
        /* for uploaded files making an entry in the database */
        /*db_query("INSERT INTO {textbook_companion_example_files} (example_id, filename, filepath, filemime, filesize, filetype, timestamp)
        VALUES (%d, '%s', '%s', '%s', %d, '%s', %d)",
        $example_data->id,
        $_FILES['files']['name']['result2'],
        $dest_path . $_FILES['files']['name']['result2'],
        $_FILES['files']['type']['result2'],
        $_FILES['files']['size']['result2'],
        'R',
        time()
        );*/

        $query = "INSERT INTO {textbook_companion_example_files} (example_id, filename, filepath, filemime, filesize, filetype, 		timestamp) VALUES (:example_id, :filename, :filepath, :filemime, :filesize, :filetype,:timestamp)";
        $args = [
          ":example_id" => $example_data->id,
          ":filename" => $_FILES['files']['name']['result2'],
          ":filepath" => $dest_path . $_FILES['files']['name']['result2'],
          ":filemime" => $_FILES['files']['type']['result2'],
          ":filesize" => $_FILES['files']['size']['result2'],
          ":filetype" => 'R',
          ":timestamp" => time(),
        ];
        $result = \Drupal::database()->query($query, $args, $query);

        \Drupal::messenger()->addStatus($_FILES['files']['name']['result2'] . ' uploaded successfully.');
      }
      else {
        \Drupal::messenger()->addError('Error uploading file : ' . $dest_path . '/' . $_FILES['files']['name']['result2']);
      }
    }

    /* handle xcos1 file */
    $cur_file_id = $form_state->getValue(['cur_xcos1_file_id']);
    if ($cur_file_id > 0) {
      /*$file_q = db_query("SELECT * FROM  {textbook_companion_example_files} WHERE id = %d AND example_id = %d", $cur_file_id, $example_data->id);
    $file_data = db_fetch_object($file_q);*/

      $query = \Drupal::database()->select('textbook_companion_example_files');
      $query->fields('textbook_companion_example_files');
      $query->condition('id', $cur_file_id);
      $query->condition('example_id', $example_data->id);
      $result = $query->execute();
      $file_data = $result->fetchObject();


      if (!$file_data) {
        \Drupal::messenger()->addError("Error deleting example xcos 1 file. File not present in database.");
        return;
      }
      if (($form_state->getValue(['cur_xcos1_checkbox']) == 1) && (!$_FILES['files']['name']['xcos1'])) {
        if (!delete_file($cur_file_id)) {
          \Drupal::messenger()->addError("Error deleting example xcos 1 file.");
          return;
        }
      }
    }
    if ($_FILES['files']['name']['xcos1']) {
      if ($cur_file_id > 0) {
        if (!delete_file($cur_file_id)) {
          \Drupal::messenger()->addError("Error removing previous example xcos 1 file.");
          return;
        }
      }
      if (file_exists($root_path . $dest_path . $_FILES['files']['name']['xcos1'])) {
        \Drupal::messenger()->addError(t("Error uploading xcos 1 file. File !filename already exists.", [
          '!filename' => $_FILES['files']['name']['xcos1']
          ]));
        return;
      }
      /* uploading file */
      if (move_uploaded_file($_FILES['files']['tmp_name']['xcos1'], $root_path . $dest_path . $_FILES['files']['name']['xcos1'])) {
        /* for uploaded files making an entry in the database */

        /*db_query("INSERT INTO {textbook_companion_example_files} (example_id, filename, filepath, filemime, filesize, filetype, timestamp)
        VALUES (%d, '%s', '%s', '%s', %d, '%s', %d)",
        $example_data->id,
        $_FILES['files']['name']['xcos1'],
        $dest_path . $_FILES['files']['name']['xcos1'],
        $_FILES['files']['type']['xcos1'],
        $_FILES['files']['size']['xcos1'],
        'X',
        time()
        );*/

        $query = "INSERT INTO {textbook_companion_example_files} (example_id, filename, filepath, filemime, filesize, filetype, 		timestamp) VALUES (:example_id, :filename, :filepath, :filemime, :filesize, :filetype,:timestamp)";
        $args = [
          ":example_id" => $example_data->id,
          ":filename" => $_FILES['files']['name']['xcos1'],
          ":filepath" => $dest_path . $_FILES['files']['name']['xcos1'],
          ":filemime" => $_FILES['files']['type']['xcos1'],
          ":filesize" => $_FILES['files']['size']['xcos1'],
          ":filetype" => 'X',
          ":timestamp" => time(),
        ];
        $result = \Drupal::database()->query($query, $args, $query);

        \Drupal::messenger()->addStatus($_FILES['files']['name']['xcos1'] . ' uploaded successfully.');
      }
      else {
        \Drupal::messenger()->addError('Error uploading file : ' . $dest_path . '/' . $_FILES['files']['name']['xcos1']);
      }
    }

    /* handle xcos2 file */
    $cur_file_id = $form_state->getValue(['cur_xcos2_file_id']);
    if ($cur_file_id > 0) {
      /*$file_q = db_query("SELECT * FROM  {textbook_companion_example_files} WHERE id = %d AND example_id = %d", $cur_file_id, $example_data->id);
    $file_data = db_fetch_object($file_q);*/

      $query = \Drupal::database()->select('textbook_companion_example_files');
      $query->fields('textbook_companion_example_files');
      $query->condition('id', $cur_file_id);
      $query->condition('example_id', $example_data->id);
      $result = $query->execute();
      $file_data = $result->fetchObject();

      if (!$file_data) {
        \Drupal::messenger()->addError("Error deleting example xcos 2 file. File not present in database.");
        return;
      }
      if (($form_state->getValue(['cur_xcos2_checkbox']) == 1) && (!$_FILES['files']['name']['xcos2'])) {
        if (!delete_file($cur_file_id)) {
          \Drupal::messenger()->addError("Error deleting example xcos 2 file.");
          return;
        }
      }
    }
    if ($_FILES['files']['name']['xcos2']) {
      if ($cur_file_id > 0) {
        if (!delete_file($cur_file_id)) {
          \Drupal::messenger()->addError("Error removing previous example xcos 2 file.");
          return;
        }
      }
      if (file_exists($root_path . $dest_path . $_FILES['files']['name']['xcos2'])) {
        \Drupal::messenger()->addError(t("Error uploading xcos 2 file. File !filename already exists.", [
          '!filename' => $_FILES['files']['name']['xcos2']
          ]));
        return;
      }
      /* uploading file */
      if (move_uploaded_file($_FILES['files']['tmp_name']['xcos2'], $root_path . $dest_path . $_FILES['files']['name']['xcos2'])) {
        /* for uploaded files making an entry in the database */
        /*db_query("INSERT INTO {textbook_companion_example_files} (example_id, filename, filepath, filemime, filesize, filetype, timestamp)
        VALUES (%d, '%s', '%s', '%s', %d, '%s', %d)",
        $example_data->id,
        $_FILES['files']['name']['xcos2'],
        $dest_path . $_FILES['files']['name']['xcos2'],
        $_FILES['files']['type']['xcos2'],
        $_FILES['files']['size']['xcos2'],
        'X',
        time()
        );*/

        $query = "INSERT INTO {textbook_companion_example_files} (example_id, filename, filepath, filemime, filesize, filetype, 		timestamp) VALUES (:example_id, :filename, :filepath, :filemime, :filesize, :filetype,:timestamp)";
        $args = [
          ":example_id" => $example_data->id,
          ":filename" => $_FILES['files']['name']['xcos2'],
          ":filepath" => $dest_path . $_FILES['files']['name']['xcos2'],
          ":filemime" => $_FILES['files']['type']['xcos2'],
          ":filesize" => $_FILES['files']['size']['xcos2'],
          ":filetype" => 'X',
          ":timestamp" => time(),
        ];
        $result = \Drupal::database()->query($query, $args, $query);

        \Drupal::messenger()->addStatus($_FILES['files']['name']['xcos2'] . ' uploaded successfully.');
      }
      else {
        \Drupal::messenger()->addError('Error uploading file : ' . $dest_path . '/' . $_FILES['files']['name']['xcos2']);
      }
    }

    /* sending email */
    $email_to = $user->mail;
    $from = \Drupal::config('textbook_companion.settings')->get('textbook_companion_from_email');
    $bcc = \Drupal::config('textbook_companion.settings')->get('textbook_companion_emails');
    $cc = \Drupal::config('textbook_companion.settings')->get('textbook_companion_cc_emails');
    $param['example_updated']['example_id'] = $example_id;
    $param['example_updated']['user_id'] = $user->uid;
    $param['example_updated']['headers'] = [
      'From' => $from,
      'MIME-Version' => '1.0',
      'Content-Type' => 'text/plain; charset=UTF-8; format=flowed; delsp=yes',
      'Content-Transfer-Encoding' => '8Bit',
      'X-Mailer' => 'Drupal',
      'Cc' => $cc,
      'Bcc' => $bcc,
    ];

    if (!drupal_mail('textbook_companion', 'example_updated', $email_to, language_default(), $param, $from, TRUE)) {
      \Drupal::messenger()->addError('Error sending email message.');
    }

    \Drupal::messenger()->addStatus(t("Example successfully udpated."));
  }

}
?>
