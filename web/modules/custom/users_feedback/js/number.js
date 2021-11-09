(function ($, Drupal, drupalSettings) {
  'use strict';
  Drupal.behaviors.users_feedback_BasicFeedbackForm = {
    attach: function (context, settings) {
      document.getElementById('edit-guest-number').disabled = false;
      document.getElementById('edit-guest-number').onkeydown = function (e) {
        return !(/^[А-Яа-яA-Za-z ]$/.test(e.key));
      }
    }
  };
})(jQuery, Drupal, drupalSettings);
