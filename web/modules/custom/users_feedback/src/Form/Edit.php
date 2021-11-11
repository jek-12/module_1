<?php

namespace Drupal\users_feedback\Form;

use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\CloseModalDialogCommand;
use Drupal\Core\Ajax\RedirectCommand;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\file\Entity\File;

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
  protected object $card;

  /**
   * {@inheritDoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, int $id = NULL): array {
    $result = $this->database->select('users_feedback', 'c')
      ->fields('c', [])
      ->condition('id', $id)
      ->execute();
    $card = $result->fetch();
    $this->card = $card;
    $form = parent::buildForm($form, $form_state);
    $form['fid_avatar']['#default_value'] = $card->fid_avatar;
    $form['guest_name']['#default_value'] = $card->guest_name;
    $form['created_time']['#default_value'] = $card->created_time;
    $form['fid_picture']['#default_value'] = $card->fid_picture;
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
  public function submitForm(&$form, FormStateInterface $form_state): void {
    $updated = [
      'fid_avatar' => $this->card->fid_avatar,
      'guest_name' => $form_state->getValue('guest_name'),
      'guest_email' => $form_state->getValue('guest_email'),
      'guest_number' => $form_state->getValue('guest_number'),
      'fid_picture' => $form_state->getValue('fid_picture')[0],
      'feedback' => $form_state->getValue('feedback'),
    ];
    $file = File::load($form_state->getValue('fid_avatar')[0]);
    $file->setPermanent();
    $file->save();
    $ava = File::load($form_state->getValue('fid_avatar')[0]);
    $ava->setPermanent();
    $ava->save();
    $this->database
      ->update('users_feedback')
      ->condition('id', $this->card->id)
      ->fields($updated)
      ->execute();
  }

  /**
   * Redirect and update data.
   */
  public function rel(): AjaxResponse {
    $response = new AjaxResponse();
    $url = Url::fromRoute('users_feedback.main_page');
    $command = new RedirectCommand($url->toString());
    $response->addCommand($command);
    $response->addCommand(new CloseModalDialogCommand());
    return $response;
  }

}
