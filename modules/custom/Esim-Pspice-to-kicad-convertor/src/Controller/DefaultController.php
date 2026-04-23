<?php /**
 * @file
 * Contains \Drupal\pspice_to_kicad\Controller\DefaultController.
 */

namespace Drupal\pspice_to_kicad\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Database\Database;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Drupal\Core\Link;
use Drupal\Core\Url;
use Drupal\Component\Utility\Html;
use Drupal\file\Entity\File;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Drupal\user\Entity\User;



/**
 * Default controller for the pspice_to_kicad module.
 */
class DefaultController extends ControllerBase {


//  public function pspice_to_kicad_view() {

//   $rows = [];

//   $header = [
//     $this->t('Uploaded by'),
//     $this->t('Download'),
//     '',
//     $this->t('Date'),
//   ];

//   $connection = Database::getConnection();
// $query = $connection->select('custom_kicad_convertor', 'ckc');
// $query->leftJoin('users_field_data', 'u', 'u.uid = ckc.uid');

// $query->fields('ckc', [
//   'id',
//   'converted_filename',
//   'converted_date',
// ]);

// // ✅ Correct way to fetch username
// $query->fields('u', ['name']);

// $query->condition('ckc.converted_flag', 2);
// $query->orderBy('ckc.converted_date', 'DESC');

// $result = $query->execute();


//   foreach ($result as $row) {

//     // ✅ Username guaranteed
// $username = !empty($row->name) ? $row->name : $this->t('Anonymous');

//     $download_link = Link::fromTextAndUrl(
//       $row->converted_filename,
//       Url::fromRoute('pspice_to_kicad.download_file', ['id' => $row->id])
//     )->toRenderable();

//     // $detail_link = Link::fromTextAndUrl(
//     //   $this->t('Detail'),
//     //   Url::fromRoute('pspice_to_kicad.description', ['id' => $row->id])
//     // )->toRenderable();

//      $detail_link = Link::fromTextAndUrl(
//         $this->t('Detail'),
//         Url::fromUserInput('/pspice-to-kicad/description/' . $row->id, [
//           'attributes' => [
//             'class' => ['use-ajax'],
//             'data-dialog-type' => 'modal',
//             'data-dialog-options' => json_encode(['width' => 700]),
//             'title' => $this->t('Click to view description of file'),
//           ],
//         ])
//       )->toString();


//     $rows[] = [
//       ['data' => ['#markup' => $username]],
//       ['data' => $download_link],
//       ['data' => $detail_link],
//       ['data' => date('d-m-Y', strtotime($row->converted_date))],
//     ];
//   }

//   if (empty($rows)) {
//     return [
//       '#markup' => '<div style="color:red;text-align:center;">No files available yet for download</div>',
//     ];
//   }

//   return [
//     '#theme' => 'table',
//     '#header' => $header,
//     '#rows' => $rows,
//     '#caption' => $this->t('List of Converted files'),
//     '#attributes' => [
//       'class' => ['table', 'table-bordered', 'table-hover'],
//     ],
//   ];
// }

public function pspice_to_kicad_view() {

  $rows = [];

  $header = [
    $this->t('Uploaded by'),
    $this->t('Download'),
    '',
    $this->t('Date'),
  ];

  $query = \Drupal::database()->select('custom_kicad_convertor', 'ckc');
  $query->fields('ckc', [
    'id',
    'uid',
    'converted_filename',
    'converted_date',
  ]);
  $query->condition('ckc.converted_flag', 2);
  $query->orderBy('converted_date', 'DESC');

  $result = $query->execute();

  foreach ($result->fetchAll() as $row) {

    /** ✅ Proper user load (Drupal 10 way) */
    // $username = $this->t('Anonymous');
    if (!empty($row->uid) && $account = User::load($row->uid) ) {
      
      $username = $account->getDisplayName();
      
    }

    $download_link = Link::fromTextAndUrl(
      $row->converted_filename,
      Url::fromRoute('pspice_to_kicad.download_file', ['id' => $row->id])
    )->toRenderable();

    $detail_link = Link::fromTextAndUrl(
      $this->t('Detail'),
      Url::fromUserInput('/pspice-to-kicad/description/' . $row->id, [
        'attributes' => [
          'class' => ['use-ajax'],
          'data-dialog-type' => 'modal',
          'data-dialog-options' => json_encode(['width' => 700]),
        ],
      ])
    )->toString();

    /** ✅ NOW item is actually created */
    $rows[] = [
      ['data' => ['#markup' => $username]],
      ['data' => $download_link],
      ['data' => $detail_link],
      ['data' => date('d-m-Y', strtotime($row->converted_date))],
    ];
  }

  if (empty($rows)) {
    return [
      '#markup' => '<div style="color:red;text-align:center;">No files available yet for download</div>',
    ];
  }

  return [
    '#theme' => 'table',
    '#header' => $header,
    '#rows' => $rows,
    '#caption' => $this->t('List of Converted files'),
    '#attributes' => [
      'class' => ['table', 'table-bordered', 'table-hover'],
    ],
  ];
}

public function pspice_to_kicad_get_description($id) {

  $connection = Database::getConnection();

  $row = $connection->select('custom_kicad_convertor', 'ckc')
    ->fields('ckc')
    ->condition('id', $id)
    ->execute()
    ->fetchObject();

  // Safety check
  if (!$row) {
    return [
      '#markup' => $this->t('File not found.'),
    ];
  }

  // Extract PDF filename safely
  $pdf_filename = '';
  if (!empty($row->description_pdf_path)) {
    $pdf_filename = basename(explode('@', $row->description_pdf_path)[0]);
  }

  // Description markup
  $build = [
    '#type' => 'container',
    '#attributes' => ['class' => ['pspice-description']],
  ];

  $build['description'] = [
    '#markup' => '<p><strong>' . $this->t('Brief Description of file:') . '</strong> ' .
      Html::escape($row->description) . '</p>',
  ];

  // PDF download link
  if (!empty($row->description_pdf_path)) {

    $pdf_url = Url::fromRoute(
      'pspice_to_kicad.download_pdf',
      ['id' => $row->id],
      ['attributes' => [
        'title' => $this->t('Click to download PDF'),
        'style' => 'color:#156AA3;text-decoration: underline',
      ]]
    );

    $build['pdf'] = [
      '#markup' => '<strong>' . $this->t('PDF File with description:') . '</strong> ' .
        Link::fromTextAndUrl($pdf_filename, $pdf_url)->toString(),
    ];
  }

  return $build;
}



public function pspice_to_kicad_remain_list() {

  $build = [];

  // Inline CSS (optional: ideally move to a library)
  $build['#attached']['html_head'][] = [
    [
      '#tag' => 'style',
      '#value' => '
        .convert_button {
          display: inline-block;
          width: 80px;
          height: 25px;
          background: #156aa3;
          padding: 2px;
          text-align: center;
          border-radius: 5px;
          color: #fff;
          text-decoration: none;
          line-height: 22px;
        }
      ',
      
    ],
    'pspice_to_kicad_inline_css',
  ];

  $header = [
    $this->t('Sr No.'),
    $this->t('Uploaded Date'),
    $this->t('File'),
    $this->t('Convert'),
    $this->t('Delete'),
    $this->t('Upload'),
    $this->t('Publish'),
  ];

  $rows = [];
  $i = 1;

  $query = Database::getConnection()
    ->select('custom_kicad_convertor', 'ckc')
    ->fields('ckc', ['id', 'uid', 'upload_date', 'upload_filename'])
    ->condition('ckc.converted_flag', 2, '<>')
    ->orderBy('id', 'DESC');

  $result = $query->execute();

  foreach ($result as $row) {
    $filename = pathinfo($row->upload_filename, PATHINFO_FILENAME);

    // File detail link
    $file_link = Link::fromTextAndUrl(
      $filename,
      Url::fromRoute('pspice_to_kicad.description', ['id' => $row->id])
    );
    $file_link_html = \Drupal::service('renderer')->render($file_link->toRenderable());

    // Action links
    // $convert_link = $this->actionLink('Convert', '/pspice-to-kicad/convert/file/' . $row->id);
    // $delete_link = $this->actionLink('Delete', '/pspice-to-kicad/delete/file/' . $row->id);
    // $upload_link = $this->actionLink('Upload', '/pspice-to-kicad/convert/upload/' . $row->id);
    // $publish_link = $this->actionLink('Publish', '/pspice-to-kicad/convert/approved/' . $row->id);
    $convert_link = $this->actionLink('Convert', '/pspice-to-kicad/convert/file/' . $row->id, ['class' => ['btn', 'btn-convert']]);

$delete_link = $this->actionLink('Delete', '/pspice-to-kicad/delete/file/' . $row->id, ['class' => ['btn', 'btn-delete']]);

$upload_link = $this->actionLink('Upload', '/pspice-to-kicad/convert/upload/' . $row->id, ['class' => ['btn', 'btn-upload']]);

$publish_link = $this->actionLink('Publish', '/pspice-to-kicad/convert/approved/' . $row->id, ['class' => ['btn', 'btn-publish']]);


    $rows[] = [
      ['data' => $i++],
      ['data' => date('d-m-Y', strtotime($row->upload_date))],
      ['data' => $file_link_html],
      ['data' => $convert_link],
      ['data' => $delete_link],
      ['data' => $upload_link],
      ['data' => $publish_link],
    ];
  }

  if (empty($rows)) {
    $build['empty'] = [
      '#markup' => '<div style="color:red;text-align:center;">No files to convert</div>',
    ];
    return $build;
  }

  $build['table'] = [
    '#type' => 'table',
    '#caption' => $this->t('List of files to be converted'),
    '#header' => $header,
    '#rows' => $rows,
    '#attributes' => [
      'class' => ['table', 'table-bordered', 'table-hover'],
    ],
  ];

  return $build;
}

/**
 * Helper to build action links as rendered HTML.
 */
protected function actionLink($text, $path) {
  $link = Link::fromTextAndUrl(
    $this->t($text),
    Url::fromUri('internal:' . $path)
  );
  return ['#markup' => \Drupal::service('renderer')->render($link->toRenderable()), '#allowed_tags' => ['a']];
}


  

