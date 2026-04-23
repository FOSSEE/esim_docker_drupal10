<?php

namespace Drupal\announcements\Form;

use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class RegionForm.
 */
class RegionForm extends EntityForm {

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    $form = parent::form($form, $form_state);

    $announcements_region = $this->entity;
    $form['label'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Label'),
      '#maxlength' => 255,
      '#default_value' => $announcements_region->label(),
      '#description' => $this->t("Label for the Announcement Region."),
      '#required' => TRUE,
    ];

    $form['id'] = [
      '#type' => 'machine_name',
      '#default_value' => $announcements_region->id(),
      '#machine_name' => [
        'exists' => '\Drupal\announcements\Entity\Region::load',
      ],
      '#disabled' => !$announcements_region->isNew(),
    ];

    /* You will need additional form elements for your custom properties. */

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $announcements_region = $this->entity;
    $status = $announcements_region->save();

    switch ($status) {
      case SAVED_NEW:
        $this->messenger()->addMessage($this->t('Created the %label Announcement Region.', [
          '%label' => $announcements_region->label(),
        ]));
        break;

      default:
        $this->messenger()->addMessage($this->t('Saved the %label Announcement Region.', [
          '%label' => $announcements_region->label(),
        ]));
    }
    \Drupal::service('plugin.manager.block')->clearCachedDefinitions();
    drupal_flush_all_caches();
    $form_state->setRedirectUrl($announcements_region->toUrl('collection'));
  }

}
