<?php

namespace Drupal\users_feedback\Controller;

use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\OpenModalDialogCommand;
use Drupal\Core\Controller\ControllerBase;
use Drupal\file\Entity\File;
use Drupal\users_feedback\Form\BasicFeedbackForm;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * A controller that displays the form and data obtained from a database.
 */
class HomePageController extends ControllerBase {

  public $database;

  /**
   * Injecting external services into objects(database).
   *
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container): HomePageController {
    $services = parent::create($container);
    $services->database = $container->get('database');
    return $services;
  }


  /**
   * Render BasicFeedbackForm.
   */
  public function formRender(): array {
    $form_class = BasicFeedbackForm::class;
    $build['form'] = \Drupal::formBuilder()->getForm($form_class);
    return $build;
  }

  public function viewCard() {
    $db = $this->database->select('users_feedback', 'a')
      ->fields('a', [])
      ->orderBy('id', 'DESC')
      ->execute();
    $result = $db->fetchAll();
    $cards = [];
    foreach ($result as $data) {
      $picture = [
        '#theme' => 'image_style',
        '#style_name' => 'medium',
        '#alt' => 'picture',
        '#uri' => File::load($data->fid_picture)->getFileUri(),
      ];
      $avatar = [
        '#theme' => 'image_style',
        '#style_name' => 'thumbnail',
        '#alt' => 'avatar',
        '#uri' => File::load($data->fid_avatar)->getFileUri(),
      ];
      $cards[] = [
        'id' => $data->id,
        'guest_name' => $data->guest_name,
        'guest_email' => $data->guest_email,
        'guest_number' => $data->guest_number,
        'feedback' => $data->feedback,
        'fid_avatar' => $avatar,
        'fid_picture' => $picture,
        'created_time' => $data->created_time,
      ];
    }
    return $cards;
  }
  public function showCurrentCard($id): AjaxResponse {
    $db = $this->database->select('users_feedback', 'b')
      ->fields('b', [])
      ->condition('id', $id)
      ->execute();
    $result = $db->fetch();
    $result->fid_picture = [
        '#theme' => 'image_style',
        '#style_name' => 'medium',
        '#alt' => 'picture',
        '#uri' => File::load($result->fid_picture)->getFileUri(),
      ];
    $result->fid_avatar = [
        '#theme' => 'image_style',
        '#style_name' => 'thumbnail',
        '#alt' => 'avatar',
        '#uri' => File::load($result->fid_avatar)->getFileUri(),
      ];
      $cards = [
        '#theme' => 'about',
        '#guest_name' => $result->guest_name,
        '#guest_email' => $result->guest_email,
        '#guest_number' => $result->guest_number,
        '#feedback' => $result->feedback,
        '#fid_avatar' => $result->fid_avatar,
        '#fid_picture' => $result->fid_picture,
        '#created_time' => $result->created_time,
      ];
      $dialog_options = [
        'width' => '800',
        'height' => '500',
        'dialogClass' => 'm',
        'modal' => 'true',
      ];
    $response = new AjaxResponse();
    $response->addCommand(new OpenModalDialogCommand('about', $cards, $dialog_options));
    return $response;
  }
  /**
   * Returns data to the template.
   */
  public function form(): array {
    $form = $this->formRender();
    return [
      '#theme' => 'main',
      '#main_form' => $form,
      '#cards' => $this->viewCard(),
    ];
  }

}
