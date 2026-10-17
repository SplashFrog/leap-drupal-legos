<?php

declare(strict_types=1);

namespace Drupal\leap_lego_video_carousel\Plugin\Field\FieldWidget;

use Drupal\Core\Field\Attribute\FieldWidget;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\Plugin\Field\FieldWidget\StringTextfieldWidget;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;

/**
 * A short textfield for a duration such as 33:52.
 */
#[FieldWidget(
  id: 'leap_video_duration_textfield',
  label: new TranslatableMarkup('Duration (m:ss)'),
  field_types: ['leap_video_duration'],
)]
class VideoDurationTextfieldWidget extends StringTextfieldWidget {

  public static function defaultSettings(): array {
    return ['size' => 8, 'placeholder' => '33:52'] + parent::defaultSettings();
  }

  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state): array {
    $element = parent::formElement($items, $delta, $element, $form, $form_state);
    $element['value']['#maxlength'] = 11;
    $element['value']['#description'] = $this->t('Minutes and seconds as a player shows them, for example 33:52, or 1:03:05 for a video over an hour.');
    return $element;
  }

}
