<?php

namespace Drupal\announcements\Form;

use Drupal\Core\Cache\CacheTagsInvalidator;
use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class AnnouncementTypeForm.
 */
class AnnouncementTypeForm extends EntityForm {

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    $form = parent::form($form, $form_state);

    /** @var \Drupal\announcements\Entity\AnnouncementTypeInterface $announcements_type */
    $announcements_type = $this->entity;
    $form['label'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Label'),
      '#maxlength' => 255,
      '#default_value' => $announcements_type->label(),
      '#description' => $this->t("Label for the Announcement type."),
      '#required' => TRUE,
    ];

    $form['id'] = [
      '#type' => 'machine_name',
      '#default_value' => $announcements_type->id(),
      '#machine_name' => [
        'exists' => '\Drupal\announcements\Entity\AnnouncementType::load',
      ],
      '#disabled' => !$announcements_type->isNew(),
    ];

    $form['dismissible'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Dismissible'),
      '#default_value' => $announcements_type->isDismissible(),
      '#description' => $this->t("Should announcements of this type be dismissible."),
    ];

    /** @var \Drupal\Core\Condition\ConditionManager $condition_manager */
    $condition_manager = \Drupal::service('plugin.manager.condition');
    $condition_plugin_definitions = $condition_manager->getDefinitions();
    $condition_plugin_options = [];

    foreach ($condition_plugin_definitions as $id => $definition) {
      $condition_plugin_options[$id] = $definition['label'] . ' (' . $id . ')';
    }
    $enabled_plugins = $announcements_type->getEnabledConditions();

    $form['enabled_conditions'] = [
      '#type' => 'checkboxes',
      '#title' => $this->t('Enabled Conditions'),
      '#options' => $condition_plugin_options,
      '#default_value' => $enabled_plugins,
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    /** @var \Drupal\announcements\Entity\AnnouncementTypeInterface $announcements_type */
    $announcements_type = $this->entity;
    $enabled_conditions = array_filter($announcements_type->getEnabledConditions());
    $announcements_type->setEnabledConditions($enabled_conditions);
    $status = $announcements_type->save();

    switch ($status) {
      case SAVED_NEW:
        $this->messenger()->addMessage($this->t('Created the %label Announcement type.', [
          '%label' => $announcements_type->label(),
        ]));
        break;

      default:
        $this->messenger()->addMessage($this->t('Saved the %label Announcement type.', [
          '%label' => $announcements_type->label(),
        ]));
    }
    $form_state->setRedirectUrl($announcements_type->toUrl('collection'));
  }

}
