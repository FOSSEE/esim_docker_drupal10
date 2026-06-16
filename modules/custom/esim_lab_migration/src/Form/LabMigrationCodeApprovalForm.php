<?php

/**
 * @file
 * Contains \Drupal\lab_migration\Form\LabMigrationCodeApprovalForm.
 */

namespace Drupal\lab_migration\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Drupal\user\Entity\User;
use Drupal\Core\Link;

class LabMigrationCodeApprovalForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'lab_migration_code_approval_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $route_match = \Drupal::routeMatch();
    $solution_id = (int) $route_match->getParameter('solution_id');
    $database = \Drupal::database();

    /* Get solution details */
    $solution_data = $database->select('lab_migration_solution', 's')
      ->fields('s')
      ->condition('id', $solution_id)
      ->execute()
      ->fetchObject();

    if (!$solution_data) {
      \Drupal::messenger()->addWarning(t('Invalid solution selected.'));
      return new RedirectResponse(Url::fromRoute('lab_migration.code_approval')->toString());
    }

    if ($solution_data->approval_status == 1) {
      \Drupal::messenger()->addWarning(t('This solution has already been approved. Are you sure you want to change the approval status?'));
    }
    if ($solution_data->approval_status == 2) {
      \Drupal::messenger()->addWarning(t('This solution has already been dis-approved. Are you sure you want to change the approval status?'));
    }

    /* Get experiment data */
    $experiment_data = $database->select('lab_migration_experiment', 'e')
      ->fields('e')
      ->condition('id', $solution_data->experiment_id)
      ->execute()
      ->fetchObject();

    /* Get proposal data */
    $proposal_data = $database->select('lab_migration_proposal', 'p')
      ->fields('p')
      ->condition('id', $experiment_data->proposal_id)
      ->execute()
      ->fetchObject();

    $form['#tree'] = TRUE;
    $form['lab_title'] = [
      '#type' => 'item',
      '#markup' => htmlspecialchars($proposal_data->lab_title ?? ''),
      '#title' => t('Title of the Lab'),
    ];
    $form['name'] = [
      '#type' => 'item',
      '#markup' => htmlspecialchars($proposal_data->name ?? ''),
      '#title' => t('Contributor Name'),
    ];
    $form['experiment']['number'] = [
      '#type' => 'item',
      '#markup' => htmlspecialchars($experiment_data->number ?? ''),
      '#title' => t('Experiment Number'),
    ];
    $form['experiment']['title'] = [
      '#type' => 'item',
      '#markup' => htmlspecialchars($experiment_data->title ?? ''),
      '#title' => t('Title of the Experiment'),
    ];
    $form['back_to_list'] = [
      '#type' => 'item',
      '#markup' => Link::fromTextAndUrl('Back to Code Approval List', Url::fromRoute('lab_migration.code_approval'))->toString(),
    ];
    $form['code_number'] = [
      '#type' => 'item',
      '#markup' => htmlspecialchars($solution_data->code_number ?? ''),
      '#title' => t('Code No'),
    ];
    $form['code_caption'] = [
      '#type' => 'item',
      '#markup' => htmlspecialchars($solution_data->caption ?? ''),
      '#title' => t('Caption'),
    ];

    /* Get solution files */
    $solution_files_html = '';
    $solution_files_q = $database->select('lab_migration_solution_files', 'f')
      ->fields('f')
      ->condition('solution_id', $solution_id)
      ->orderBy('id', 'ASC')
      ->execute();

    if ($solution_files_q) {
      while ($solution_files_data = $solution_files_q->fetchObject()) {
        switch ($solution_files_data->filetype) {
          case 'S': $code_file_type = 'Source'; break;
          case 'R': $code_file_type = 'Result'; break;
          case 'X': $code_file_type = 'Xcox'; break;
          default:  $code_file_type = 'Unknown'; break;
        }
        $url = Url::fromUri('internal:/lab-migration/download/solution/' . $solution_files_data->id);
        $link = Link::fromTextAndUrl($solution_files_data->filename, $url)->toString();
        $solution_files_html .= $link . ' (' . $code_file_type . ')<br/>';
      }
    }

    $form['solution_files'] = [
      '#type' => 'item',
      '#markup' => $solution_files_html,
      '#title' => t('Solution'),
    ];
    $form['approved'] = [
      '#type' => 'radios',
      '#options' => [
        '0' => 'Pending',
        '1' => 'Approved',
        '2' => 'Dis-approved (Solution will be deleted)',
      ],
      '#title' => t('Approval'),
      '#default_value' => $solution_data->approval_status,
    ];
    $form['message'] = [
      '#type' => 'textarea',
      '#title' => t('Reason for dis-approval'),
      '#states' => [
        'visible' => [
          ':input[name="approved"]' => ['value' => '2'],
        ],
        'required' => [
          ':input[name="approved"]' => ['value' => '2'],
        ],
      ],
    ];
    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Submit'),
    ];
    $form['cancel'] = [
      '#type' => 'markup',
      '#markup' => Link::fromTextAndUrl(t('Cancel'), Url::fromRoute('lab_migration.code_approval'))->toString(),
    ];
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    if ($form_state->getValue('approved') == '2') {
      if (strlen(trim($form_state->getValue('message') ?? '')) <= 30) {
        $form_state->setErrorByName('message', t('Please mention the reason for disapproval. (Minimum 30 characters required).'));
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $current_user = \Drupal::currentUser();
    $database = \Drupal::database();
    $route_match = \Drupal::routeMatch();
    $solution_id = (int) $route_match->getParameter('solution_id');

    /* Fetch structural records before deletion executes */
    $solution_data = $database->select('lab_migration_solution', 's')
      ->fields('s')
      ->condition('id', $solution_id)
      ->execute()
      ->fetchObject();

    if (!$solution_data) {
      \Drupal::messenger()->addError(t('Invalid solution selected.'));
      $form_state->setRedirect('lab_migration.code_approval');
      return;
    }

    $experiment_data = $database->select('lab_migration_experiment', 'e')
      ->fields('e')
      ->condition('id', $solution_data->experiment_id)
      ->execute()
      ->fetchObject();

    $proposal_data = $database->select('lab_migration_proposal', 'p')
      ->fields('p')
      ->condition('id', $experiment_data->proposal_id)
      ->execute()
      ->fetchObject();

    $user_data = User::load($proposal_data->uid);   
    $email_to = $user_data ? $user_data->getEmail() : $current_user->getEmail();
    $approver_uid = $current_user->id();
    
    $config = \Drupal::config('lab_migration.settings');
    $from = $config->get('lab_migration_from_email');
    $bcc  = $config->get('lab_migration_emails');
    $cc   = $config->get('lab_migration_cc_emails');

    $cc  = is_array($cc)  ? implode(',', $cc)  : $cc;
    $bcc = is_array($bcc) ? implode(',', $bcc) : $bcc;

    $langcode = \Drupal::languageManager()->getDefaultLanguage()->getId();
    $mail_manager = \Drupal::service('plugin.manager.mail');
    
    $approval_selection = $form_state->getValue('approved');

    if ($approval_selection == "0") {
      $database->update('lab_migration_solution')
        ->fields([
          'approval_status' => 0,
          'approver_uid' => $approver_uid,
          'approval_date' => time(),
        ])
        ->condition('id', $solution_id)
        ->execute();

      $param['solution_pending'] = [
        'solution_id' => $solution_id,
        'user_id'     => $user_data ? $user_data->id() : 0,
        'headers' => [
          'From' => $from,
          'MIME-Version' => '1.0',
          'Content-Type' => 'text/plain; charset=UTF-8; format=flowed; delsp=yes',
          'Content-Transfer-Encoding' => '8Bit',
          'X-Mailer' => 'Drupal',
          'Cc' => $cc,
          'Bcc' => $bcc,
        ],
      ];

      $mail_manager->mail('lab_migration', 'solution_pending', $email_to, $langcode, $param, $from, TRUE);
      \Drupal::messenger()->addStatus(t('Solution status updated to pending.'));
    }
    
    elseif ($approval_selection == "1") {
      $database->update('lab_migration_solution')
        ->fields([
          'approval_status' => 1,
          'approver_uid' => $approver_uid,
          'approval_date' => time(),
        ])
        ->condition('id', $solution_id)
        ->execute();

      $param['solution_approved'] = [
        'solution_id' => $solution_id,
        'user_id'     => $user_data ? $user_data->id() : 0,
        'headers' => [
          'From' => $from,
          'MIME-Version' => '1.0',
          'Content-Type' => 'text/plain; charset=UTF-8; format=flowed; delsp=yes',
          'Content-Transfer-Encoding' => '8Bit',
          'X-Mailer' => 'Drupal',
          'Cc' => $cc,
          'Bcc' => $bcc,
        ],
      ];

      $mail_manager->mail('lab_migration', 'solution_approved', $email_to, $langcode, $param, $from, TRUE);
      \Drupal::messenger()->addStatus(t('Solution approved successfully.'));
    }
    
    elseif ($approval_selection == "2") {
      // Build mail payload before execution triggers
      $param['solution_disapproved'] = [
        'solution_id'       => $proposal_data->id,
        'experiment_number' => $experiment_data->number ?? '',
        'experiment_title'  => $experiment_data->title ?? '',
        'solution_number'   => $solution_data->code_number ?? '',
        'solution_caption'  => $solution_data->caption ?? '',
        'user_id'           => $user_data ? $user_data->id() : 0,
        'message'           => $form_state->getValue('message'),
        'headers' => [
          'From' => $from,
          'MIME-Version' => '1.0',
          'Content-Type' => 'text/plain; charset=UTF-8; format=flowed; delsp=yes',
          'Content-Transfer-Encoding' => '8Bit',
          'X-Mailer' => 'Drupal',
          'Cc' => $cc,
          'Bcc' => $bcc,
        ],
      ];

      // Execute modern service method
      if (\Drupal::service("lab_migration_global")->lab_migration_delete_solution($solution_id)) {
        
        if (function_exists('del_lab_pdf')) {
          del_lab_pdf($proposal_data->id);
        }

        $result = $mail_manager->mail('lab_migration', 'solution_disapproved', $email_to, $langcode, $param, $from, TRUE);
        
        if ($result['result']) {
          \Drupal::messenger()->addStatus(t('Solution completely dis-approved and deleted. Email notification dispatched.'));
        } else {
          \Drupal::messenger()->addWarning(t('Solution record deleted, but email tracking failed to send.'));
        }
      }
      else {
        \Drupal::messenger()->addError(t('An error occurred while deleting the solution dependencies.'));
      }
    }

    $form_state->setRedirect('lab_migration.code_approval');
  }
}