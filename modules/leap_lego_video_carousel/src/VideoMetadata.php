<?php

declare(strict_types=1);

namespace Drupal\leap_lego_video_carousel;

use Drupal\Component\Utility\Html;
use Drupal\media\MediaInterface;
use Drupal\media\OEmbed\ProviderException;
use Drupal\media\OEmbed\Resource;
use Drupal\media\OEmbed\ResourceException;
use Drupal\media\Plugin\media\Source\OEmbedInterface;

/**
 * Reads a video's displayable metadata, and prepares text for GLightbox.
 */
final class VideoMetadata {

  /**
   * Resolved oEmbed resources for this request, keyed by source URL.
   *
   * @var array<string, \Drupal\media\OEmbed\Resource|null>
   */
  private static array $resources = [];

  /**
   * The display title: alternative title, provider title, then media name.
   */
  public static function title(MediaInterface $media): string {
    $display = self::fieldString($media, 'field_video_display_title');
    if ($display !== '') {
      return $display;
    }
    $provider = trim((string) self::resource($media)?->getTitle());
    return $provider !== '' ? $provider : trim((string) $media->label());
  }

  public static function summary(MediaInterface $media): string {
    return self::fieldString($media, 'field_video_summary');
  }

  public static function duration(MediaInterface $media): ?Duration {
    $value = self::fieldString($media, 'field_video_duration');
    return $value === '' ? NULL : Duration::fromString($value);
  }

  public static function providerName(MediaInterface $media): string {
    return trim((string) self::resource($media)?->getProvider()?->getName());
  }

  /**
   * Builds the accessible name and lightbox caption attributes for a video link.
   *
   * GLightbox writes data-title and data-description into the lightbox with
   * innerHTML, and the title comes from a third-party provider, so both are
   * escaped for an HTML context. They are their own attributes because
   * GLightbox reads those directly, unlike the data-glightbox string, whose
   * parser misreads a value containing another option's `key:`.
   *
   * @return array<string, \Stringable|string>
   *   Attribute values keyed by attribute name.
   */
  public static function linkAttributes(string $title, string $description = '', ?Duration $duration = NULL): array {
    $name = $duration === NULL
      ? t('Play video: @title', ['@title' => $title])
      : t('Play video: @title, @duration', ['@title' => $title, '@duration' => $duration->spoken()]);
    $attributes = ['aria-label' => $name, 'data-title' => Html::escape($title)];
    if (trim($description) !== '') {
      $attributes['data-description'] = Html::escape($description);
    }
    return $attributes;
  }

  /**
   * Points every data-gallery attribute in markup at one gallery id.
   *
   * The GLightbox formatters derive the id from the media's parent entity,
   * which is empty when the media is rendered directly, so every link on the
   * page would otherwise share one gallery.
   */
  public static function scopeGallery(string $html, string $gallery): string {
    if ($gallery === '') {
      return $html;
    }
    return preg_replace_callback(
      '/data-gallery="[^"]*"/',
      static fn (): string => 'data-gallery="' . Html::escape($gallery) . '"',
      $html
    ) ?? $html;
  }

  /**
   * Fetches the oEmbed resource, or NULL when there is none to fetch.
   *
   * Calls the oEmbed services directly: $source->getMetadata() shows the
   * visitor an error message when the provider cannot be reached. Core caches
   * a successful fetch, but not a failed one, so the outcome (including NULL)
   * is remembered per URL for the request; an unreachable provider is then
   * tried once, not once per caller. ResourceFetcher throws
   * \InvalidArgumentException for a provider missing from the registry.
   */
  private static function resource(MediaInterface $media): ?Resource {
    $source = $media->getSource();
    if (!$source instanceof OEmbedInterface) {
      return NULL;
    }
    $url = $source->getSourceFieldValue($media);
    if (empty($url)) {
      return NULL;
    }
    $key = (string) $url;
    if (array_key_exists($key, self::$resources)) {
      return self::$resources[$key];
    }
    try {
      $resource_url = \Drupal::service('media.oembed.url_resolver')->getResourceUrl($url);
      $resource = \Drupal::service('media.oembed.resource_fetcher')->fetchResource($resource_url);
    }
    catch (ResourceException | ProviderException | \InvalidArgumentException) {
      $resource = NULL;
    }
    return self::$resources[$key] = $resource;
  }

  private static function fieldString(MediaInterface $media, string $field): string {
    if (!$media->hasField($field)) {
      return '';
    }
    $value = $media->get($field)->value;
    return is_string($value) ? trim($value) : '';
  }

}
