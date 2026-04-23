<?php
 
namespace Drupal\hackathon_submission\Services;

use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\File\FileSystemInterface;
use Drupal\Core\Link;
use Drupal\Core\Url;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Drupal\Core\Database\Database;
use Drupal\Core\DrupalKernel;
use Drupal\user\Entity\User;

class HackathonSubmissionGLobalFunction{

 function hackathon_submission_files_path() {
  return $_SERVER['DOCUMENT_ROOT'] . base_path() . 'esim_uploads/hackathon_submission_uploads/';
}

function mscd_hackathon_submission_files_path() {
  return $_SERVER['DOCUMENT_ROOT'] . base_path() . 'esim_uploads/hackathon_submission_uploads/mscd_uploads/';
}

function _hs_dir_name($circuit_name, $participant_name)
{
    $circuit_name = hs_ucname($circuit_name);
    $participant_name = hs_ucname($participant_name);
    $dir_name = $circuit_name . ' By ' . $participant_name;
    $directory_name = str_replace("__", "_", str_replace(" ", "_", str_replace("/","_", trim($dir_name))));
    return $directory_name;
}

}
 