 public function pspice_to_kicad_download_file($id) {

    $connection = Database::getConnection();
    $root_path = pspice_to_kicad_directroy_path();

    // Load file record.
    $file_data = $connection->select('custom_kicad_convertor', 'c')
      ->fields('c')
      ->condition('id', $id)
      ->range(0, 1)
      ->execute()
      ->fetchObject();

    if (!$file_data) {
      $this->messenger()->addError($this->t('File not found.'));
      return new RedirectResponse(
        Url::fromUserInput('/pspice-to-kicad/list')->toString()
      );
    }

    $file_path = $root_path . '/' . $file_data->converted_filepath;

    if (!file_exists($file_path)) {
      $this->messenger()->addError($this->t('Converted file does not exist.'));
      return new RedirectResponse(
        Url::fromUserInput('/pspice-to-kicad/list')->toString()
      );
    }

    // Update download counter.
    $connection->update('custom_kicad_convertor')
      ->fields([
        'download_counter' => (int) $file_data->download_counter + 1,
      ])
      ->condition('id', $id)
      ->execute();

    // Return file download response.
    $response = new BinaryFileResponse($file_path);
    $response->headers->set('Content-Type', $file_data->converted_filemime);
    $response->setContentDisposition(
      ResponseHeaderBag::DISPOSITION_ATTACHMENT,
      $file_data->converted_filename
    );
    $response->headers->set('Content-Length', filesize($file_path));

    return $response;
  }


