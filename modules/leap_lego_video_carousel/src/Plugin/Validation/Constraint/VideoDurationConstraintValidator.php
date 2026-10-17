<?php

declare(strict_types=1);

namespace Drupal\leap_lego_video_carousel\Plugin\Validation\Constraint;

use Drupal\leap_lego_video_carousel\Duration;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Validates the LeapVideoDuration constraint.
 */
class VideoDurationConstraintValidator extends ConstraintValidator {

  public function validate(mixed $value, Constraint $constraint): void {
    if ($value === NULL || trim((string) $value) === '') {
      return;
    }
    if (Duration::fromString((string) $value) === NULL) {
      $this->context->addViolation($constraint->message);
    }
  }

}
