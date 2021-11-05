<?php

namespace Drupal\users_feedback\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\users_feedback\Form\BasicFeedbackForm;

class HomePageController extends ControllerBase {

  public function formRender(): array {
    $form_class = BasicFeedbackForm::class;
    $build['form'] = \Drupal::formBuilder()->getForm($form_class);
    return $build;
  }

  public function form () {
    $a = $this->formRender();
    return [
      '#theme' => 'main',
      '#main_form' => $a,
    ];
  }

}
