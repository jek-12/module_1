<?php

namespace Drupal\users_feedback\Form;

use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\HtmlCommand;
use Drupal\Core\Ajax\MessageCommand;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\file\Entity\File;
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
    $form['#suffix'] = '<div id = "form-wrap">';
    $form['#prefix'] = '</div>';
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
//    '#theme' => 'image_style',
//      '#style_name' => 'wide',
//      '#uri' => File::load($cat->fid)->getFileUri(),
//    $image = $form_state->getValue('fid_avatar')[0];
//    $file = File::load($image);
//    $file->setPermanent();
//    $file->save();
//    $domain = $_SERVER['SERVER_NAME'];
//    $image_ava = '/modules/custom/users_feedback/file/index.jpeg';
//    $url_ava = "//{$domain}{$image_ava}";
//    $a = $entity->get($form['fid_avatar']);

//    $file = File::load(1);
//    $file->setPermanent();
//    $file->save();
//    $uri = _get_file_field_uri($form, 'fid_avatar');
//    $absolute_url = file_create_url($uri);
    $file = File::load(12);
    $file->setPermanent();
    $file->save();
    $form['fid_avatar'] = [
      '#type' => 'managed_file',
      '#title' => $this->t('Please, choose profile picture'),
      '#upload_location' => 'public://images/avatar/',
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
    $form['fid_def'] = [
      '#theme' => 'image_style',
      '#style_name' => 'wide',
      '#uri' => File::load(12)->getFileUri(),
      '#attributes' => [
        'class' => 'img-about',
        'alt' => 'cat',
      ],
    ];
    $form['fid_picture'] = [
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
  public function validateAjax($form, FormStateInterface $form_state) {
    $fieldName = $this->getTrigElement($form_state);
    $fieldValue = $this->getTrigValue($form_state);
    $st = $form_state->getValue('fid_avatar')[0];
    $wrapperQuerySelec = strval('#' . $fieldName . '-message');
    if (empty($fieldValue)) {
      $response = new AjaxResponse();
      $response->addCommand(new MessageCommand( t('@fieldName is empty. Please, enter the correct data', [
        '@fieldName' => $fieldName,
      ]), $wrapperQuerySelec, ['type' => 'warning'], TRUE));
      return $response;
    }
    elseif ($this->valid) {
      $response = new AjaxResponse();
      $response->addCommand(new MessageCommand( t('Correctly entered data: @fieldValue, for the @fieldName field.', [
        '@fieldName' => $fieldName,
        '@fieldValue' => $fieldValue,
      ]), $wrapperQuerySelec, ['type' => 'status'], TRUE));
      return $response;
    }
    else {
      $response = new AjaxResponse();
      $response->addCommand(new MessageCommand(t('Incorrectly entered data: @fieldValue, for the @fieldName field.', [
        '@fieldName' => $fieldName,
        '@fieldValue' => $fieldValue,
        ]),
        $wrapperQuerySelec, ['type' => 'error'], TRUE));
      return $response;
    }
  }

public function getTrigElement(FormStateInterface $form_state) {
  $triggeringElement = $form_state->getTriggeringElement()["#name"];
  return $triggeringElement;
}
public function getTrigValue(FormStateInterface $form_state) {
    $value = $form_state->getValue($this->getTrigElement($form_state));
    return $value;
}
  /**
   *
   */
  public function validateFields(FormStateInterface $form_state, $element, $regex) {
    $fieldName = strval($element);
    $fieldReg = strval($regex);
    if($this->getTrigElement($form_state) === $fieldName) {
      $reg = preg_match($fieldReg, $this->getTrigValue($form_state));
      if($reg) {
        $this->valid = TRUE;
      } else {
        $form_state->setErrorByName($this->getTrigElement($form_state));
        $this->valid = FALSE;
      }
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
    $this->validateFields($form_state, 'guest_name', '/^[a-zA-Z]{2,100}$/');
    $this->validateFields($form_state, 'guest_email', '/^([a-z0-9_-]+\.)*[a-z0-9_-]+@[a-z0-9_-]+(\.[a-z0-9_-]+)*\.[a-z]{2,7}$/');
    $this->validateFields($form_state, 'guest_number', '/^\+?3?8?(0\d{9})$/');
  }

  /**
   * {@inheritDoc}
   */
  // запхнути на сабміті дефолтне значення картинки якщо філд не заповнений!
  public function submitForm(&$form, FormStateInterface $form_state) {
    $forma = [
      '#type' => 'managed_file',
      '#title' => $this->t('Please, choose profile picture'),
      '#default_value' => [12],
      '#upload_location' => 'public://images/avatar/',
      '#upload_validators' => [
        'file_validate_extension' => ['jpeg jpg png'],
        'file_validate_size' => ['2097152'],
      ],
    ];
    if(empty($form_state->getValue('fid_avatar'))) {
      $c = $form_state->setValueForElement($form['fid_avatar'], $forma);
    }
  }

}
