<?php

namespace Drupal\users_feedback\Form;

use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\HtmlCommand;
use Drupal\Core\Ajax\MessageCommand;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * This class created main form with.
 */
class BasicFeedbackForm extends FormBase {

  private $valid = FALSE;
  private $once = TRUE;
  private object $database;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container): BasicFeedbackForm {
    $services = parent::create($container);
    $services->messenger = $container->get('messenger');
    $services->database = $container->get('database');
    return $services;
  }

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
      '#prefix' => '<div class="name-validation-message"></div>',
      '#title' => $this->t('Please, enter your name:'),
      '#description' => $this->t('Only latin letters, max-length: 100'),
      '#required' => TRUE,
      '#ajax' => [
        'callback' => '::validateGuestNameAjax',
        'event' => '',
        'wrapper' => 'name-wrapper',
        'disable-refocus' => TRUE,
        'progress' => FALSE,
      ],
    ];
    $form['guest_email'] = [
      '#type' => 'email',
      '#prefix' => '<div class="email-validation-message"></div>',
      '#title' => $this->t('Please, enter your email:'),
      '#description' => $this->t('validate info'),
      '#required' => TRUE,
      '#ajax' => [
        'callback' => '::validateEmailAjax',
        'event' => 'keyup',
        'wrapper' => 'mail-wrapper',
        'disable-refocus' => TRUE,
        'progress' => FALSE,
      ],
    ];
    $form['number'] = [
      '#type' => 'tel',
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
      '#attached' => [
        'library' => [
          'users_feedback/main',
        ],
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

  //
  //  public function setWarnForField(FormStateInterface $form_state, string $field) {
  //    $fieldValue = $form_state->getValue($field);
  //    $form_state->setErrorByName($field, t('Please enter correct @fieldName, because @fieldValue is incorrect!', [
  //      '@fieldValue' => $fieldValue,
  //      '@fieldName' => $field,
  //    ]));
  //    return $fieldValue;
  //  }
  //  /**
  //   * {@inheritdoc}
  //   */
  //  public function validateGuestEmail(array &$form, FormStateInterface $form_state) {
  //    $response = new AjaxResponse();
  //
  //    $reg = preg_match('/^([a-z0-9_-]+\.)*[a-z0-9_-]+@[a-z0-9_-]+(\.[a-z0-9_-]+)*\.[a-z]{2,6}$/', $form_state->getValue('guest_email'));
  //
  //    if (true) {
  //      $response->addCommand(new HtmlCommand('.email-validation-message', strval($this->setWarnForField($form_state, 'guest_email'))));
  //    }
  //    else {
  //      # Убираем ошибку если она была и пользователь изменил почтовый адрес.
  //      $response->addCommand(new HtmlCommand('.email-validation-message', ''));
  //    }
  //    return $response;.

  /**
   * }.
   */
  public function validateEmailAjax() {
    if ($this->valid) {
      $response = new AjaxResponse();
      $response->addCommand(new MessageCommand('Your changes have been saved.', '#system-messages', ['type' => 'status'], TRUE));
      return $response;
    }
    else {
      $response = new AjaxResponse();
      // $a = $this->messenger->addMessage('Valid Email', 'status');
      $response->addCommand(new MessageCommand('Error.', '#system-messages', ['type' => 'error'], TRUE));
      return $response;
    }
  }


  /**
   *
   */
//  public function validateEmail(FormStateInterface $form_state, $field) {
//    $fieldName = strval($field);
//    $fieldValue = $form_state->getValue($fieldName);
//    $reg = preg_match('/^([a-z0-9_-]+\.)*[a-z0-9_-]+@[a-z0-9_-]+(\.[a-z0-9_-]+)*\.[a-z]{2,7}$/', $fieldValue);
//    if ($reg) {
//      $this->valid = TRUE;
//    }
//    else {
//      $form_state->setErrorByName($fieldName, $this->t('hala'));
//      $this->valid = FALSE;
//    }
//  }
  public function validateFields(FormStateInterface $form_state, $field, $regex) {
    $fieldName = strval($field);
    $fieldValue = $form_state->getValue($fieldName);
    $reg = preg_match(strval($regex), $fieldValue);
    if ($reg) {
      $this->valid = TRUE;
    }
    else {
      $form_state->setErrorByName($fieldName);
      $this->valid = FALSE;
    }
  }
  /**
   *\+?([0-9]{2})-?([0-9]{3})-?([0-9]{6,7})
   */
//  public function validateName(FormStateInterface $form_state, $field) {
//    $fieldName = strval($field);
//    $fieldValue = $form_state->getValue($fieldName);
//    $reg = preg_match('/^([a-z0-9_-]+\.)*[a-z0-9_-]+@[a-z0-9_-]+(\.[a-z0-9_-]+)*\.[a-z]{2,7}$/', $fieldValue);
//    if ($reg) {
//      $this->valid = TRUE;
//    }
//    else {
//      $form_state->setErrorByName($fieldName, $this->t('hala'));
//      $this->valid = FALSE;
//    }
//  }
  /**
   * {@inheritDoc}
   */
  public function validateForm(&$form, FormStateInterface $form_state) {
    if (strlen($form_state->getValue('guest_email')) > 5) {
      $this->validateFields($form_state, 'guest_email', '/^([a-z0-9_-]+\.)*[a-z0-9_-]+@[a-z0-9_-]+(\.[a-z0-9_-]+)*\.[a-z]{2,7}$/');
    }
    else {
      $form_state->setErrorByName('guest_email');
    }
  }

  /**
   * {@inheritDoc}
   */
  public function submitForm(&$form, FormStateInterface $form_state) {

  }

}
