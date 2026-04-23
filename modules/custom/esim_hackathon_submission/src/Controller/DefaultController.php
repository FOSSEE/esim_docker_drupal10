<?php /**
 * @file
 * Contains \Drupal\hackathon_submission\Controller\DefaultController.
 */

namespace Drupal\hackathon_submission\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Database\Database;
use Drupal\Core\Link;
use Drupal\Core\Url;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\HttpFoundation\Response;
use Drupal\Core\Routing\RouteMatchInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Drupal\Core\File\FileSystemInterface;
use Drupal\Service;
use Drupal\user\Entity\User;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Form\FormBuilderInterface;
use Drupal\Core\Render\Markup;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Drupal\Core\Database\Connection;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;





/**
 * Default controller for the hackathon_submission module.
 */
class DefaultController extends ControllerBase {

  public function hackathon_submission_display_my_submissions() {
    $user = \Drupal::currentUser();
    /* get pending proposals to be approved */
    if ($user->uid == 0) {
      $msg = \Drupal::messenger()->addError(t('It is mandatory to login on this website to view your proposals. If you are new user please create a new account first.'));
      //drupal_goto('/pssp');
      drupal_goto('user/login', [
        'query' => drupal_get_destination()
        ]);
      return $msg;
    }
    $output = "<p>Final Submission</p>";
    $final_submission_rows = [];
    $query = \Drupal::database()->select('hackathon_literature_survey');
    $query->fields('hackathon_literature_survey');
    //$query->condition('approval_status', 2);
    $query->condition('uid', $user->uid);
    $query->orderBy('id', 'DESC');
    $my_proposals_q = $query->execute();
    $my_proposals_data = $my_proposals_q->fetchObject();
    if (!$my_proposals_data) {
      \Drupal::messenger()->addError('We have not received your submission');
      drupal_goto('');
    }
    $query = \Drupal::database()->select('hackathon_final_submission');
    $query->fields('hackathon_final_submission');
    $query->condition('uid', $user->uid);
    $query->condition('literature_survey_id', $my_proposals_data->id);
    $query->orderBy('id', 'DESC');
    $final_submission_q = $query->execute();
    $final_submission_data = $final_submission_q->fetchObject();
    $today = date("Y-m-d H:i:s");
    $final_submission_last_date = "2021-06-30 23:59:59.0";
    $query = \Drupal::database()->select('hackathon_final_submission_project_files');
    $query->fields('hackathon_final_submission_project_files');
    $query->condition('literature_submission_id', $submission_id);
    $project_files_data = $query->execute()->fetchObject();
    /*if(!$project_files_data){
     $output .= "<p>You have not yet uploaded your project files submission.</p>";
    }
    else{
       $output .= l('Edit Project Files', 'hackathon-submission/edit/project-files/' . $my_proposals_data->id);
    }*/
    if ($today > $final_submission_last_date) {
      // @FIXME
// l() expects a Url object, created from a route name or external URI.
// $action = l('Download Final Report', 'hackathon-submission/generate-report/final-submission/' . $final_submission_data->id);

      $creation_date = date('d-m-Y', $final_submission_data->creation_date);
    }
    else {
      if (!$final_submission_data) {
        // @FIXME
// l() expects a Url object, created from a route name or external URI.
// $action = l('Add Final Submission', 'hackathon-submission/add/final-submission') . ' | ' . l('Edit Project files', 'hackathon-submission/edit/project-files/' . $my_proposals_data->id);

        $creation_date = "Final Submission not received";
      }
      else {
        // @FIXME
// l() expects a Url object, created from a route name or external URI.
// $action = l('Download Final Report', 'hackathon-submission/generate-report/final-submission/' . $final_submission_data->id) . ' | ' . l('Edit Final Report', 'hackathon-submission/edit/final-submission/' . $final_submission_data->id) . ' | ' . l('Edit Project files', 'hackathon-submission/edit/project-files/' . $my_proposals_data->id);

        $creation_date = date('d-m-Y', $final_submission_data->creation_date);
      }
    }
    // @FIXME
// l() expects a Url object, created from a route name or external URI.
// $final_submission_rows[$final_submission_data->id] = [
//       $creation_date,
//       l($my_proposals_data->participant_name, 'user/' . $my_proposals_data->uid),
//       $my_proposals_data->circuit_name,
//       $action,
//     ];

    /* check if there are any pending proposals */
    //!$pending_rows
    $final_submission_header = [
      'Date of Submission',
      'Name',
      'Circuit Name',
      '',
    ];
    //$output = theme_table($pending_header, $pending_rows);

    // @FIXME
// theme() has been renamed to _theme() and should NEVER be called directly.
// Calling _theme() directly can alter the expected output and potentially
// introduce security issues (see https://www.drupal.org/node/2195739). You
// should use renderable arrays instead.
// 
// 
// @see https://www.drupal.org/node/2195739
// $output .= theme('table', [
//       'header' => $final_submission_header,
//       'rows' => $final_submission_rows,
//     ]);


    $output .= "<p>Literature Survey</p>";
    $my_proposal_rows = [];
    $last_date = "2021-06-21 23:59:59.0";
    if ($today > $last_date) {
      // @FIXME
// l() expects a Url object, created from a route name or external URI.
// $action = l('Download Literature Survey Report', 'hackathon-submission/generate-report/literature-survey/' . $my_proposals_data->id);

    }
    else {
      // @FIXME
// l() expects a Url object, created from a route name or external URI.
// $action = l('Download Literature Survey Report', 'hackathon-submission/generate-report/literature-survey/' . $my_proposals_data->id) . ' | ' . l('Edit', 'hackathon-submission/edit/literature-report/' . $my_proposals_data->id);

    }
    // @FIXME
// l() expects a Url object, created from a route name or external URI.
// $my_proposal_rows[$my_proposals_data->id] = [
//       date('d-m-Y', $my_proposals_data->creation_date),
//       l($my_proposals_data->participant_name, 'user/' . $my_proposals_data->uid),
//       $my_proposals_data->circuit_name,
//       $action,
//     ];

    /* check if there are any pending proposals */
    if (!$my_proposal_rows) {
      // @FIXME
// l() expects a Url object, created from a route name or external URI.
// \Drupal::messenger()->addStatus(t('You do not have any active submissions. To submit, click ') . l('here', 'hackathon-submission/add/literature-report'));

      return '';
    } //!$pending_rows
    $my_proposal_header = [
      'Date of Submission',
      'Name',
      'Circuit Name',
      '',
    ];
    //$output = theme_table($pending_header, $pending_rows);
    // @FIXME
// theme() has been renamed to _theme() and should NEVER be called directly.
// Calling _theme() directly can alter the expected output and potentially
// introduce security issues (see https://www.drupal.org/node/2195739). You
// should use renderable arrays instead.
// 
// 
// @see https://www.drupal.org/node/2195739
// $output .= theme('table', [
//       'header' => $my_proposal_header,
//       'rows' => $my_proposal_rows,
//     ]);

    return $output;
  }


function hackathon_submission_completed_circuits() {
  $connection = Database::getConnection();

  // Main query (fetch all rows)
  $query = $connection->select('hackathon_completed_circuits', 'hcc')
    ->fields('hcc');
  $records = $query->execute()->fetchAll();

  // Count total participants
  $i = count($records);

  $page_content = [];

  // Intro text
  $intro_text = "FOSSEE Project in collaboration with VLSI System Design (VSD) Corp. Pvt.
   Ltd and the Ministry of Education, Govt. of India conducted a 2-weeks high
    intensity eSim Circuit Design and Simulation Marathon using Skywater 130nm technology,
     a fully open source process design kit. Close to 3000+ students from all over India participated
      in this Marathon and close to 200+ students completed this marathon with brilliant circuit design ideas. 
      The following participants have successfully completed designing the circuits. More details about this event can be 
      found here: <a href='https://hackathon.fossee.in/esim/2021' target='_blank'>https://hackathon.fossee.in/esim/2021</a>.<hr>";

  $page_content[] = [
    '#type' => 'markup',
    '#markup' => $intro_text,
  ];

  $rows = [];

  foreach ($records as $completed) {

    // Fetch survey details
    $sub_query = $connection->select('hackathon_literature_survey', 'hls')
      ->fields('hls')
      ->condition('id', $completed->literature_survey_id);

    $survey_rows = $sub_query->execute()->fetchAll();

    foreach ($survey_rows as $row) {
      // Create circuit name link
      // $url = Url::fromUri('internal:/hackathon/download/completed-circuits/' . $completed->literature_survey_id);
      $url = Url::fromRoute(
  'hackathon_submission.download_hackathon_final_submission_form',
  ['submission_id' => $completed->id]
);
      $link = Link::fromTextAndUrl($row->circuit_name, $url)->toString();

      $rows[] = [
        $i,
        ['data' => ['#markup' => $link]],
        wordwrap($row->participant_name, 10, " ", false),
        $row->institute,
      ];

      $i--;
    }
  }

  // Table render array
  $table = [
    '#theme' => 'table',
    '#header' => [
      'No',
      'Circuit Name',
      'Participant Name',
      'Institute',
    ],
    '#rows' => $rows,
  ];

  $page_content[] = $table;

  return $page_content;
}



public function hackathon_submission_display_final_submissions() {
  $user = \Drupal::currentUser();

  // Force login.
  if ($user->isAnonymous()) {
    \Drupal::messenger()->addError(t('It is mandatory to login on this website to view all submissions. If you are a new user, please create an account first.'));

    $login_url = Url::fromRoute('user.login', [], [
      'query' => \Drupal::destination()->getAsArray(),
    ])->toString();

    return new RedirectResponse($login_url);
  }

  $rows = [];

  $connection = \Drupal::database();
  $query = $connection->select('hackathon_final_submission', 'hfs');
  $query->fields('hfs');
  $query->orderBy('id', 'DESC');
  $result = $query->execute();

  foreach ($result as $submission) {

    $survey_query = $connection->select('hackathon_literature_survey', 'hls');
    $survey_query->fields('hls');
    $survey_query->condition('id', $submission->literature_survey_id);
    $survey_data = $survey_query->execute()->fetchObject();

    if (!$survey_data) {
      continue;
    }

    // Action links.
    $actions = [];

    $actions[] = Link::fromTextAndUrl(
      t('Edit submission'),
      Url::fromUri('internal:/hackathon-submission/manage/edit-submission/' . $survey_data->id)
    )->toString();

    $actions[] = Link::fromTextAndUrl(
      t('Download Final Report'),
      Url::fromUri('internal:/hackathon-submission/generate-report/final-submission/' . $submission->id)
    )->toString();

    $actions[] = Link::fromTextAndUrl(
      t('View Submission Details'),
      Url::fromUri('internal:/hackathon-submission/view-details/final-submission/' . $survey_data->id)
    )->toString();

    $actions[] = Link::fromTextAndUrl(
      t('Download Project Files'),
      Url::fromUri('internal:/hackathon-submission/download/project-files/' . $survey_data->id)
    )->toString();

    $rows[] = [
      date('d-m-Y', $submission->creation_date),
      Link::fromTextAndUrl(
        $survey_data->participant_name,
        Url::fromRoute('entity.user.canonical', ['user' => $survey_data->uid])
      ),
      $survey_data->circuit_name,
      [
        'data' => [
          '#markup' => implode(' | ', $actions),
        ],
      ],
    ];
  }

  if (empty($rows)) {
    \Drupal::messenger()->addStatus(
      t('You do not have any active submissions. To submit, click @link.', [
        '@link' => Link::fromTextAndUrl(
          t('here'),
          Url::fromUri('internal:/hackathon-submission/add/literature-report')
        )->toString(),
      ])
    );
    return [];
  }

  return [
    '#markup' => '<p>' . t('Total number of submissions: @count', ['@count' => count($rows)]) . '</p>',
    'table' => [
      '#type' => 'table',
      '#header' => [
        t('Date of Submission'),
        t('Name'),
        t('Circuit Name'),
        t('Actions'),
      ],
      '#rows' => $rows,
      '#empty' => t('No submissions found.'),
    ],
  ];
}


public function hackathon_submission_display_all_submissions() {
  $user = \Drupal::currentUser();

  // Require login.
  if ($user->isAnonymous()) {
    \Drupal::messenger()->addError(t('It is mandatory to login on this website to view all submissions. If you are a new user, please create an account first.'));

    $login_url = Url::fromRoute('user.login', [], [
      'query' => \Drupal::destination()->getAsArray(),
    ])->toString();

    return new RedirectResponse($login_url);
  }

  $rows = [];
  $connection = \Drupal::database();

  $query = $connection->select('hackathon_literature_survey', 'hls');
  $query->fields('hls');
  $query->orderBy('id', 'DESC');
  $result = $query->execute();

  foreach ($result as $survey) {

    // Check if final submission exists (optional but preserved from original logic).
    $final_query = $connection->select('hackathon_final_submission', 'hfs');
    $final_query->fields('hfs');
    $final_query->condition('literature_survey_id', $survey->id);
    $final_submission = $final_query->execute()->fetchObject();

    // Action link.
    $action = Link::fromTextAndUrl(
      t('Download Literature Survey Report'),
      Url::fromUri('internal:/hackathon-submission/generate-report/literature-survey/' . $survey->id)
    )->toString();

    $rows[] = [
      date('d-m-Y', $survey->creation_date),
      Link::fromTextAndUrl(
        $survey->participant_name,
        Url::fromRoute('entity.user.canonical', ['user' => $survey->uid])
      ),
      $survey->circuit_name,
      [
        'data' => [
          '#markup' => $action,
        ],
      ],
    ];
  }

  if (empty($rows)) {
    \Drupal::messenger()->addStatus(
      t('You do not have any active submissions. To submit, click @link.', [
        '@link' => Link::fromTextAndUrl(
          t('here'),
          Url::fromUri('internal:/hackathon-submission/add/literature-report')
        )->toString(),
      ])
    );
    return [];
  }

  return [
    'summary' => [
      '#markup' => '<p>' . t('Total number of submissions: @count', [
        '@count' => count($rows),
      ]) . '</p>' .
      '<p>' . t('Click @link to download the Email IDs of the participants.', [
        '@link' => Link::fromTextAndUrl(
          t('here'),
          Url::fromUri('internal:/download-emails')
        )->toString(),
      ]) . '</p>',
    ],
    'table' => [
      '#type' => 'table',
      '#header' => [
        t('Date of Submission'),
        t('Name'),
        t('Circuit Name'),
        t('Actions'),
      ],
      '#rows' => $rows,
      '#empty' => t('No submissions found.'),
    ],
  ];
}

