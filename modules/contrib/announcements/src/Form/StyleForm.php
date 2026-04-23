<?php

namespace Drupal\announcements\Form;

use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class StyleForm.
 */
class StyleForm extends EntityForm {

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    $form = parent::form($form, $form_state);

    $announcements_style = $this->entity;
    $form['label'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Label'),
      '#maxlength' => 255,
      '#default_value' => $announcements_style->label(),
      '#description' => $this->t("Label for the Announcement Style."),
      '#required' => TRUE,
    ];

    $form['id'] = [
      '#type' => 'machine_name',
      '#default_value' => $announcements_style->id(),
      '#machine_name' => [
        'exists' => '\Drupal\announcements\Entity\Style::load',
      ],
      '#disabled' => !$announcements_style->isNew(),
    ];

    $form['extra_classes'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Extra Classes'),
      '#maxlength' => 255,
      '#default_value' => $announcements_style->getExtraClasses(),
      '#description' => $this->t("Extra classes to be applied to the announcement during rendering."),
    ];

    /* You will need additional form elements for your custom properties. */

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $announcements_style = $this->entity;
    $status = $announcements_style->save();

    switch ($status) {
      case SAVED_NEW:
        $this->messenger()->addMessage($this->t('Created the %label Announcement Style.', [
          '%label' => $announcements_style->label(),
        ]));
        break;

      default:
        $this->messenger()->addMessage($this->t('Saved the %label Announcement Style.', [
          '%label' => $announcements_style->label(),
        ]));
    }
    $form_state->setRedirectUrl($announcements_style->toUrl('collection'));
  }

}