    public function pspice_to_kicad_download_pdf_file($id) {

    $connection = Database::getConnection();

    // Load file record.
    $file_data = $connection->select('custom_kicad_convertor', 'c')
      ->fields('c')
      ->condition('id', $id)
      ->range(0, 1)
      ->execute()
      ->fetchObject();

    if (!$file_data || empty($file_data->description_pdf_path)) {
      throw new NotFoundHttpException('PDF file not found.');
    }

    $root_path = pspice_to_kicad_directroy_path();
    $file_path = $root_path . '/' . $file_data->description_pdf_path;

    if (!file_exists($file_path)) {
      throw new NotFoundHttpException('PDF file does not exist.');
    }

    // Extract PDF filename safely.
    $pdf_filename = basename(explode('@', $file_data->description_pdf_path)[0]);

    // Create secure PDF download response.
    $response = new BinaryFileResponse($file_path);
    $response->setContentDisposition(
      ResponseHeaderBag::DISPOSITION_ATTACHMENT,
      $pdf_filename
    );

    // Explicitly set PDF MIME type.
    $response->headers->set('Content-Type', 'application/pdf');

    return $response;
  }


    public function pspice_to_kicad_convert_file($id) {

    try {
      $upload_root_path = pspice_to_kicad_directroy_path();

      // Load DB record.
      $row = Database::getConnection()
        ->select('custom_kicad_convertor', 'ckc')
        ->fields('ckc', [
          'id',
          'uid',
          'caption',
          'upload_date',
          'upload_filename',
          'upload_filepath',
        ])
        ->condition('id', $fileid)
        ->execute()
        ->fetchObject();

      if (!$row) {
        $this->messenger()->addError($this->t('Invalid file ID.'));
        return new RedirectResponse(
          Url::fromUserInput('/pspice-to-kicad/convert')->toString()
        );
      }

      $file = $row->upload_filename;
      $filename_without_ext = pathinfo($file, PATHINFO_FILENAME);

      $filePath = $upload_root_path . '/' . $row->upload_filepath;
      $uploadfolder = $row->uid;

      // Conversion paths.
      $convert_root_path = get_directory_path($row->uid, $fileid, 2);
      $converted_rel_path = get_directory_path($row->uid, $fileid, 4);

      // Shell script path (update if needed).
      $sh_script_file = DRUPAL_ROOT . '/modules/custom/pspice_to_kicad/convert.sh';

      // Execute conversion.
      shell_exec(
        escapeshellcmd($sh_script_file) . ' ' .
        escapeshellarg($convert_root_path) . ' ' .
        escapeshellarg($filePath) . ' ' .
        escapeshellarg($uploadfolder)
      );

      $converted_file = $convert_root_path . '/' . $filename_without_ext . '.zip';

      if (!file_exists($converted_file)) {
        throw new \Exception('Converted file not found.');
      }

      // Update DB.
      Database::getConnection()
        ->update('custom_kicad_convertor')
        ->fields([
          'converted_filename' => $filename_without_ext . '.zip',
          'converted_filepath' => $converted_rel_path . '/' . $filename_without_ext . '.zip',
          'converted_filemime' => mime_content_type($converted_file),
          'converted_filesize' => filesize($converted_file),
          'converted_date' => date('Y-m-d H:i:s'),
          'converted_flag' => 1,
        ])
        ->condition('id', $row->id)
        ->execute();

      // Return file download response.
      $response = new BinaryFileResponse($converted_file);
      $response->setContentDisposition(
        ResponseHeaderBag::DISPOSITION_ATTACHMENT,
        $filename_without_ext . '.zip'
      );

      return $response;

    }
    catch (\Exception $e) {

      $this->messenger()->addError(
        $this->t('Error while converting file. Please download and check the file manually.')
      );

      return new RedirectResponse(
        Url::fromUserInput('/pspice-to-kicad/convert')->toString()
      );
    }
  }