  public function hackathon_submission_download_emails() {
    $user = \Drupal::currentUser();
    /* get pending submissions to be approved */
    if ($user->uid == 0) {
      $msg = \Drupal::messenger()->addError(t('It is mandatory to login on this website to download the email IDs of the participants. If you are new user please create a new account first.'));
      //drupal_goto('/pssp');
      drupal_goto('user/login', [
        'query' => drupal_get_destination()
        ]);
      return $msg;
    }
    $root_path = hackathon_submission_files_path();
    $my_submission_rows = [];
    $query = \Drupal::database()->select('hackathon_literature_survey');
    $query->fields('hackathon_literature_survey');
    //$query->condition('approval_status', 2);
    //$query->condition('uid',$user->uid);
    $all_submissions_q = $query->execute();
    $participants_email_id_file = $root_path . "participants-emails.csv";
    //var_dump($participants_email_id_file);die;
    $fp = fopen($participants_email_id_file, "w");
    /* making the first row */
    $item = ["Email ID"];
    fputcsv($fp, $item);

    while ($row = $all_submissions_q->fetchObject()) {
      $item = [$row->participant_email];
      fputcsv($fp, $item);
    }
    fclose($fp);
    if ($participants_email_id_file) {
      ob_clean();
      header("Pragma: public");
      header("Expires: 0");
      header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
      header("Cache-Control: public");
      header("Content-Description: File Transfer");
      header('Content-Type: application/csv');
      header('Content-disposition: attachment; filename=email-ids.csv');
      header('Content-Length:' . filesize($participants_email_id_file));
      header("Content-Transfer-Encoding: binary");
      header('Expires: 0');
      header('Pragma: no-cache');
      readfile($participants_email_id_file);
      /*ob_end_flush();
            ob_clean();
            flush();*/
    }
  }


