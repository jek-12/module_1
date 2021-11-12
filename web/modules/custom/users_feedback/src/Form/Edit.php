<?php

namespace Drupal\users_feedback\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\file\Entity\File;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Custom form Edit.
 */
class Edit extends BasicFeedbackForm {

  /**
   * {@inheritDoc}
   */
  public function getFormId(): string {
    return 'editForm';
  }

  /**
   * Cat to edit if any.
   *
   * @var object
   */
  protected object $obj;


  function defAvatar(FormStateInterface $form_state) {
    if(isset($form_state->getValue('fid_avatar')[0])) {
     return $form_state->getValue('fid_avatar')[0];
    }
    else {
      return 12;
    }
  }
  function defPicture(FormStateInterface $form_state) {
    if(isset($form_state->getValue('fid_picture')[0])) {
      return $form_state->getValue('fid_picture')[0];
    }
    else {
      return NULL;
    }
  }
  /**
   * {@inheritDoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, int $id = NULL): array {
    $result = $this->database->select('users_feedback', 'c')
      ->fields('c', [])
      ->condition('id', $id)
      ->execute();
    $card = $result->fetch();
    $this->obj = $card;
    $form = parent::buildForm($form, $form_state);
    $form['guest_name']['#default_value'] = $card->guest_name;
    $form['created_time']['#default_value'] = $card->created_time;
    $form['feedback']['#default_value'] = $card->feedback;
    $form['guest_email']['#default_value'] = $card->guest_email;
    $form['guest_number']['#default_value'] = $card->guest_number;
    $form['submit']['#value'] = $this->t('Edit');
    return $form;
  }

  /**
   * Submit edited version of the cat.
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  public function submitForm(&$form, FormStateInterface $form_state) {
    $updated = [
      'fid_avatar' => $this->defAvatar($form_state),
      'guest_name' => $form_state->getValue('guest_name'),
      'guest_email' => $form_state->getValue('guest_email'),
      'guest_number' => $form_state->getValue('guest_number'),
      'fid_picture' => $this->defPicture($form_state),
      'feedback' => $form_state->getValue('feedback'),
    ];
    $file = File::load($this->defAvatar($form_state));
    $file->setPermanent();
    $file->save();
    $ava = File::load($this->defPicture($form_state));
    $ava->setPermanent();
    $ava->save();
    $this->database
      ->update('users_feedback')
      ->condition('id', $this->obj->id)
      ->fields($updated)
      ->execute();
  }

//  /**
//   * Redirect and update data.
//   */
  // Public function rel(): AjaxResponse {
  //    $response = new AjaxResponse();
  //    $url = Url::fromRoute('users_feedback.main_page');
  //    $command = new RedirectCommand($url->toString());
  //    $response->addCommand($command);
  //    $response->addCommand(new CloseModalDialogCommand());
  //    return $response;
  //  }.
}
