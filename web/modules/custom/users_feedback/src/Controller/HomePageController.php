<?php

namespace Drupal\users_feedback\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\users_feedback\Form\BasicFeedbackForm;

/**
 * A controller that displays the form and data obtained from a database.
 */
class HomePageController extends ControllerBase {

  /**
   * Render BasicFeedbackForm.
   */
  public function formRender(): array {
    $form_class = BasicFeedbackForm::class;
    $build['form'] = \Drupal::formBuilder()->getForm($form_class);
    return $build;
  }

  /**
   * Returns data to the template.
   */
  public function form(): array {
    $form = $this->formRender();
    return [
      '#theme' => 'main',
      '#main_form' => $form,
    ];
  }

}
