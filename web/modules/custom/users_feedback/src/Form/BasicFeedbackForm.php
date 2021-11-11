<?php

namespace Drupal\users_feedback\Form;

use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\MessageCommand;
use Drupal\Core\Ajax\RedirectCommand;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\file\Entity\File;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class creat main form with validating of each existing fields.
 */
class BasicFeedbackForm extends FormBase {

  /**
   * Determines the validation state.
   *
   * @var bool
   */
  private bool $valid = FALSE;
  /**
   * Database connection object.
   *
   * @var \Drupal\Core\Database\Connection|object|null
   */
  protected object $database;

  /**
   * Injecting external services into objects(database).
   *
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container): BasicFeedbackForm {
    $services = parent::create($container);
    $services->database = $container->get('database');
    return $services;
  }

  /**
   * The unique string identifying the desired form.
   *
   * {@inheritDoc}
   */
  public function getFormId(): string {
    return 'BasicFeedbackForm';
  }

  /**
   * Builds and processes a form for a given form ID.
   *
   * {@inheritDoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state): array {
    $form['system_messages'] = [
      '#markup' => '<div id = "system-messages"></div>',
      '#weight' => -1000,
    ];
    $form['#suffix'] = '<div id = "form-wrap">';
    $form['#prefix'] = '</div>';
    $form['fid_avatar'] = [
      '#type' => 'managed_file',
      '#title' => $this->t('Please, choose profile picture'),
      '#upload_location' => 'public://images/avatar/',
      '#default_value' => [12],
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
    $form['guest_name'] = [
      '#type' => 'textfield',
      '#prefix' => '<div id = "guest_name-message"></div>',
      '#title' => $this->t('Please, enter your name:'),
      '#description' => $this->t('Only latin letters, min/max-length: 2/100.'),
      '#placeholder' => $this->t('name'),
      '#required' => TRUE,
      '#ajax' => [
        'callback' => '::validateAjax',
        'event' => 'keyup',
        'wrapper' => 'name-wrapper',
        'disable-refocus' => TRUE,
        'progress' => FALSE,
      ],
    ];
    $form['guest_email'] = [
      '#type' => 'textfield',
      '#prefix' => '<div id = "guest_email-message"></div>',
      '#title' => $this->t('Please, enter your email:'),
      '#description' => $this->t('my_name@service_name.domen'),
      '#placeholder' => $this->t('my_name@service_name.domen'),
      '#required' => TRUE,
      '#ajax' => [
        'callback' => '::validateAjax',
        'event' => 'keyup',
        'wrapper' => 'mail-wrapper',
        'disable-refocus' => TRUE,
        'progress' => FALSE,
      ],
    ];
    $form['guest_number'] = [
      '#type' => 'tel',
      '#title' => $this->t('Please, enter your phone number:'),
      '#prefix' => '<div id = "guest_number-message"></div>',
      '#description' => $this->t('Only numbers. +380681234567'),
      '#placeholder' => $this->t('+380681234567'),
      '#maxlength' => 13,
      '#required' => TRUE,
      '#ajax' => [
        'callback' => '::validateAjax',
        'event' => 'keyup',
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
        'callback' => 'validateAjax',
        'event' => '',
        'wrapper' => 'feedback-wrapper',
        'disable-refocus' => TRUE,
        'progress' => FALSE,
      ],
    ];
//    $form['picture_wraper'] = [
//      '#type' => 'container',
//      '#attributes' => [
//        'class' => ['asdasd', 'asdasdsasdasd'],
//      ],
//    ];
    $form['picture_wraper']['fid_picture'] = [
      '#type' => 'managed_file',
      '#title' => $this->t('Please, choose picture'),
      '#upload_location' => 'public://images/picture/',
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
//  public function reloadAjax(): AjaxResponse {
//    $response = new AjaxResponse();
//    $url = Url::fromRoute('users_feedback.main_page');
//    $command = new RedirectCommand($url->toString());
//    $response->addCommand($command);
//    return $response;
//  }
  /**
   * Ajax callback for validate each form field.
   */
  public function validateAjax($form, FormStateInterface $form_state): AjaxResponse {
    // Name of field which is validating now (keyup). Data get from form_state.
    $fieldName = $this->getTrigElement($form_state);
    // Value of field which is validating now (keyup). Data get from form_state.
    $fieldValue = $this->getTrigValue($form_state);
    // Wrapper where help message will be outputted.
    $wrapperQuerySelec = ('#' . $fieldName . '-message');
    $response = new AjaxResponse();
    if (empty($fieldValue)) {
      $response->addCommand(new MessageCommand(t('@fieldName is empty. Please, enter the correct data', [
        '@fieldName' => $fieldName,
      ]), $wrapperQuerySelec, ['type' => 'warning'], TRUE));
    }
    elseif ($this->valid) {
      $response->addCommand(new MessageCommand(t('Correctly entered data: @fieldValue, for the @fieldName field.', [
        '@fieldName' => $fieldName,
        '@fieldValue' => $fieldValue,
      ]), $wrapperQuerySelec, ['type' => 'status'], TRUE));
    }
    else {
      $response->addCommand(new MessageCommand(t('Incorrectly entered data: @fieldValue, for the @fieldName field.', [
        '@fieldName' => $fieldName,
        '@fieldValue' => $fieldValue,
      ]),
        $wrapperQuerySelec, ['type' => 'error'], TRUE));
    }
    return $response;
  }

