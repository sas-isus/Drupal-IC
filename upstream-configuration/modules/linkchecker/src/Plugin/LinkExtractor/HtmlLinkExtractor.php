<?php

namespace Drupal\linkchecker\Plugin\LinkExtractor;

use Drupal\Component\Utility\Html;
use Drupal\linkchecker\Plugin\LinkExtractorBase;

/**
 * Created a class to extract links.
 *
 * @LinkExtractor(
 *   id = "html_link_extractor",
 *   label = @Translation("HTML extractor"),
 *   field_types = {
 *     "text",
 *     "text_long",
 *     "text_with_summary",
 *   }
 * )
 */
class HtmlLinkExtractor extends LinkExtractorBase {

  /**
   * {@inheritdoc}
   */
  protected function extractUrlFromField(array $value) {
    $string = $value['value'];

    if (empty($string)) {
      return [];
    }

    $html_dom = Html::load($string);
    $urls = [];

    // Finds all hyperlinks in the content.
    if ($this->linkcheckerSetting->get('extract.from_a') == TRUE) {
      $links = $html_dom->getElementsByTagName('a');
      foreach ($links as $link) {
        $urls[] = $link->getAttribute('href');
      }

      $links = $html_dom->getElementsByTagName('area');
      foreach ($links as $link) {
        $urls[] = $link->getAttribute('href');
      }
    }

    // Finds all audio links in the content.
    if ($this->linkcheckerSetting->get('extract.from_audio') == TRUE) {
      $audios = $html_dom->getElementsByTagName('audio');
      foreach ($audios as $audio) {
        $urls[] = $audio->getAttribute('src');

        // Finds source tags with links in the audio tag.
        $sources = $audio->getElementsByTagName('source');
        foreach ($sources as $source) {
          $urls[] = $source->getAttribute('src');
        }
        // Finds track tags with links in the audio tag.
        $tracks = $audio->getElementsByTagName('track');
        foreach ($tracks as $track) {
          $urls[] = $track->getAttribute('src');
        }
      }
    }

    // Finds embed tags with links in the content.
    if ($this->linkcheckerSetting->get('extract.from_embed') == TRUE) {
      $embeds = $html_dom->getElementsByTagName('embed');
      foreach ($embeds as $embed) {
        $urls[] = $embed->getAttribute('src');
        $urls[] = $embed->getAttribute('pluginurl');
        $urls[] = $embed->getAttribute('pluginspage');
      }
    }

    // Finds iframe tags with links in the content.
    if ($this->linkcheckerSetting->get('extract.from_iframe') == TRUE) {
      $iframes = $html_dom->getElementsByTagName('iframe');
      foreach ($iframes as $iframe) {
        $urls[] = $iframe->getAttribute('src');
      }
    }

    // Finds img tags with links in the content.
    if ($this->linkcheckerSetting->get('extract.from_img') == TRUE) {
      $imgs = $html_dom->getElementsByTagName('img');
      foreach ($imgs as $img) {
        $urls[] = $img->getAttribute('src');
        $urls[] = $img->getAttribute('longdesc');
      }
    }

    // Finds object/param tags with links in the content.
    if ($this->linkcheckerSetting->get('extract.from_object') == TRUE) {
      $objects = $html_dom->getElementsByTagName('object');
      foreach ($objects as $object) {
        $urls[] = $object->getAttribute('data');
        $urls[] = $object->getAttribute('codebase');

        // Finds param tags with links in the object tag.
        $params = $object->getElementsByTagName('param');
        foreach ($params as $param) {
          // @todo Try to extract links in unknown "flashvars" values
          //   (e.g., file=http://, data=http://).
          $names = ['archive', 'filename', 'href', 'movie', 'src', 'url'];
          if ($param->hasAttribute('name') && in_array($param->getAttribute('name'), $names)) {
            $urls[] = $param->getAttribute('value');
          }

          $movie_sources = ['movie'];
          if ($param->hasAttribute('src') && in_array($param->getAttribute('src'), $movie_sources)) {
            $urls[] = $param->getAttribute('value');
          }
        }
      }
    }

    // Finds video tags with links in the content.
    if ($this->linkcheckerSetting->get('extract.from_video') == TRUE) {
      $videos = $html_dom->getElementsByTagName('video');
      foreach ($videos as $video) {
        $urls[] = $video->getAttribute('poster');
        $urls[] = $video->getAttribute('src');

        // Finds source tags with links in the video tag.
        $sources = $video->getElementsByTagName('source');
        foreach ($sources as $source) {
          $urls[] = $source->getAttribute('src');
        }
        // Finds track tags with links in the audio tag.
        $tracks = $video->getElementsByTagName('track');
        foreach ($tracks as $track) {
          $urls[] = $track->getAttribute('src');
        }
      }
    }

    // Remove empty values.
    $urls = array_filter($urls);
    // Remove duplicate urls.
    $urls = array_unique($urls);

    return $urls;
  }

}
