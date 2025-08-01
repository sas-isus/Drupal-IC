<?php

namespace Drupal\Tests\linkchecker\Kernel;

use Drupal\Component\Render\FormattableMarkup;
use Drupal\KernelTests\KernelTestBase;
use Drupal\Tests\node\Traits\NodeCreationTrait;
use Drupal\linkchecker\Entity\LinkCheckerLink;
use Drupal\linkchecker\LinkCheckerLinkInterface;
use Drupal\node\Entity\NodeType;

/**
 * Test link extractor service.
 *
 * @group linkchecker
 */
class LinkcheckerLinkExtractorServiceTest extends KernelTestBase {

  use NodeCreationTrait;

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'node',
    'user',
    'system',
    'field',
    'filter',
    'text',
    'linkchecker',
    'path_alias',
  ];

  /**
   * HTTP protocol.
   *
   * @var string
   */
  protected $httpProtocol;

  /**
   * Base url.
   *
   * @var string
   */
  protected $baseUrl;

  /**
   * First folder in node path alias.
   *
   * @var string
   */
  protected $folder1;

  /**
   * Second folder in node path alias.
   *
   * @var string
   */
  protected $folder2;

  /**
   * The Linkchecker settings.
   *
   * @var \Drupal\Core\Config\Config
   */
  protected $linkcheckerSetting;

  /**
   * Current request.
   *
   * @var \Symfony\Component\HttpFoundation\Request
   */
  protected $request;

  /**
   * Extractor service.
   *
   * @var \Drupal\linkchecker\LinkExtractorService
   */
  protected $extractorService;

  /**
   * {@inheritdoc}
   */
  public function setUp(): void {
    parent::setUp();
    // Installing sequences table is deprecated since 10.2 release so call it
    // conditionally.
    // @see https://www.drupal.org/node/3349345
    if (version_compare(\Drupal::VERSION, '10.2', '<')) {
      $this->installSchema('system', 'sequences');
    }
    $this->installSchema('node', 'node_access');
    $this->installSchema('linkchecker', 'linkchecker_index');
    $this->installEntitySchema('user');
    $this->installEntitySchema('node');
    $this->installEntitySchema('path_alias');
    $this->installEntitySchema('linkcheckerlink');
    $this->installConfig(['field', 'node', 'filter', 'linkchecker']);

    $this->linkcheckerSetting = $this->container->get('config.factory')
      ->getEditable('linkchecker.settings');

    $this->request = $this->container->get('request_stack')
      ->getCurrentRequest();

    if (isset($this->request)) {
      $this->httpProtocol = $this->request->getScheme() . '://';
      $this->baseUrl = $this->request->getSchemeAndHttpHost() . $this->request->getBasePath();
    }
    else {
      $this->httpProtocol = $this->linkcheckerSetting->get('default_url_scheme');
      $this->baseUrl = $this->httpProtocol . $this->linkcheckerSetting->get('base_path');
    }

    // Save folder names in variables for reuse.
    $this->folder1 = $this->randomMachineName(10);
    $this->folder2 = $this->randomMachineName(5);

    $this->extractorService = $this->container->get('linkchecker.extractor');
  }

  /**
   * Test external URLs.
   */
  public function testExternalUrls() {
    // Disable blacklist URLs.
    $this->linkcheckerSetting->set('check.disable_link_check_for_urls', '');

    // Enable external URLs only.
    $this->linkcheckerSetting->set('check_links_types', LinkCheckerLinkInterface::TYPE_EXTERNAL);
    $this->linkcheckerSetting->save(TRUE);

    $extracted = $this->extractorService->getLinks($this->getTestUrlList());

    $countExpected = count($this->getExternalUrls()) + count($this->getBlacklistedUrls());
    $countExtracted = count($extracted);
    $this->assertEquals($countExpected, $countExtracted, new FormattableMarkup('Expected to extract @count but get @actual links.', [
      '@count' => $countExpected,
      '@actual' => $countExtracted,
    ]));

    foreach ($this->getExternalUrls() + $this->getBlacklistedUrls() as $url) {
      $this->assertTrue(in_array($url, $extracted), new FormattableMarkup('URL @url was not extracted!', ['@url' => $url]));
    }
  }

  /**
   * Test relative URLs.
   */
  public function testRelativeUrls() {
    // Disable blacklist URLs.
    $this->linkcheckerSetting->set('check.disable_link_check_for_urls', '');

    // Enable internal links URLs only.
    $this->linkcheckerSetting->set('check_links_types', LinkCheckerLinkInterface::TYPE_INTERNAL);
    $this->linkcheckerSetting->save(TRUE);

    $extracted = $this->extractorService->getLinks($this->getTestUrlList(), $this->baseUrl . '/' . $this->folder1 . '/' . $this->folder2);

    $countExpected = count($this->getRelativeUrls());
    $countExtracted = count($extracted);
    $this->assertEquals($countExpected, $countExtracted, new FormattableMarkup('Expected to extract @count but get @actual links.', [
      '@count' => $countExpected,
      '@actual' => $countExtracted,
    ]));

    foreach ($this->getRelativeUrls() as $url) {
      $this->assertTrue(in_array($url, $extracted), new FormattableMarkup('URL @url was not extracted!', ['@url' => $url]));
    }
  }

  /**
   * Test that we can extract internal URLs.
   */
  public function testInternalUrls() {
    // Enable internal links URLs only.
    $this->linkcheckerSetting->set('check_links_types', LinkCheckerLinkInterface::TYPE_ALL);
    $this->linkcheckerSetting->save(TRUE);
    $extracted = $this->extractorService->getLinks([
      'internal:/node/add',
    ], $this->baseUrl . '/' . $this->folder1 . '/' . $this->folder2);
    self::assertCount(1, $extracted);
  }

  /**
   * Test blacklisted URLs.
   */
  public function testBlacklistedUrls() {
    // Enable blacklist URLs.
    $this->linkcheckerSetting->set('check.disable_link_check_for_urls', "example.com\nexample.net\nexample.org");

    // Enable internal links URLs only.
    $this->linkcheckerSetting->set('check_links_types', LinkCheckerLinkInterface::TYPE_ALL);
    $this->linkcheckerSetting->save(TRUE);

    $extracted = $this->extractorService->getLinks($this->getTestUrlList(), $this->baseUrl . '/' . $this->folder1 . '/' . $this->folder2);

    $countExpected = count($this->getTestUrlList()) - count($this->getBlacklistedUrls()) - count($this->getUnsupportedUrls());
    $countExtracted = count($extracted);
    $this->assertEquals($countExpected, $countExtracted, new FormattableMarkup('Expected to extract @count but get @actual links.', [
      '@count' => $countExpected,
      '@actual' => $countExtracted,
    ]));

    foreach ($this->getBlacklistedUrls() as $url) {
      $this->assertNotTrue(in_array($url, $extracted), new FormattableMarkup('Blacklisted URL @url was extracted!', ['@url' => $url]));
    }
  }

  /**
   * Test isLinkExists method.
   */
  public function testIsExists() {
    $type = NodeType::create(['name' => 'Links', 'type' => 'links']);
    $type->save();
    node_add_body_field($type);

    $node = $this->createNode([
      'type' => 'links',
      'body' => [
        [
          'value' => '<a href="https://existing.com"></a>'
          . '<a href="https://example.com/existing"></a>'
          . '<a href="/existing.local"></a>',
        ],
      ],
    ]);

    $fieldDefinition = $node->get('body')->getFieldDefinition();
    $config = $fieldDefinition->getConfig($node->bundle());
    $config->setThirdPartySetting('linkchecker', 'scan', TRUE);
    $config->setThirdPartySetting('linkchecker', 'extractor', 'html_link_extractor');
    $config->save();

    $urls = [
      'https://existing.com',
      'https://not-existing.com',
      'https://example.com/existing',
      $this->baseUrl . '/existing.local',
    ];

    /** @var \Drupal\linkchecker\LinkCheckerLinkInterface[] $links */
    $links = [];
    foreach ($urls as $url) {
      $tmpLink = LinkCheckerLink::create([
        'url' => $url,
        'parent_entity_type_id' => $node->getEntityTypeId(),
        'parent_entity_id' => $node->id(),
        'entity_field' => 'body',
        'entity_langcode' => 'en',
      ]);
      $tmpLink->save();
      $links[] = $tmpLink;
    }

    // Extract all link with empty blacklist.
    $checkMap = [
      TRUE,
      FALSE,
      TRUE,
      TRUE,
    ];
    // Disable blacklist URLs.
    $this->linkcheckerSetting->set('check.disable_link_check_for_urls', '');
    $this->linkcheckerSetting->set('check_links_types', LinkCheckerLinkInterface::TYPE_ALL);
    $this->linkcheckerSetting->save(TRUE);

    foreach ($links as $key => $link) {
      $this->assertEquals($checkMap[$key], $this->extractorService->isLinkExists($link));
    }

    // Extract all links with example.com blacklisting.
    $checkMap = [
      TRUE,
      FALSE,
      FALSE,
      TRUE,
    ];
    // Enable blacklist URLs.
    $this->linkcheckerSetting->set('check.disable_link_check_for_urls', "example.com");
    $this->linkcheckerSetting->save(TRUE);

    foreach ($links as $key => $link) {
      $this->assertEquals($checkMap[$key], $this->extractorService->isLinkExists($link));
    }

    // Extract external only.
    $checkMap = [
      TRUE,
      FALSE,
      FALSE,
      FALSE,
    ];
    // Enable blacklist URLs.
    $this->linkcheckerSetting->set('check.disable_link_check_for_urls', "example.com");
    $this->linkcheckerSetting->set('check_links_types', LinkCheckerLinkInterface::TYPE_EXTERNAL);
    $this->linkcheckerSetting->save(TRUE);

    foreach ($links as $key => $link) {
      $this->assertEquals($checkMap[$key], $this->extractorService->isLinkExists($link));
    }

    // Extract internal only.
    $checkMap = [
      FALSE,
      FALSE,
      FALSE,
      TRUE,
    ];
    // Enable blacklist URLs.
    $this->linkcheckerSetting->set('check.disable_link_check_for_urls', "example.com");
    $this->linkcheckerSetting->set('check_links_types', LinkCheckerLinkInterface::TYPE_INTERNAL);
    $this->linkcheckerSetting->save(TRUE);

    foreach ($links as $key => $link) {
      $this->assertEquals($checkMap[$key], $this->extractorService->isLinkExists($link));
    }

    // If parent entity was removed.
    $node->delete();
    // Disable blacklist URLs.
    $this->linkcheckerSetting->set('check.disable_link_check_for_urls', '');
    $this->linkcheckerSetting->set('check_links_types', LinkCheckerLinkInterface::TYPE_ALL);
    $this->linkcheckerSetting->save(TRUE);
    // We should reload links to clear internal runtime cache.
    foreach (LinkCheckerLink::loadMultiple() as $link) {
      $this->assertFalse($this->extractorService->isLinkExists($link));
    }
  }

  /**
   * List of blacklisted links to test.
   *
   * @return array
   *   Links.
   */
  protected function getBlacklistedUrls() {
    return [
      'http://example.net/video/foo1.mov',
      'http://example.net/video/foo2.mov',
      'http://example.net/video/foo3.mov',
      'http://example.org/video/foo1.mp4',
      'http://example.org/video/foo2.mp4',
      'http://example.org/video/foo3.mp4',
      'http://example.org/video/foo4.mp4',
      'http://example.org/video/foo5.mp4',
      'http://example.org/video/foo6.mp4',
      'http://example.org/video/player1.swf',
      'http://example.org/video/player2.swf',
      'http://example.org/video/player3.swf',
      'http://example.com/iframe/',
      'http://example.com/foo bar/is_valid-hack.test',
      'http://example.com/ajax.html#key1=value1&key2=value2',
      'http://example.com/test.html#test',
      'http://example.com/test.html#test%20ABC',
    ];
  }

  /**
   * List of relative links to test.
   *
   * @return array
   *   Links.
   */
  protected function getRelativeUrls() {
    return [
      '../foo1/test.png' => $this->baseUrl . '/foo1/test.png',
      '/foo2/test.png' => $this->baseUrl . '/foo2/test.png',
      'test.png' => $this->baseUrl . '/' . $this->folder1 . '/test.png',
      '../foo1/bar1' => $this->baseUrl . '/foo1/bar1',
      './foo2/bar2' => $this->baseUrl . '/' . $this->folder1 . '/foo2/bar2',
      '../foo3/../foo4/foo5' => $this->baseUrl . '/foo4/foo5',
      './foo4/../foo5/foo6' => $this->baseUrl . '/' . $this->folder1 . '/foo5/foo6',
      './foo4/./foo5/foo6' => $this->baseUrl . '/' . $this->folder1 . '/foo4/foo5/foo6',
      './test/foo bar/is_valid-hack.test' => $this->baseUrl . '/' . $this->folder1 . '/test/foo bar/is_valid-hack.test',
      'flash.png' => $this->baseUrl . '/' . $this->folder1 . '/flash.png',
      'ritmo.mid' => $this->baseUrl . '/' . $this->folder1 . '/ritmo.mid',
      'my_ogg_video.ogg' => $this->baseUrl . '/' . $this->folder1 . '/my_ogg_video.ogg',
      'video.ogv' => $this->baseUrl . '/' . $this->folder1 . '/video.ogv',
      'flvplayer1.swf' => $this->baseUrl . '/' . $this->folder1 . '/flvplayer1.swf',
      'flvplayer2.swf' => $this->baseUrl . '/' . $this->folder1 . '/flvplayer2.swf',
      'foo.ogg' => $this->baseUrl . '/' . $this->folder1 . '/foo.ogg',
    ];
  }

  /**
   * List of external links to test.
   *
   * @return array
   *   Links.
   */
  protected function getExternalUrls() {
    return [
      'http://www.lagrandeepicerie.fr/#e-boutique/Les_produits_du_moment,2/coffret_vins_doux_naturels,149',
      'http://wetterservice.msn.de/phclip.swf?zip=60329&ort=Frankfurt',
      'http://www.msn.de/',
      'http://www.adobe.com/',
      'http://www.apple.com/qtactivex/qtplugin.cab',
      'http://www.theora.org/cortado.jar',
      'http://v2v.cc/~j/theora_testsuite/pixel_aspect_ratio.ogg',
      'http://v2v.cc/~j/theora_testsuite/pixel_aspect_ratio.mov',
      'http://v2v.cc/~j/theora_testsuite/320x240.ogg',
      'https://httpbin.org/anything/xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx',
    ];
  }

  /**
   * List of unsupported links to test.
   *
   * @return array
   *   Links.
   */
  protected function getUnsupportedUrls() {
    return [
      'mailto:test@example.com',
      'javascript:foo()',
      '',
      'data:image/png;base64,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',
      'https://httpbin.org/anything/xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxyz',
    ];
  }

  /**
   * List of links to test.
   *
   * @return array
   *   Key is a link, value is a config.
   */
  protected function getTestUrlList() {
    return array_merge($this->getExternalUrls(), $this->getBlacklistedUrls(), array_keys($this->getRelativeUrls()), $this->getUnsupportedUrls());
  }

}