  public function pspice_to_kicad_convert_approved($fileid) {

    $connection = Database::getConnection();

    // Check converted_flag = 1.
    $count = $connection->select('custom_kicad_convertor', 'c')
      ->condition('id', $fileid)
      ->condition('converted_flag', 1)
      ->countQuery()
      ->execute()
      ->fetchField();

    if ($count == 0) {
      $this->messenger()->addError(
        $this->t('File should be converted before publishing.')
      );

      return new RedirectResponse(
        Url::fromUserInput('/pspice-to-kicad/convert')->toString()
      );
    }

    // Update converted_flag → 2.
    $connection->update('custom_kicad_convertor')
      ->fields([
        'converted_flag' => 2,
      ])
      ->condition('id', $fileid)
      ->execute();

    // Load record.
    $row = $connection->select('custom_kicad_convertor', 'c')
      ->fields('c')
      ->condition('id', $fileid)
      ->execute()
      ->fetchObject();

    if (!$row) {
      $this->messenger()->addError($this->t('Invalid file.'));
      return new RedirectResponse(
        Url::fromUserInput('/pspice-to-kicad/convert')->toString()
      );
    }

    // Delete uploaded file.
    $upload_root_path = pspice_to_kicad_user_upload_path();
    $filepath = $upload_root_path . $row->uid . '/' . $row->upload_filename;

    if (file_exists($filepath)) {
      unlink($filepath);
    }

    // Load user.
    $user = User::load($row->uid);
    if (!$user) {
      return new RedirectResponse(
        Url::fromUserInput('/pspice-to-kicad/convert')->toString()
      );
    }

    // Config values (replaces variable_get).
    $config = $this->config('pspice_to_kicad.settings');

    $to = $user->getEmail();
    $from = $config->get('kicad_from_email');
    $subject = '[esim.in][PSpice to KiCad Converter] PSpice file converted successfully';

    $body = [
      $this->t('Dear @name,', ['@name' => $user->getDisplayName()]),
      '',
      $this->t(
        'The uploaded PSpice file "@file" has been successfully converted to KiCad format.',
        ['@file' => $row->upload_filename]
      ),
      '',
      $this->t(
        'You can download the converted file here: @link',
        ['@link' => 'https://esim.fossee.in/pspice-to-kicad/download/file/' . $fileid]
      ),
      '',
      $this->t('Best Wishes,'),
      $this->config('system.site')->get('name') . ' Team,',
      'FOSSEE, IIT Bombay',
    ];

    // Send email.
    $this->mailManager()->mail(
      'pspice_to_kicad',
      'convert_approved',
      $to,
      $this->languageManager()->getDefaultLanguage()->getId(),
      ['message' => implode("\n", $body)],
      $from
    );

    $this->messenger()->addStatus(
      $this->t('File approved and user notified successfully.')
    );

    return new RedirectResponse(
      Url::fromUserInput('/pspice-to-kicad/convert')->toString()
    );
  }

