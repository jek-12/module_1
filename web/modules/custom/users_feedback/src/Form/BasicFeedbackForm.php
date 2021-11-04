<?php

namespace Drupal\users_feedback\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * This class created main form with.
 */
class BasicFeedbackForm extends FormBase {

  /**
   * {@inheritDoc}
   */
  public function getFormId() {
    return 'BasicFeedbackForm';
  }

  /**
   * {@inheritDoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['system_messages'] = [
      '#markup' => '<div id = "system-messages"></div>',
      '#weight' => -1000,
    ];
    $form['#suffix'] = '<div id = "form-wrap">';
    $form['#prefix'] = '</div>';
    $form['guest_name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Please, enter your name:'),
      '#description' => $this->t('Only latin letters, max-length: 100'),
      '#required' => TRUE,
      '#ajax' => [
        'callback' => '',
        'event' => '',
        'wrapper' => 'name-wrapper',
        'disable-refocus' => TRUE,
        'progress' => FALSE,
      ],
    ];
    $form['guest_mail'] = [
      '#type' => 'email',
      '#title' => $this->t('Please, enter your email:'),
      '#description' => $this->t('validate info'),
      '#required' => TRUE,
      '#ajax' => [
        'callback' => '',
        'event' => '',
        'wrapper' => 'mail-wrapper',
        'disable-refocus' => TRUE,
        'progress' => FALSE,
      ],
    ];
    $form['number'] = [
      '#type' => 'number',
      '#title' => $this->t('Please, enter your phone number:'),
      '#description' => $this->t('validate info'),
      '#required' => TRUE,
      '#ajax' => [
        'callback' => '',
        'event' => '',
        'wrapper' => 'phone-wrapper',
        'disable-refocus' => TRUE,
        'progress' => FALSE,
      ],
    ];
    $form['feedback'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Please, enter your impressions:'),
      '#description' => 'validate info',
      '#required' => TRUE,
      '#ajax' => [
        'callback' => '',
        'event' => '',
        'wrapper' => 'feedback-wrapper',
        'disable-refocus' => TRUE,
        'progress' => FALSE,
      ],
    ];
    $form['fid_avatar'] = [
      '#type' => 'managed_file',
      '#title' => $this->t('Please, choose profile picture'),
      '#upload_validators' => [
        'file_validate_extension' => ['jpeg jpg png'],
        'file_validate_size' => ['2097152'],
      ],
      '#ajax' => [
        'callback' => '',
        'event' => '',
        'wrapper' => 'avatar-wrapper',
        'disable-refocus' => TRUE,
        'progress' => FALSE,
      ],
    ];
    $form['fid_picture'] = [
      '#type' => 'managed_file',
      '#title' => $this->t('Please, choose profile picture'),
      '#upload_validators' => [
        'file_validate_extension' => ['jpeg jpg png'],
        'file_validate_size' => ['5242880'],
      ],
      '#ajax' => [
        'callback' => '',
        'event' => '',
        'wrapper' => 'picture-wrapper',
        'disable-refocus' => TRUE,
        'progress' => FALSE,
      ],
    ];
    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Add feedback'),
      '#ajax' => [
        'callback' => '',
        'event' => '',
        'wrapper' => 'form-wrapper',
        'disable-refocus' => TRUE,
        'progress' => FALSE,
      ],
    ];
    return $form;
  }

  /**
   * {@inheritDoc}
   */
  public function submitForm(&$form, FormStateInterface $form_state) {

  }

}