    public function hackathon_submission_download_project_files($submission_id) {
    $current_user = $this->currentUser();
$database = \Drupal::database();
    $root_path = hackathon_submission_files_path();

    /* Fetch literature submission */
    $submission_data = $database->select('hackathon_literature_survey', 'hls')
      ->fields('hls')
      ->condition('id', $submission_id)
      ->execute()
      ->fetchObject();

    if (!$submission_data) {
      throw new NotFoundHttpException('Invalid submission.');
    }

    /* Fetch final submission */
    $final_submission_data = $database->select('hackathon_final_submission', 'hfs')
      ->fields('hfs')
      ->condition('literature_survey_id', $submission_id)
      ->execute()
      ->fetchObject();

    $directory_path = $submission_data->directory_name . '/';

    /* Zip folder */
    $zip_files_path = $root_path . 'zip_files/';
    if (!is_dir($zip_files_path)) {
      mkdir($zip_files_path, 0775, TRUE);
    }

    $zip_filename = $zip_files_path . $submission_data->id . '.zip';
    $zip = new \ZipArchive();
    $zip->open($zip_filename, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);

    /* Add project files */
    $project_files_q = $database->select('hackathon_final_submission_project_files', 'pf')
      ->fields('pf')
      ->condition('literature_submission_id', $submission_id)
      ->execute();

    while ($file = $project_files_q->fetchObject()) {
      $zip->addFile(
        $root_path . $file->filepath,
        $directory_path . str_replace(' ', '_', basename($file->filename))
      );
    }

    /* Ensure PDF reports exist */
    $final_report = $submission_data->id . '_final_submission_report.pdf';
    $literature_report = $submission_data->id . '_literature_survey_report.pdf';

    if (!file_exists($root_path . 'latex/' . $final_report)) {
      generate_fs_latex_files($final_submission_data->id);
    }
    if (!file_exists($root_path . 'latex/' . $literature_report)) {
      generate_ls_latex_files($submission_data->id);
    }

    $zip->addFile($root_path . 'latex/' . $final_report, $final_report);
    $zip->addFile($root_path . 'latex/' . $literature_report, $literature_report);

    $zip_file_count = $zip->numFiles;
    $zip->close();

    if ($zip_file_count === 0) {
      $this->messenger()->addError($this->t('There are no files to download.'));
      return new RedirectResponse('/hackathon-submission/all-submissions');
    }

    /* Stream ZIP file */
    $response = new BinaryFileResponse($zip_filename);
    $response->setContentDisposition(
      ResponseHeaderBag::DISPOSITION_ATTACHMENT,
      str_replace(' ', '_', $submission_data->id) . '.zip'
    );
    $response->deleteFileAfterSend(TRUE);

    return $response;
  }




public function hackathon_submission_download_completed_circuit($submission_id) {

  $submission_id = (int) $submission_id;

  $current_user = \Drupal::currentUser();
      $root_path = \Drupal::service("hackathon_submission_global")->hackathon_submission_files_path();
// var_Dump($submission_id);die;

  // Get submission data
  $submission_data = \Drupal::database()->select('hackathon_literature_survey', 'hls')
    ->fields('hls')
    ->condition('id', $submission_id)
    ->execute()
    ->fetchObject();

  if (!$submission_data) {
    throw new NotFoundHttpException('Submission not found');
  }

  // var_dump($submission_data);die;
  $directory_path = $submission_data->directory_name . '/';

  // Get final submission data
  $final_submission_data = \Drupal::database()->select('hackathon_final_submission', 'hfs')
    ->fields('hfs')
    ->condition('literature_survey_id', $submission_id)
    ->execute()
    ->fetchObject();

  // Create ZIP file
  $zip_filename = $root_path . $submission_data->id . '.zip';
// var_dump($root_path);die;
  $zip = new \ZipArchive();
  if ($zip->open($zip_filename, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== TRUE) {
    throw new NotFoundHttpException('Unable to create ZIP file');
  }
// var_dump($zip_filename);die;
  // Add project files
  $project_files = \Drupal::database()->select('hackathon_final_submission_project_files', 'hfspf')
    ->fields('hfspf')
    ->condition('literature_submission_id', $submission_id)
    ->execute();

  foreach ($project_files as $file) {
    $file_path = $root_path . $file->filepath;

    if (file_exists($file_path)) {
      $zip->addFile(
        $file_path,
        $directory_path . str_replace(' ', '_', basename($file->filename))
      );
    }
  }

  // Add final report
  $final_report = $submission_data->id . '_final_submission_report.pdf';
  $final_report_path = $root_path . 'latex/' . $final_report;

  if (!file_exists($final_report_path) && $final_submission_data) {
    generate_fs_latex_files($final_submission_data->id);
  }

  if (file_exists($final_report_path)) {
    $zip->addFile($final_report_path, $final_report);
  }

  $zip_file_count = $zip->numFiles;
  $zip->close();

  // If no files
  if ($zip_file_count === 0) {
    \Drupal::messenger()->addError('There are no files in this circuit to download');

  }

  // Prepare download response
  $response = new BinaryFileResponse($zip_filename);

  $download_name = str_replace(' ', '_', $submission_data->circuit_name) . '.zip';

  $response->setContentDisposition(
    ResponseHeaderBag::DISPOSITION_ATTACHMENT,
    $download_name
  );

  $response->headers->set('Content-Type', 'application/zip');

  // Delete file after sending
  $response->deleteFileAfterSend(TRUE);

  return $response;
}
// function hackathon_submission_download_completed_circuit() {
//  $submission_id = \Drupal::routeMatch()->getParameter('submission_id');

//   $connection = Database::getConnection();

//   // Fetch completed circuits.
//   $records = $connection->select('hackathon_completed_circuits', 'hcc')
//     ->fields('hcc')
//     ->execute()
//     ->fetchAll();

//   $count = count($records);
//   $page_content = [];

//   // Intro text.
//   $page_content[] = [
//     '#type' => 'markup',
//     '#markup' => '
//       FOSSEE Project in collaboration with VLSI System Design (VSD) Corp. Pvt. Ltd and
//       the Ministry of Education, Govt. of India conducted a 2-weeks high intensity eSim
//       Circuit Design and Simulation Marathon using Skywater 130nm technology, a fully
//       open source process design kit. Close to 3000+ students from all over India
//       participated in this Marathon and close to 200+ students completed this marathon
//       with brilliant circuit design ideas. The following participants have successfully
//       completed designing the circuits. More details about this event can be found here:
//       <a href="https://hackathon.fossee.in/esim/2021" target="_blank">
//         https://hackathon.fossee.in/esim/2021
//       </a>.
//       <hr>',
//   ];

//   $rows = [];

//   foreach ($records as $completed) {

//     if (empty($completed->literature_survey_id)) {
//       continue;
//     }

//     // Fetch literature survey details.
//     $survey_rows = $connection->select('hackathon_literature_survey', 'hls')
//       ->fields('hls')
//       ->condition('id', $completed->literature_survey_id)
//       ->execute()
//       ->fetchAll();

//     foreach ($survey_rows as $row) {

//       // Circuit name link.
//       $url = Url::fromRoute(
//         'hackathon_submission.download_hackathon_final_submission_form',
//         ['submission_id' => (int) $completed->literature_survey_id]
//       );

//       $link = Link::fromTextAndUrl($row->circuit_name, $url)->toRenderable();

//       $rows[] = [
//         $count,
//         ['data' => $link],
//         wordwrap($row->participant_name, 10, ' ', FALSE),
//         $row->institute,
//       ];

//       $count--;
//     }
//   }

//   // Table render array.
//   $page_content[] = [
//     '#theme' => 'table',
//     '#header' => [
//       'No',
//       'Circuit Name',
//       'Participant Name',
//       'Institute',
//     ],
//     '#rows' => $rows,
//     '#empty' => t('No completed circuits found.'),
//   ];

//   return $page_content;
// }



  public function create_zip_of_all_files() {
    $root_path = hackathon_submission_files_path();
    $query = \Drupal::database()->query("SELECT p.literature_submission_id, l.circuit_name, l.participant_name
            FROM hackathon_final_submission_project_files as p
            LEFT JOIN hackathon_literature_survey as l
            ON l.id = p.literature_submission_id where p.literature_submission_id in 
            (select l.id from hackathon_final_submission as f
            LEFT JOIN hackathon_literature_survey as l
            ON l.id = f.literature_survey_id)
            group by p.literature_submission_id
            order by p.literature_submission_id");
    //var_dump($query->rowCount());die;
    $pathdir = $root_path . 'zip_files/';
    $zipcreated = $root_path . 'Allfiles.zip';
    //var_dump($zipcreated);die;
    // Create new zip class
    $zip = new \ZipArchive();
    $zip->open($zipcreated, \ZipArchive::CREATE);
    while ($result = $query->fetchObject()) {
      if (!file_exists($root_path . 'zip_files/' . $result->literature_submission_id . '.zip')) {
        hackathon_submission_download_all_files($result->literature_submission_id);
      }
      $zip->addFile($root_path . 'zip_files/' . $result->literature_submission_id . '.zip', $result->literature_submission_id . '.zip');
    }
    $zip ->close();
    header('Content-Type: application/zip');
    header('Content-disposition: attachment; filename= Allfiles.zip');
    header('Content-Length: ' . filesize($zipcreated));
    ob_clean();
    readfile($zipcreated);

  }

public function download_literature_survey_report($submission_id) {
  $root_path = \Drupal::service("hackathon_submission_global")->hackathon_submission_files_path();
  $dir_path = $root_path . 'latex/';

  $connection = \Drupal::database();

  $submission_data = $connection->query(
    "SELECT * FROM {hackathon_literature_survey} WHERE id = :id",
    [':id' => $submission_id]
  )->fetchObject();

  if (!$submission_data) {
    \Drupal::messenger()->addError(t('Invalid submission specified.'));
    return new RedirectResponse(Url::fromRoute('<front>')->toString());
  }

  $pdf_path = $dir_path . $submission_data->id . '_literature_survey_report.pdf';

  // Remove existing PDF.
  if (file_exists($pdf_path)) {
    unlink($pdf_path);
  }

  // Circuit diagram.
  $circuit = $connection->query(
    "SELECT * FROM {hackathon_literature_survey_files}
     WHERE submission_id = :id AND filetype = :type",
    [':id' => $submission_data->id, ':type' => 'C']
  )->fetchObject();

  // Waveform.
  $waveform = $connection->query(
    "SELECT * FROM {hackathon_literature_survey_files}
     WHERE submission_id = :id AND filetype = :type",
    [':id' => $submission_data->id, ':type' => 'W']
  )->fetchObject();

  $circuit_diagram_path = $root_path . $circuit->filepath;
  $waveform_path = $root_path . $waveform->filepath;

  /* ---------- BibTeX ---------- */
  $bibscript = '';
  $eol = "\n";

  $bib_refs = $connection->query(
    "SELECT * FROM {hackathon_literature_survey_bib_references}
     WHERE submission_id = :id",
    [':id' => $submission_data->id]
  );

  while ($bib = $bib_refs->fetchObject()) {
    $bibscript .= '@MISC{' . $bib->id .
      ', author={' . hs_convert_special_characters($bib->resource_author) .
      '}, title={' . hs_convert_special_characters($bib->resource_title) .
      '}, howpublished={' . hs_convert_special_characters($bib->resource_link) .
      '}}' . $eol;
  }

  file_put_contents($dir_path . 'references.bib', $bibscript);

  /* ---------- TEX ---------- */
  $tex = $this->buildTexDocument($submission_data, $circuit_diagram_path, $waveform_path);
  $tex_file = $submission_data->id . '_literature_survey_report.tex';
  file_put_contents($dir_path . $tex_file, $tex);

  /* ---------- PDF GENERATION ---------- */
  chdir($root_path . 'latex');
  exec('/bin/bash pdf_creator.sh ' . $tex_file . ' ' . $submission_data->id . '_literature_survey_report');

  if (!file_exists($pdf_path) || filesize($pdf_path) === 0) {
    \Drupal::messenger()->addError(t('Error occurred when generating the PDF version of the report.'));
    return new RedirectResponse(Url::fromRoute('<front>')->toString());
  }

  /* ---------- DOWNLOAD ---------- */
  $response = new BinaryFileResponse($pdf_path);
  $response->setContentDisposition(
    ResponseHeaderBag::DISPOSITION_ATTACHMENT,
    basename($pdf_path)
  );
  $response->headers->set('Content-Type', 'application/pdf');

  return $response;
}
protected function buildTexDocument($data, $circuit_path, $waveform_path) {
  return '
\documentclass[10pt,twocolumn]{article}
\usepackage[english]{babel}
\usepackage[utf8]{inputenc}
\usepackage[T1]{fontenc}
\usepackage{graphicx}
\usepackage{geometry}
\geometry{a4paper, margin=2cm}
\title{' . hs_convert_special_characters($data->circuit_name) . '}
\author{' . hs_convert_special_characters($data->participant_name) . ', ' .
  hs_convert_special_characters($data->institute) . '}
\begin{document}
\maketitle
\begin{abstract}
' . hs_convert_special_characters($data->abstract) . '
\end{abstract}

\section{Reference Circuit Details}
' . hs_convert_special_characters($data->circuit_details) . '

\section{Reference Circuit}
\includegraphics[width=0.4\textwidth]{' . $circuit_path . '}

\section{Reference Circuit Waveforms}
\includegraphics[width=0.4\textwidth]{' . $waveform_path . '}

\nocite{*}
\bibliographystyle{ieee}
\bibliography{references}
\end{document}';
}

  public function download_final_submission_report() {
    // $final_submission_id = arg(3);
                $final_submission_id = \Drupal::routeMatch()->getParameter('final_submission_id');

    $root_path = \Drupal::service("hackathon_submission_global")->hackathon_submission_files_path();
    $dir_path = $root_path . "latex/";
    //var_dump($dir_path);die;
    $final_submission_circuit_filedata = "";
    $reference_filedata = "";
    $eol = "\n";
    $sep = "#";
    $final_submission_q = \Drupal::database()->query("SELECT * FROM {hackathon_final_submission} WHERE id = :id", [
      ':id' => $final_submission_id
      ]);
    $final_submission_data = $final_submission_q->fetchObject();
    $submission_q = \Drupal::database()->query("SELECT * FROM {hackathon_literature_survey} WHERE id = :id", [
      ':id' => $final_submission_data->literature_survey_id
      ]);
    $submission_data = $submission_q->fetchObject();
    if (!$final_submission_data) {
      \Drupal::messenger()->addError('Invalid submission specified.');
      // drupal_goto('');
    } //!$preference_data
    //$final_submission_circuit_filedata = $submission_data->circuit_name . $sep . $final_submission_data->participant_name . $sep . $final_submission_data->institute . $sep . $final_submission_data->abstract . $sep . $final_submission_data->circuit_details . $eol;
    /* check if book already generated */
    //var_dump(file_exists($dir_path . "book_" . $final_submission_data->id . ".pdf"));die;
    if (file_exists($dir_path . $submission_data->id . "_final_submission_report.pdf")) {
      // download PDF file 
      unlink($dir_path . $submission_data->id . "_final_submission_report.pdf");
    }//file_exists($dir_path . "book_" . $preference_data->id . ".pdf")
    $circuit_diagram_q = \Drupal::database()->query("SELECT * FROM {hackathon_final_submission_files} WHERE final_submission_id = :id and filetype = :filetype", [
      ':id' => $final_submission_data->id,
      ':filetype' => 'C',
    ]);
    $circuit_diagram_path = $root_path . $circuit_diagram_q->fetchObject()->filepath;
    $waveform_q = \Drupal::database()->query("SELECT * FROM {hackathon_final_submission_files} WHERE final_submission_id = :id and filetype = :filetype", [
      ':id' => $final_submission_data->id,
      ':filetype' => 'W',
    ]);
    $waveform_path = $root_path . $waveform_q->fetchObject()->filepath;

    $bib_references_q = \Drupal::database()->query("SELECT * FROM {hackathon_final_submission_bib_references} WHERE final_submission_id = :id", [
      ':id' => $final_submission_data->id
      ]);
    /*while ($final_submission_files_data = $final_submission_files_q->fetchObject())
        {
                $reference_filedata .= $final_submission_files_data->filename . $sep;
                $reference_filedata .= $final_submission_files_data->filepath . $sep;
                $reference_filedata .= $final_submission_files_data->filetype . $sep;
                $reference_filedata .= $sep;
                $reference_filedata .= $final_submission_files_data->id;
                $reference_filedata .= $eol;
        }*/ //$example_data = $example_q->fetchObject()
    /********************* Write to tex file ***********************/
    $bibscript = '';
    while ($bib_ref_data = $bib_references_q->fetchObject()) {
      $resource_link = hs_convert_special_characters($bib_ref_data->resource_link);
      $bibscript .= '@MISC{' . $bib_ref_data->id . ', author={' . hs_convert_special_characters($bib_ref_data->resource_author) . '}, title={' . hs_convert_special_characters($bib_ref_data->resource_title) . '}, howpublished={' . $resource_link . '}}' . $eol;
    }

    $circuit_details = hs_convert_special_characters($final_submission_data->circuit_details);
    $abstract = hs_convert_special_characters($final_submission_data->abstract);
    $circuit_name = hs_convert_special_characters($submission_data->circuit_name);
    $participant_name = hs_convert_special_characters($submission_data->participant_name);
    $participant_institute = hs_convert_special_characters($submission_data->institute);

    $bib_fn = "references.bib";
    $bib_file = fopen($dir_path . $bib_fn, "w");
    fwrite($bib_file, $bibscript);
    fclose($bib_file);


    $texscript = '
\documentclass[10pt,twocolumn,letterpaper]{article}
%%
%   Template taken from Overleaf
%   Fill in details where prompted
%
%% Language and font encodings
\usepackage[english]{babel}
\usepackage[utf8]{inputenc}
\usepackage[T1]{fontenc}
%\usepackage{url}
\usepackage{amssymb}
\usepackage{mathptmx}
\usepackage[mathletters]{ucs}
%% Sets page size and margins
\usepackage[a4paper,top=1cm,bottom=2cm,left=2cm,right=2cm,marginparwidth=1.75cm]{geometry}
\usepackage{float}
%% Useful packages
\usepackage{amsmath}
\usepackage{graphicx}
%\usepackage[backend=bibtex]{biblatex}
%\usepackage{ieee}
%\usepackage[colorinlistoftodos]{todonotes}
%\usepackage[colorlinks=true, allcolors=blue]{hyperref}
%\usepackage{underscore}
\usepackage{textcomp}

\title{ ' . $circuit_name . ' }

\usepackage{authblk}
\author[1]{' . $participant_name . ', ' . $participant_institute . '}

\begin{document}
\maketitle

\selectlanguage{english}
\begin{abstract}
' . $abstract . '
\end{abstract}


\section{Circuit Details}

' . $circuit_details . '

\section{Implemented Circuit}
\begin{figure}[H]
\centering
\includegraphics[width=0.4\textwidth]{' . $circuit_diagram_path . '}
\caption{\label{fig:RefCktDiagram} Implemented circuit diagram.}
%~\cite{001}}
\end{figure}

\section{Implemented Waveforms}
\begin{figure}[H]
\centering
\includegraphics[width=0.4\textwidth]{' . $waveform_path . '}
\caption{\label{fig:RefWaveform}Implemented waveform.}
%~\cite{6556063}}
\end{figure}

\nocite{*}

\bibliographystyle{ieee}
%\addbibresource{references.bib}
\bibliography{references}

\end{document}';

    //write code to file
    $fn = $submission_data->id . "_final_submission_report.tex";
    $myfile = fopen($dir_path . $fn, "w");
    fwrite($myfile, $texscript);
    fclose($myfile);

    chdir("esim_uploads/hackathon_submission_uploads");
    chdir("latex");
    $ref_fn = $submission_data->id . "_final_submission_report";

    $sh_command = "/bin/bash pdf_creator.sh " . $fn . " " . $ref_fn;
    exec($sh_command);
    $download_filename = $submission_data->id . "_final_submission_report.pdf";

    if (filesize($dir_path . $submission_data->id . "_final_submission_report.pdf") == TRUE) {
      ob_clean();
      header("Pragma: public");
      header("Expires: 0");
      header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
      header("Cache-Control: public");
      header("Content-Description: File Transfer");
      header('Content-Type: application/pdf');
      header('Content-disposition: attachment; filename=' . $download_filename);
      header('Content-Length: ' . filesize($dir_path . $submission_data->id . "_final_submission_report.pdf"));
      header("Content-Transfer-Encoding: binary");
      header('Expires: 0');
      header('Pragma: no-cache');
      @readfile($dir_path . $submission_data->id . "_final_submission_report.pdf");
      ob_end_flush();
      ob_clean();
      flush();
    } //filesize($dir_path . $pdf_filename) == TRUE
    else {
      \Drupal::messenger()->addError("Error occurred when generating the PDF version of the report.");
      // drupal_goto('');
      return;
    }

  }


  public function add_literature_report_submission() {
    $user = \Drupal::currentUser();
    if ($user->uid == 0) {
      $msg = \Drupal::messenger()->addError(t('It is mandatory to log in on this website to upload your submission. If you are new user please create a new account first.'));
      //drupal_goto('esim-circuit-simulation-project');
      drupal_goto('user/login', [
        'query' => drupal_get_destination()
        ]);
      return $msg;
    } //$user->uid == 0
    $today = date("Y-m-d H:i:s");
    //var_dump($today);die;
    $start_date = "2021-06-17 23:59:59.0";
    $last_date = "2021-06-21 23:59:59.0";
    $return_html = '';
    if ($today < $start_date) {
      $return_html .= '<p>You can submit your Literature Survey report at anytime between 18-06-2021, 12 AM and 21-06-2021, 23:59 PM.</p>';
    }
    elseif ($today > $last_date) {
      $return_html .= '<p>Literature Survey Submissions are closed.</p>';
    }
    else {
      $submission_form = \Drupal::formBuilder()->getForm("add_literature_report_submission_form");
      $return_html .= \Drupal::service("renderer")->render($submission_form);
    }
    return $return_html;
  }

  public function add_final_submission_report_submission() {
    $user = \Drupal::currentUser();
    if ($user->uid == 0) {
      $msg = \Drupal::messenger()->addError(t('It is mandatory to log in on this website to upload your submission. If you are new user please create a new account first.'));
      //drupal_goto('esim-circuit-simulation-project');
      drupal_goto('user/login', [
        'query' => drupal_get_destination()
        ]);
      return $msg;
    } //$user->uid == 0
    $query = \Drupal::database()->select('hackathon_literature_survey');
    $query->fields('hackathon_literature_survey');
    $query->condition('uid', $user->uid);
    //$query->condition('id', $submission_id);
    //$query->range(0, 1);
    $submission_q = $query->execute();
    $literature_submission_data = $submission_q->fetchObject();
    if ($literature_submission_data) {
      $today = date("Y-m-d H:i:s");
      //var_dump($today);die;
      $start_date = "2021-06-27 23:59:59.0";
      $last_date = "2021-06-30 23:59:59.0";
      $return_html = '';
      if ($today < $start_date) {
        $return_html .= '<p>You can submit your Final report at anytime between 28-06-2021, 12 AM and 30-06-2021, 23:59 PM.</p>';
      }
      elseif ($today > $last_date) {
        $return_html .= '<p>Final Report Submissions are closed.</p>';
      }
      else {
        $submission_form = \Drupal::formBuilder()->getForm("add_final_submission_report_submission_form");
        $return_html .= \Drupal::service("renderer")->render($submission_form);
      }
      return $return_html;
    }
    else {
      \Drupal::messenger()->addError('We regret to inform that we have not received your literature survey report');
      drupal_goto('');
    }
  }

  public function add_project_files_submission() {
    $user = \Drupal::currentUser();
    if ($user->uid == 0) {
      $msg = \Drupal::messenger()->addError(t('It is mandatory to log in on this website to upload your project files. If you are new user please create a new account first.'));
      //drupal_goto('esim-circuit-simulation-project');
      drupal_goto('user/login', [
        'query' => drupal_get_destination()
        ]);
      return $msg;
    } //$user->uid == 0
    $query = \Drupal::database()->select('hackathon_literature_survey');
    $query->fields('hackathon_literature_survey');
    $query->condition('uid', $user->uid);
    //$query->condition('id', $submission_id);
    //$query->range(0, 1);
    $submission_q = $query->execute();
    $literature_submission_data = $submission_q->fetchObject();
    if ($literature_submission_data) {
      $today = date("Y-m-d H:i:s");
      //var_dump($today);die;
      $start_date = "2021-06-27 23:59:59.0";
      $last_date = "2021-06-30 23:59:59.0";
      $return_html = '';
      if ($today < $start_date) {
        $return_html .= '<p>You can upload your project files anytime between 28-06-2021, 12 AM and 30-06-2021, 23:59 PM.</p>';
      }
      elseif ($today > $last_date) {
        $return_html .= '<p>Submissions are closed.</p>';
      }
      else {
        $submission_form = \Drupal::formBuilder()->getForm("hackathon_submission_add_project_files_form");
        $return_html .= \Drupal::service("renderer")->render($submission_form);
      }
      return $return_html;
    }
    else {
      \Drupal::messenger()->addError('We regret to inform that we have not received your literature survey report');
      drupal_goto('');
    }
  }

  public function add_mscd_literature_report_submission() {
    $user = \Drupal::currentUser();
    if ($user->uid == 0) {
      $msg = \Drupal::messenger()->addError(t('It is mandatory to log in on this website to upload your submission. If you are new user please create a new account first.'));
      //drupal_goto('esim-circuit-simulation-project');
      drupal_goto('user/login', [
        'query' => drupal_get_destination()
        ]);
      return $msg;
    } //$user->uid == 0
    $today = date("Y-m-d H:i:s");
    //var_dump($today);die;
    // @FIXME
// // @FIXME
// // This looks like another module's variable. You'll need to rewrite this call
// // to ensure that it uses the correct configuration object.
// $start_date = variable_get('mscd_literature_report_start_date', '');

    // @FIXME
// // @FIXME
// // This looks like another module's variable. You'll need to rewrite this call
// // to ensure that it uses the correct configuration object.
// $last_date = variable_get('mscd_literature_report_last_date', '');

    $return_html = '';
    //var_dump(date("d-m-Y, H:i A", strtotime($start_date)));die;
    if ($today < $start_date) {
      $return_html .= '<p>You can submit your Literature Survey report at anytime between ' . date("d-m-Y, H:i A", strtotime($start_date)) . ', and ' . date("d-m-Y, H:i A", strtotime($last_date)) . '.</p>';
    }
    elseif ($today > $last_date) {
      $return_html .= '<p>Literature Survey Submissions are closed.</p>';
    }
    else {
      $submission_form = \Drupal::formBuilder()->getForm("add_mixed_signal_marathon_literature_report_form");
      $return_html .= \Drupal::service("renderer")->render($submission_form);
    }
    return $return_html;
  }

  public function add_mscd_final_report_submission() {
    $user = \Drupal::currentUser();
    if ($user->uid == 0) {
      $msg = \Drupal::messenger()->addError(t('It is mandatory to log in on this website to upload your submission. If you are new user please create a new account first.'));
      //drupal_goto('esim-circuit-simulation-project');
      drupal_goto('user/login', [
        'query' => drupal_get_destination()
        ]);
      return $msg;
    } //$user->uid == 0
    $query = \Drupal::database()->select('mixed_signal_marathon_literature_survey');
    $query->fields('mixed_signal_marathon_literature_survey');
    $query->condition('uid', $user->uid);
    //$query->condition('id', $submission_id);
    //$query->range(0, 1);
    $submission_q = $query->execute();
    $literature_submission_data = $submission_q->fetchObject();
    if ($literature_submission_data) {
      $today = date("Y-m-d H:i:s");
      //var_dump($today);die;
      // @FIXME
// // @FIXME
// // This looks like another module's variable. You'll need to rewrite this call
// // to ensure that it uses the correct configuration object.
// $start_date = variable_get('mscd_final_submission_start_date', '');

      // @FIXME
// // @FIXME
// // This looks like another module's variable. You'll need to rewrite this call
// // to ensure that it uses the correct configuration object.
// $last_date = variable_get('mscd_final_submission_last_date', '');

      $return_html = '';
      if ($today < $start_date) {
        $return_html .= '<p>You can submit your Literature Survey report at anytime between ' . date("d-m-Y, H:i A", strtotime($start_date)) . ', and ' . date("d-m-Y, H:i A", strtotime($last_date)) . '.</p>';
      }
      elseif ($today > $last_date) {
        $return_html .= '<p>Final Report Submissions are closed.</p>';
      }
      else {
        $submission_form = \Drupal::formBuilder()->getForm("add_mscd_final_report_submission_form");
        $return_html .= \Drupal::service("renderer")->render($submission_form);
      }
      return $return_html;
    }
    else {
      \Drupal::messenger()->addError('We regret to inform that we have not received your literature survey report');
      drupal_goto('');
    }
  }


  public function mscd_submission_display_my_submissions() {
  $user = \Drupal::currentUser();

  // User must be logged in.
  if ($user->isAnonymous()) {
    \Drupal::messenger()->addError($this->t('It is mandatory to login to view your submissions.'));
    return new RedirectResponse(Url::fromRoute('user.login')->toString());
  }

  $connection = Database::getConnection();

  // Fetch latest literature survey
  $literature = $connection->select('mixed_signal_marathon_literature_survey', 'ls')
    ->fields('ls')
    ->condition('uid', $user->id())
    ->orderBy('id', 'DESC')
    ->range(0, 1)
    ->execute()
    ->fetchObject();

  if (!$literature) {
    \Drupal::messenger()->addError($this->t('We have not received your submission.'));
    return [];
  }

  // Fetch final submission
  $final = $connection->select('mixed_signal_marathon_final_submission', 'fs')
    ->fields('fs')
    ->condition('uid', $user->id())
    ->condition('literature_survey_id', $literature->id)
    ->orderBy('id', 'DESC')
    ->range(0, 1)
    ->execute()
    ->fetchObject();

  // Dates
  $literature_date = date('d-m-Y', $literature->creation_date);
  $final_date = $final && $final->creation_date
    ? date('d-m-Y', $final->creation_date)
    : $this->t('Not yet uploaded');

  // Literature actions
  $literature_actions = Link::fromTextAndUrl(
    $this->t('View'),
    Url::fromRoute(
      'hackathon_submission.view_literature_report',
      ['submission_id' => $literature->id]
    )
  )->toString();

  // Final submission actions
  if ($final) {
    $final_actions = Link::fromTextAndUrl(
      $this->t('View'),
      Url::fromRoute(
        'hackathon_submission.view_final_submission',
        ['submission_id' => $literature->id]
      )
    )->toString();
  }
  else {
    $final_actions = Link::fromTextAndUrl(
      $this->t('Add Final Submission'),
      Url::fromRoute('hackathon_submission.add_final_submission')
    )->toString();
  }

  // Table rows
  $rows[] = [
    $literature_date,
    $final_date,
    Link::fromTextAndUrl(
      $literature->participant_name,
      Url::fromRoute('entity.user.canonical', ['user' => $literature->uid])
    )->toString(),
    $literature->circuit_name,
    ['data' => ['#markup' => $literature_actions]],
    ['data' => ['#markup' => $final_actions]],
  ];

  // Render table
  return [
    '#theme' => 'table',
    '#header' => [
      $this->t('Literature Survey Date'),
      $this->t('Final Submission Date'),
      $this->t('Name'),
      $this->t('Circuit Name'),
      $this->t('Literature Survey'),
      $this->t('Final Submission'),
    ],
    '#rows' => $rows,
    '#empty' => $this->t('No submissions found.'),
  ];
}


public function mscd_display_final_submissions() {
  $current_user = \Drupal::currentUser();

  // Require login.
  if (!$current_user->isAuthenticated()) {
    \Drupal::messenger()->addError(t('It is mandatory to login on this website to view all submissions. If you are a new user, please create an account first.'));

    $destination = \Drupal::request()->getRequestUri();
    return new RedirectResponse(
      Url::fromRoute('user.login', [], ['query' => ['destination' => $destination]])->toString()
    );
  }

  $rows = [];

  $query = \Drupal::database()->select('mixed_signal_marathon_final_submission', 'f');
  $query->fields('f');
  $query->orderBy('id', 'DESC');
  $result = $query->execute();

  foreach ($result as $submission) {

    $ls_query = \Drupal::database()->select('mixed_signal_marathon_literature_survey', 'l');
    $ls_query->fields('l');
    $ls_query->condition('id', $submission->literature_survey_id);
    $final_submission = $ls_query->execute()->fetchObject();

    if (!$final_submission) {
      continue;
    }

    // Participant profile link.
    $user_link = Link::fromTextAndUrl(
      $final_submission->participant_name,
      Url::fromRoute('entity.user.canonical', ['user' => $final_submission->uid])
    )->toString();

    // View submission link.
    $view_link = Link::fromTextAndUrl(
      t('View'),
      Url::fromUri('internal:/mixed-signal-design-marathon/view/final-submission/' . $final_submission->id)
    )->toString();

    $rows[] = [
      date('d-m-Y', $submission->creation_date),
      $user_link,
      $final_submission->circuit_name,
      $view_link,
    ];
  }

  if (empty($rows)) {
    \Drupal::messenger()->addStatus(t('There are no active submissions.'));
    return [];
  }

  return [
    '#markup' => '<p>' . t('Total number of submissions: @count', ['@count' => count($rows)]) . '</p>',
    'table' => [
      '#type' => 'table',
      '#header' => [
        t('Date of Submission'),
        t('Name'),
        t('Circuit Name'),
        t('Final Submission'),
      ],
      '#rows' => $rows,
      '#empty' => t('No submissions available.'),
    ],
  ];
}

public function mscd_display_literature_submissions() {
  $current_user = \Drupal::currentUser();

  // Require login.
  if (!$current_user->isAuthenticated()) {
    \Drupal::messenger()->addError(t('It is mandatory to login on this website to view all submissions. If you are a new user, please create an account first.'));

    $destination = \Drupal::request()->getRequestUri();
    return new RedirectResponse(
      Url::fromRoute('user.login', [], ['query' => ['destination' => $destination]])->toString()
    );
  }

  $rows = [];

  $query = \Drupal::database()->select('mixed_signal_marathon_literature_survey', 'l');
  $query->fields('l');
  $query->orderBy('id', 'DESC');
  $result = $query->execute();

  foreach ($result as $record) {

    // User profile link.
    $user_link = Link::fromTextAndUrl(
      $record->participant_name,
      Url::fromRoute('entity.user.canonical', ['user' => $record->uid])
    )->toString();

    // Literature survey view link.
    $view_link = Link::fromTextAndUrl(
      t('View'),
      Url::fromUri('internal:/mixed-signal-design-marathon/view/literature-report/' . $record->id)
    )->toString();

    $rows[] = [
      date('d-m-Y', $record->creation_date),
      $user_link,
      $record->circuit_name,
      $view_link,
    ];
  }

  if (empty($rows)) {
    \Drupal::messenger()->addStatus(t('There are no active submissions yet.'));
    return [];
  }

  return [
    '#markup' => '<p>' . t('Total number of submissions: @count', ['@count' => count($rows)]) . '</p>
                  <p>' . t('Click') . ' <a href="' . Url::fromUri('internal:/download-emails')->toString() . '">' . t('here') . '</a> ' . t('to download the Email IDs of the participants.') . '</p>',
    'table' => [
      '#type' => 'table',
      '#header' => [
        t('Date of Submission'),
        t('Name'),
        t('Circuit Name'),
        t('Literature Survey'),
      ],
      '#rows' => $rows,
      '#empty' => t('No submissions available.'),
    ],
  ];
}

public function mscd_download_literature_report() {
  $submission_id = \Drupal::routeMatch()->getParameter('submission_id');

  // Load submission data.
  $submission_data = \Drupal::database()
    ->select('mixed_signal_marathon_literature_survey', 'm')
    ->fields('m')
    ->condition('id', $submission_id)
    ->execute()
    ->fetchObject();

  if (!$submission_data) {
    $this->messenger()->addError($this->t('Invalid submission.'));
    return $this->redirect('hackathon_submission.soc_marathon_display_final_submissions');
  }

  $root_path = mscd_hackathon_submission_files_path();
  $file_path = $root_path . '/' . $submission_data->directory_name . '/' . $submission_data->report_file;

  if (!file_exists($file_path)) {
    $this->messenger()->addError($this->t('File not found.'));
    return $this->redirect('hackathon_submission.soc_marathon_display_final_submissions');
  }

  // Return file download response.
  $response = new BinaryFileResponse($file_path);
  $response->setContentDisposition(
    ResponseHeaderBag::DISPOSITION_ATTACHMENT,
    $submission_data->report_file
  );
  $response->headers->set('Content-Type', 'application/pdf');

  return $response;
}


public function mscd_download_final_submission() {

  $submission_id = \Drupal::routeMatch()->getParameter('submission_id'); 
  // Adjust if your route uses a different parameter name

  if (!$submission_id) {
    \Drupal::messenger()->addError("Invalid submission ID.");
    return $this->redirect('hackathon_submission.mscd_hackathon_submission_download_completed_circuit'); // update route as needed
  }

  $root_path = mscd_hackathon_submission_files_path();

  // Get submission data
  $connection = \Drupal::database();
  $query = $connection->select('mixed_signal_marathon_literature_survey', 'l');
  $query->fields('l');
  $query->condition('id', $submission_id);
  $submission_data = $query->execute()->fetchObject();

  if (!$submission_data) {
    \Drupal::messenger()->addError("Submission not found.");
    return $this->redirect('<front>');
  }

  // Directory inside ZIP
  $directory_path = $submission_data->directory_name . '/';

  // Get files
  $query = $connection->select('mixed_signal_marathon_final_submission_files', 'f');
  $query->fields('f');
  $query->condition('literature_survey_id', $submission_id);
  $final_submission_q = $query->execute();

  // Build ZIP
  $zip_filename = $root_path . str_replace(' ', '_', $submission_data->circuit_name) . '.zip';
  $zip = new \ZipArchive();

  if ($zip->open($zip_filename, \ZipArchive::CREATE) !== TRUE) {
    \Drupal::messenger()->addError("Unable to create ZIP file.");
    return $this->redirect('<front>');
  }

  while ($file = $final_submission_q->fetchObject()) {
    if (file_exists($root_path . $file->filepath)) {
      $zip->addFile(
        $root_path . $file->filepath,
        $directory_path . str_replace(' ', '_', basename($file->filename))
      );
    }
  }

  $zip->close();

  // If ZIP is empty
  if (filesize($zip_filename) == 0) {
    unlink($zip_filename);
    \Drupal::messenger()->addError("There are no files in this circuit to download.");
    // return $this->redirect('mixed_signal_marathon.dashboard');
        return $this->redirect('hackathon_submission.mscd_hackathon_submission_download_completed_circuit');

  }

  // Return ZIP as a download
  $response = new BinaryFileResponse($zip_filename);
  $response->setContentDisposition(
    ResponseHeaderBag::DISPOSITION_ATTACHMENT,
    str_replace(' ', '_', $submission_data->circuit_name) . '.zip'
  );

  // Delete the ZIP after response
  $response->deleteFileAfterSend(true);

  return $response;
}


function mscd_hackathon_submission_download_completed_circuit() {

  $connection = \Drupal::database();

  // Load approved final submissions.
  $result = $connection->select('mixed_signal_marathon_final_submission', 'msm')
    ->fields('msm')
    ->condition('approval_status', 3)
    ->orderBy('id', 'DESC')
    ->execute()
    ->fetchAll();

  // Intro message.
  $intro = "
    FOSSEE, IIT Bombay, along with VLSI System Design Corp. Pvt. Ltd and Redwood EDA 
    conducted a 3-weeks high intensity eSim Mixed Signal Circuit Design and Simulation Marathon. 
    Close to 1700+ students from all over India participated in this Marathon and close to 
    60+ students completed this marathon with brilliant circuit design ideas. 
    The following participants have successfully completed designing the circuits. 
    More details can be found here: 
    <a href='https://hackathon.fossee.in/esim/feb22/' target='_blank'>
      https://hackathon.fossee.in/esim/feb22/
    </a>.
    <hr>
  ";

  // If no records found.
  if (empty($result)) {
    return [
      '#type' => 'markup',
      '#markup' => $intro,
    ];
  }

  $rows = [];
  $i = count($result);

  foreach ($result as $submission) {

    // Get literature survey data.
    $lit = $connection->select('mixed_signal_marathon_literature_survey', 'ls')
      ->fields('ls')
      ->condition('id', $submission->literature_survey_id)
      ->execute()
      ->fetchObject();

    if (!$lit) {
      continue;
    }

    // Create download link.
    $url = Url::fromUri('internal:/mixed-signal-design-marathon/download/circuits/' . $lit->id);
    $link = Link::fromTextAndUrl($lit->circuit_name, $url)->toString();

    $rows[] = [
      $i,
      ['data' => ['#markup' => $link]],
      $lit->participant_name,
      $lit->institute,
    ];

    $i--;
  }

  if (empty($rows)) {
    \Drupal::messenger()->addStatus("There are no active submissions.");
    return [];
  }

  // Return full render array.
  return [
    'intro_text' => [
      '#type' => 'markup',
      '#markup' => $intro,
    ],

    'submission_table' => [
      '#type' => 'table',
      '#header' => [
        'S.No',
        'Circuit Name',
        'Participant Name',
        'University',
      ],
      '#rows' => $rows,
      '#attributes' => ['class' => ['mscd-submission-table']],
    ],
  ];
}

  public function mscd_download_emails() {
    $user = \Drupal::currentUser();
    /* get pending submissions to be approved */
    if ($user->uid == 0) {
      $msg = \Drupal::messenger()->addError(t('It is mandatory to login on this website to download the email IDs of the participants. If you are new user please create a new account first.'));
      //drupal_goto('/pssp');
      drupal_goto('user/login', [
        'query' => drupal_get_destination()
        ]);
      return $msg;
    }
    $root_path = mscd_hackathon_submission_files_path();
    $my_submission_rows = [];
    $query = \Drupal::database()->select('mixed_signal_marathon_literature_survey');
    $query->fields('mixed_signal_marathon_literature_survey');
    //$query->condition('approval_status', 2);
    //$query->condition('uid',$user->uid);
    $all_submissions_q = $query->execute();
    $participants_email_id_file = $root_path . "participants-emails.csv";
    //var_dump($participants_email_id_file);die;
    $fp = fopen($participants_email_id_file, "w");
    /* making the first row */
    $item = ["Email ID"];
    fputcsv($fp, $item);

    while ($row = $all_submissions_q->fetchObject()) {
      $item = [$row->participant_email];
      fputcsv($fp, $item);
    }
    fclose($fp);
    if ($participants_email_id_file) {
      ob_clean();
      header("Pragma: public");
      header("Expires: 0");
      header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
      header("Cache-Control: public");
      header("Content-Description: File Transfer");
      header('Content-Type: application/csv');
      header('Content-disposition: attachment; filename=email-ids.csv');
      header('Content-Length:' . filesize($participants_email_id_file));
      header("Content-Transfer-Encoding: binary");
      header('Expires: 0');
      header('Pragma: no-cache');
      readfile($participants_email_id_file);
      /*ob_end_flush();
            ob_clean();
            flush();*/
    }
  }


public function soc_marathon_download_completed_circuit() {

  $output = "";

  $connection = \Drupal::database();
  $query = $connection->select('mixed_signal_soc_marathon_final_submission', 'f');
  $query->fields('f');
  $query->condition('approval_status', 3);
  $query->orderBy('id', 'DESC');
  $result = $query->execute();

  $rows = $result->fetchAll();

  // Intro text
  $intro_text = "FOSSEE, IIT Bombay, along with Google and VLSI System Design Corp. Pvt. Ltd conducted a 3-weeks high intensity Mixed Signal SoC design Marathon using eSim & SKY130. Close to 3000 students from all over India participated in this Marathon and close to 100+ students completed this marathon with brilliant circuit design ideas. The following participants have successfully completed designing the circuits.
More details about this event can be found here: <a href='https://hackathon.fossee.in/esim' target='_blank'>https://hackathon.fossee.in/esim</a>.<hr>";

  $output .= $intro_text;

  // If no data
  if (empty($rows)) {
    return [
      '#type' => 'markup',
      '#markup' => $output,
    ];
  }

  /* Build table rows */
  $table_rows = [];
  $i = count($rows);

  foreach ($rows as $submission) {

    // Fetch literature survey
    $query = $connection->select('mixed_signal_soc_marathon_literature_survey', 'l');
    $query->fields('l');
    $query->condition('id', $submission->literature_survey_id);
    $query->orderBy('id', 'DESC');
    $lit = $query->execute()->fetchObject();

    if (!$lit) {
      continue;
    }

    // Create link for circuit download
    $url = Url::fromRoute('hackathon_submission.download_soc_marathon_final_submission_form', [
      'submission_id' => $lit->id,
    ]);
    $link = Link::fromTextAndUrl($lit->circuit_name, $url)->toString();

    $table_rows[] = [
      $i,
      ['data' => ['#markup' => $link]],
      $lit->participant_name,
      $lit->institute,
    ];

    $i--;
  }

  if (empty($table_rows)) {
    \Drupal::messenger()->addStatus("There are no active submissions");
    return ['#markup' => $output];
  }

  /* Build renderable table */
  $table = [
    '#type' => 'table',
    '#header' => [
      'S.No',
      'Circuit Name',
      'Participant Name',
      'University',
    ],
    '#rows' => $table_rows,
    '#empty' => t('No completed circuits found.'),
  ];

  return [
    'intro' => [
      '#type' => 'markup',
      '#markup' => $output,
    ],
    'table' => $table,
  ];
}


  public function add_soc_literature_report_submission() {
    $user = \Drupal::currentUser();
    if ($user->uid == 0) {
      $msg = \Drupal::messenger()->addError(t('It is mandatory to log in on this website to upload your submission. If you are new user please create a new account first.'));
      //drupal_goto('esim-circuit-simulation-project');
      drupal_goto('user/login', [
        'query' => drupal_get_destination()
        ]);
      return $msg;
    } //$user->uid == 0
    $today = date("Y-m-d H:i:s");
    //var_dump($today);die;
    // @FIXME
// // @FIXME
// // This looks like another module's variable. You'll need to rewrite this call
// // to ensure that it uses the correct configuration object.
// $start_date = variable_get('soc_marathon_literature_report_start_date', '');

    // @FIXME
// // @FIXME
// // This looks like another module's variable. You'll need to rewrite this call
// // to ensure that it uses the correct configuration object.
// $last_date = variable_get('soc_marathon_literature_report_last_date', '');

    $return_html = '';
    //var_dump(date("d-m-Y, H:i A", strtotime($start_date)));die;
    if ($today < $start_date) {
      $return_html .= '<p>You can submit your Literature Survey report at anytime between ' . date("d-m-Y, H:i A", strtotime($start_date)) . ', and ' . date("d-m-Y, H:i A", strtotime($last_date)) . '.</p>';
    }
    elseif ($today > $last_date) {
      $return_html .= '<p>Literature Survey Submissions are closed.</p>';
    }
    else {
      $submission_form = \Drupal::formBuilder()->getForm("add_mixed_signal_soc_marathon_literature_report_form");
      $return_html .= \Drupal::service("renderer")->render($submission_form);
    }
    return $return_html;
  }

public function soc_marathon_submission_display_my_submissions() {
  $user = \Drupal::currentUser();

  // User must be logged in.
  if ($user->isAnonymous()) {
    \Drupal::messenger()->addError($this->t(
      'It is mandatory to login on this website to view your proposals.'
    ));
    return new RedirectResponse(Url::fromRoute('user.login')->toString());
  }

  $connection = Database::getConnection();

  // Latest literature survey
  $literature = $connection->select('mixed_signal_soc_marathon_literature_survey', 'ls')
    ->fields('ls')
    ->condition('uid', $user->id())
    ->orderBy('id', 'DESC')
    ->range(0, 1)
    ->execute()
    ->fetchObject();

  if (!$literature) {
    \Drupal::messenger()->addError($this->t('We have not received your submission.'));
    return [];
  }

  // Latest final submission
  $final = $connection->select('mixed_signal_soc_marathon_final_submission', 'fs')
    ->fields('fs')
    ->condition('uid', $user->id())
    ->condition('literature_survey_id', $literature->id)
    ->orderBy('id', 'DESC')
    ->range(0, 1)
    ->execute()
    ->fetchObject();

  // Dates
  $literature_date = date('d-m-Y', $literature->creation_date);
  $final_date = ($final && $final->creation_date)
    ? date('d-m-Y', $final->creation_date)
    : $this->t('Not yet uploaded');

  // Literature actions
  $literature_actions = Link::fromTextAndUrl(
    $this->t('View'),
    Url::fromRoute(
      'hackathon_submission.view_soc_literature_report',
      ['submission_id' => $literature->id]
    )
  )->toString();

  // Final submission actions
  if ($final) {
    $final_actions = Link::fromTextAndUrl(
      $this->t('View'),
      Url::fromRoute(
        'hackathon_submission.view_soc_final_submission',
        ['submission_id' => $literature->id]
      )
    )->toString();
  }
  else {
    $final_actions = Link::fromTextAndUrl(
      $this->t('Add Final Submission'),
      Url::fromRoute('hackathon_submission.add_soc_final_submission')
    )->toString();
  }

  // Table rows
  $rows[] = [
    $literature_date,
    $final_date,
    Link::fromTextAndUrl(
      $literature->participant_name,
      Url::fromRoute('entity.user.canonical', ['user' => $literature->uid])
    )->toString(),
    $literature->circuit_name,
    ['data' => ['#markup' => $literature_actions]],
    ['data' => ['#markup' => $final_actions]],
  ];

  // Return render array
  return [
    '#theme' => 'table',
    '#header' => [
      $this->t('Literature Survey Date'),
      $this->t('Final Submission Date'),
      $this->t('Name'),
      $this->t('Circuit Name'),
      $this->t('Literature Survey'),
      $this->t('Final Submission'),
    ],
    '#rows' => $rows,
    '#empty' => $this->t('No submissions found.'),
  ];
}

public function soc_marathon_download_literature_report() {
      $submission_id = \Drupal::routeMatch()->getParameter('submission_id');


  $query = \Drupal::database()->select('mixed_signal_soc_marathon_literature_survey', 'm');
  $query->fields('m');
  $query->condition('id', $submission_id);
  $submission_data = $query->execute()->fetchObject();

  if (!$submission_data) {
    \Drupal::messenger()->addError($this->t('Invalid submission.'));
    return $this->redirect('hackathon_submission.soc_marathon_display_final_submissions');
  }

  $root_path = soc_marathon_hackathon_submission_files_path();
  $file_path = $root_path . '/' . $submission_data->directory_name . '/' . $submission_data->report_file;

  if (!file_exists($file_path)) {
    \Drupal::messenger()->addError($this->t('File not found.'));
    return $this->redirect('hackathon_submission.soc_marathon_display_final_submissions');
  }

  $response = new BinaryFileResponse($file_path);
  $response->setContentDisposition(
    ResponseHeaderBag::DISPOSITION_ATTACHMENT,
    $submission_data->report_file
  );
  $response->headers->set('Content-Type', 'application/pdf');

  return $response;
}

public function soc_marathon_display_final_submissions() {
  $current_user = \Drupal::currentUser();

  // Require login.
  if (!$current_user->isAuthenticated()) {
    \Drupal::messenger()->addError(t('It is mandatory to login on this website to view all submissions. If you are a new user, please create an account first.'));

    $destination = \Drupal::request()->getRequestUri();
    return new RedirectResponse(
      Url::fromRoute('user.login', [], ['query' => ['destination' => $destination]])->toString()
    );
  }

  $rows = [];

  $query = \Drupal::database()->select('mixed_signal_soc_marathon_final_submission', 'f');
  $query->fields('f');
  $query->orderBy('id', 'DESC');
  $final_results = $query->execute();

  foreach ($final_results as $final_record) {

    $ls_query = \Drupal::database()->select('mixed_signal_soc_marathon_literature_survey', 'l');
    $ls_query->fields('l');
    $ls_query->condition('id', $final_record->literature_survey_id);
    $literature = $ls_query->execute()->fetchObject();

    if (!$literature) {
      continue;
    }

    // Participant profile link.
    $user_link = Link::fromTextAndUrl(
      $literature->participant_name,
      Url::fromRoute('entity.user.canonical', ['user' => $literature->uid])
    )->toString();

    // Final submission view link.
    $view_link = Link::fromTextAndUrl(
      t('View'),
      Url::fromUri('internal:/mixed-signal-soc-design-marathon/view/final-submission/' . $literature->id)
    )->toString();

    $rows[] = [
      date('d-m-Y', $final_record->creation_date),
      $user_link,
      $literature->circuit_name,
      $view_link,
    ];
  }

  if (empty($rows)) {
    \Drupal::messenger()->addStatus(t('There are no active submissions.'));
    return [];
  }

  return [
    '#markup' => '<p>' . t('Total number of submissions: @count', ['@count' => count($rows)]) . '</p>',
    'table' => [
      '#type' => 'table',
      '#header' => [
        t('Date of Submission'),
        t('Name'),
        t('Circuit Name'),
        t('Final Submission'),
      ],
      '#rows' => $rows,
      '#empty' => t('No submissions available.'),
    ],
  ];
}


public function soc_marathon_display_literature_submissions() {
  $current_user = \Drupal::currentUser();

  // Require login.
  if (!$current_user->isAuthenticated()) {
    \Drupal::messenger()->addError(t('It is mandatory to login on this website to view all submissions. If you are a new user, please create an account first.'));

    $destination = \Drupal::request()->getRequestUri();
    return new RedirectResponse(
      Url::fromRoute('user.login', [], ['query' => ['destination' => $destination]])->toString()
    );
  }

  $rows = [];

  $query = \Drupal::database()->select('mixed_signal_soc_marathon_literature_survey', 'l');
  $query->fields('l');
  $query->orderBy('id', 'DESC');
  $result = $query->execute();

  foreach ($result as $record) {

    // Participant profile link.
    $user_link = Link::fromTextAndUrl(
      $record->participant_name,
      Url::fromRoute('entity.user.canonical', ['user' => $record->uid])
    )->toString();

    // Literature survey view link.
    $view_link = Link::fromTextAndUrl(
      t('View'),
      Url::fromUri('internal:/mixed-signal-soc-design-marathon/view/literature-report/' . $record->id)
    )->toString();

    $rows[] = [
      date('d-m-Y', $record->creation_date),
      $user_link,
      $record->circuit_name,
      $view_link,
    ];
  }

  if (empty($rows)) {
    \Drupal::messenger()->addStatus(t('There are no active submissions yet.'));
    return [];
  }

  return [
    '#markup' => '<p>' . t('Total number of submissions: @count', ['@count' => count($rows)]) . '</p>
                  <p>' . t('Click') . ' <a href="' .
                  Url::fromUri('internal:/download-emails')->toString() .
                  '">' . t('here') . '</a> ' .
                  t('to download the Email IDs of the participants.') . '</p>',
    'table' => [
      '#type' => 'table',
      '#header' => [
        t('Date of Submission'),
        t('Name'),
        t('Circuit Name'),
        t('Literature Survey'),
      ],
      '#rows' => $rows,
      '#empty' => t('No submissions available.'),
    ],
  ];
}

  public function soc_marathon_download_emails() {
    $user = \Drupal::currentUser();
    /* get pending submissions to be approved */
    if ($user->uid == 0) {
      $msg = \Drupal::messenger()->addError(t('It is mandatory to login on this website to download the email IDs of the participants. If you are new user please create a new account first.'));
      //drupal_goto('/pssp');
      drupal_goto('user/login', [
        'query' => drupal_get_destination()
        ]);
      return $msg;
    }
    $root_path = soc_marathon_hackathon_submission_files_path();
    $my_submission_rows = [];
    $query = \Drupal::database()->select('mixed_signal_soc_marathon_literature_survey');
    $query->fields('mixed_signal_soc_marathon_literature_survey');
    //$query->condition('approval_status', 2);
    //$query->condition('uid',$user->uid);
    $all_submissions_q = $query->execute();
    $participants_email_id_file = $root_path . "soc-participants-emails.csv";
    //var_dump($participants_email_id_file);die;
    $fp = fopen($participants_email_id_file, "w");
    /* making the first row */
    $item = ["Email ID"];
    fputcsv($fp, $item);

    while ($row = $all_submissions_q->fetchObject()) {
      $item = [$row->participant_email];
      fputcsv($fp, $item);
    }
    fclose($fp);
    if ($participants_email_id_file) {
      ob_clean();
      header("Pragma: public");
      header("Expires: 0");
      header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
      header("Cache-Control: public");
      header("Content-Description: File Transfer");
      header('Content-Type: application/csv');
      header('Content-disposition: attachment; filename=email-ids.csv');
      header('Content-Length:' . filesize($participants_email_id_file));
      header("Content-Transfer-Encoding: binary");
      header('Expires: 0');
      header('Pragma: no-cache');
      readfile($participants_email_id_file);
      /*ob_end_flush();
            ob_clean();
            flush();*/
    }
  }

  public function add_soc_marathon_final_report_submission() {
    $user = \Drupal::currentUser();
    if ($user->uid == 0) {
      $msg = \Drupal::messenger()->addError(t('It is mandatory to log in on this website to upload your submission. If you are new user please create a new account first.'));
      //drupal_goto('esim-circuit-simulation-project');
      drupal_goto('user/login', [
        'query' => drupal_get_destination()
        ]);
      return $msg;
    } //$user->uid == 0
    $query = \Drupal::database()->select('mixed_signal_soc_marathon_literature_survey');
    $query->fields('mixed_signal_soc_marathon_literature_survey');
    $query->condition('uid', $user->uid);
    //$query->condition('id', $submission_id);
    //$query->range(0, 1);
    $submission_q = $query->execute();
    $literature_submission_data = $submission_q->fetchObject();
    if ($literature_submission_data) {
      $today = date("Y-m-d H:i:s");
      //var_dump($today);die;
      // @FIXME
// // @FIXME
// // This looks like another module's variable. You'll need to rewrite this call
// // to ensure that it uses the correct configuration object.
// $start_date = variable_get('soc_marathon_final_submission_start_date', '');

      // @FIXME
// // @FIXME
// // This looks like another module's variable. You'll need to rewrite this call
// // to ensure that it uses the correct configuration object.
// $last_date = variable_get('soc_marathon_final_submission_last_date', '');

      $return_html = '';
      if ($today < $start_date) {
        $return_html .= '<p>You can submit your Literature Survey report at anytime between ' . date("d-m-Y, H:i A", strtotime($start_date)) . ', and ' . date("d-m-Y, H:i A", strtotime($last_date)) . '.</p>';
      }
      elseif ($today > $last_date) {
        $return_html .= '<p>Final Report Submissions are closed.</p>';
      }
      else {
        $submission_form = \Drupal::formBuilder()->getForm("add_soc_marathon_final_report_submission_form");
        $return_html .= \Drupal::service("renderer")->render($submission_form);
      }
      return $return_html;
    }
    else {
      \Drupal::messenger()->addError('We regret to inform that we have not received your literature survey report');
      drupal_goto('');
    }
  }

  public function soc_marathon_download_final_submission($submission_id) {
    $root_path = soc_marathon_hackathon_submission_files_path();

    // 1. Fetch literature survey entry.
    $submission_data = \Drupal::database()
      ->select('mixed_signal_soc_marathon_literature_survey', 'm')
      ->fields('m')
      ->condition('id', $submission_id)
      ->execute()
      ->fetchObject();

    if (!$submission_data) {
      $this->messenger()->addError("Invalid submission ID.");
      return new RedirectResponse(Url::fromRoute('<front>')->toString());
    }

    // 2. Fetch related files.
    $final_submission_q = \Drupal::database()
      ->select('mixed_signal_soc_marathon_final_submission_files', 'f')
      ->fields('f')
      ->condition('literature_survey_id', $submission_id)
      ->execute();

    $zip_filename = $root_path . $submission_data->circuit_name . '.zip';
    $zip = new \ZipArchive();
    $zip->open($zip_filename, \ZipArchive::CREATE);

    $directory_path = ''; // If you want folder inside ZIP, set it here.

    while ($final_submission_data = $final_submission_q->fetchObject()) {
      $zip->addFile(
        $root_path . $final_submission_data->filepath,
        $directory_path . str_replace(' ', '_', basename($final_submission_data->filename))
      );
    }

    $zip_file_count = $zip->numFiles;
    $zip->close();

    // If ZIP has files → download
    if ($zip_file_count > 0) {
      $response = new BinaryFileResponse($zip_filename);
      $response->setContentDisposition(
        ResponseHeaderBag::DISPOSITION_ATTACHMENT,
        str_replace(' ', '_', $submission_data->circuit_name) . '.zip'
      );
      $response->deleteFileAfterSend(true);
      return $response;
    }

    // No files → redirect
    $this->messenger()->addError("There are no files in this circuit to download.");
    return new RedirectResponse(Url::fromRoute('hackathon_submission.soc_marathon_download_completed_circuit')->toString());
  }

}