  public function pspice_to_kicad_your_uploaded_file() {

    $current_user = $this->currentUser();
    $uid = $current_user->id();

    $header = [
      ['data' => $this->t('Date'), 'field' => 'ckc.upload_date', 'sort' => 'desc'],
      ['data' => $this->t('File name')],
      ['data' => $this->t('Conversion Status')],
      ['data' => ''],
    ];

    $query = Database::getConnection()
      ->select('custom_kicad_convertor', 'ckc')
      ->fields('ckc', [
        'id',
        'uid',
        'description',
        'upload_filename',
        'converted_flag',
        'upload_date',
      ])
      ->condition('ckc.uid', $uid)
      ->extend('Drupal\Core\Database\Query\PagerSelectExtender')
      ->limit(10)
      ->extend('Drupal\Core\Database\Query\TableSortExtender')
      ->orderByHeader($header);

    $result = $query->execute();

    $rows = [];
    foreach ($result as $row) {

      $status = in_array($row->converted_flag, [0, 1])
        ? $this->t('In Process')
        : $this->t('Converted');

      $detail_link = Link::fromTextAndUrl(
        $this->t('Detail'),
        Url::fromUserInput('/pspice-to-kicad/description/' . $row->id, [
          'attributes' => [
            'class' => ['use-ajax'],
            'data-dialog-type' => 'modal',
            'data-dialog-options' => json_encode(['width' => 700]),
            'title' => $this->t('Click to view description of file'),
          ],
        ])
      )->toString();

      $rows[] = [
        date('d-m-Y', strtotime($row->upload_date)),
        $row->upload_filename,
        $status,
        ['data' => ['#markup' => $detail_link]],
      ];
    }

    if (empty($rows)) {
      return [
        '#markup' => '<div style="color:red;text-align:center;">' .
          $this->t('No files have been uploaded by you') .
          '</div>',
      ];
    }

    return [
      'table' => [
        '#theme' => 'table',
        '#header' => $header,
        '#rows' => $rows,
        '#attributes' => [
          'class' => ['table', 'table-bordered', 'table-hover'],
        ],
      ],
      'pager' => [
        '#type' => 'pager',
      ],
    ];
  }

}