  /**
   * Get element name from form_state when validateForm is processed.
   */
  public function getTrigElement(FormStateInterface $form_state) {
    return $form_state->getTriggeringElement()["#name"];
  }

  /**
   * Get element name from form_state when validateForm is processed.
   */
  public function getTrigValue(FormStateInterface $form_state) {
    return $form_state->getValue($this->getTrigElement($form_state));
  }

  /**
   * Custom validate method which call in validateForm.
   *
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   Stores information about the state of a form.
   * @param string $element
   *   Name of field which will be validate by current regex.
   * @param string $regex
   *   Regex which will be test current $element.
   */
  public function validateFields(FormStateInterface $form_state, string $element, string $regex): void {
    $fieldName = $element;
    $fieldReg = $regex;
    if ($this->getTrigElement($form_state) === $fieldName) {
      $reg = preg_match($fieldReg, $this->getTrigValue($form_state));
      if ($reg) {
        $this->valid = TRUE;
      }
      else {
        $form_state->setErrorByName($this->getTrigElement($form_state));
        $this->valid = FALSE;
      }
    }
  }

  /**
   * {@inheritDoc}
   */
  public function validateForm(&$form, FormStateInterface $form_state): void {
    $this->validateFields($form_state, 'guest_name', '/^[a-zA-Z]{2,100}$/');
    $this->validateFields($form_state, 'guest_email', '/^([a-z0-9_-]+\.)*[a-z0-9_-]+@[a-z0-9_-]+(\.[a-z0-9_-]+)*\.[a-z]{2,7}$/');
    $this->validateFields($form_state, 'guest_number', '/^\+?3?8?(0\d{9})$/');
  }

  /**
   *
   */
  public function saveFile(FormStateInterface $form_state, $image) {
    $img = $this->existAvatar($form_state, $image);
    $file = File::load($img);
    $file->setPermanent();
    $file->save();
    return (int) $img;
  }

  /**
   *
   */
  public function getValue(FormStateInterface $form_state, string $value) {
    return $form_state->getValue($value);
  }

  /**
   *
   */
  public function existAvatar(FormStateInterface $form_state, $image) {
    if (!empty($form_state->getValue($image)[0])) {
      return $form_state->getValue($image)[0];
    }
    else {
      return "12";
    }
  }

  public function pushData(array $form, FormStateInterface $form_state) {
    $requestTime = \Drupal::time()->getRequestTime();
    $data = [
      'guest_name' => $this->getValue($form_state, 'guest_name'),
      'guest_email' => $this->getValue($form_state, 'guest_email'),
      'guest_number' => $this->getValue($form_state, 'guest_number'),
      'feedback' => $this->getValue($form_state, 'feedback'),
      'fid_avatar' => $this->saveFile($form_state, 'fid_avatar'),
      'fid_picture' => $this->saveFile($form_state, 'fid_picture'),
      'created_time' => $requestTime,
    ];
    if($form_state->hasAnyErrors()){
      $this->database->insert('users_feedback')->fields($data)->execute();
    }

  }
  /**
   * {@inheritdoc}
   */

  /**
   * запхнути на сабміті дефолтне значення картинки якщо філд не заповнений!
   */
  public function submitForm(&$form, FormStateInterface $form_state) {
      $this->pushData($form, $form_state);
  }

  // If (empty($form_state->getValue('fid_avatar'))) {
  // $default_avatar = [                      ->format("Y-m-d H:i:s");
  // '#type' => 'managed_file',
  // '#title' => $this->t('Please, choose profile picture'),
  // '#default_value' => [12],
  // '#upload_location' => 'public://images/avatar/',
  // '#upload_validators' => [
  // 'file_validate_extension' => ['jpeg jpg png'],
  // 'file_validate_size' => ['2097152'],
  // ],
  // ];
  // $c = $form_state->setValueForElement($form['fid_avatar'], $default_avatar);
  // }.
}
