<?php

namespace Drupal\Tests\linkchecker\Kernel;

use Drupal\KernelTests\KernelTestBase;
use Drupal\Tests\node\Traits\NodeCreationTrait;
use Drupal\linkchecker\Entity\LinkCheckerLink;
use Drupal\node\Entity\NodeType;

/**
 * Test for linkchecker.clean_up service.
 *
 * @coversDefaultClass \Drupal\linkchecker\LinkCleanUp
 *
 * @group linkchecker
 */
class LinkcheckerCleanUpTest extends KernelTestBase {

  use NodeCreationTrait;

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'system',
    'linkchecker',
    'node',
    'user',
    'field',
    'filter',
    'text',
  ];

  /**
   * The link clean up service.
   *
   * @var \Drupal\linkchecker\LinkCleanUp
   */
  protected $linkCleanUp;

  /**
   * The link checker link storage.
   *
   * @var \Drupal\Core\Entity\EntityStorageInterface
   */
  protected $linkCheckerLinkStorage;

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
    $this->installEntitySchema('node');
    $this->installEntitySchema('user');
    $this->installEntitySchema('linkcheckerlink');
    $this->installConfig(['field', 'node', 'filter', 'linkchecker']);
    $this->installSchema('linkchecker', 'linkchecker_index');

    $this->linkCleanUp = $this->container->get('linkchecker.clean_up');
    $this->linkCheckerLinkStorage = $this->container->get('entity_type.manager')
      ->getStorage('linkcheckerlink');
  }

  /**
   * @covers ::cleanUpForEntity
   */
  public function testEntityCleanup() {
    $urls = [
      'http://httpstat.us/304',
      'http://httpstat.us/503',
    ];

    $node_type = NodeType::create([
      'type' => 'page',
    ]);
    $node_type->save();
    node_add_body_field($node_type);
    $node = $this->createNode([
      'type' => 'page',
      'body' => [
        [
          'value' => '
          <a href="http://httpstat.us/304">The nightmare continues</a>',
        ],
      ],
    ]);
    $fieldDefinition = $node->get('body')->getFieldDefinition();
    /** @var \Drupal\field\Entity\FieldConfig $config */
    $config = $fieldDefinition->getConfig($node->bundle());
    $config->setThirdPartySetting('linkchecker', 'scan', TRUE);
    $config->setThirdPartySetting('linkchecker', 'extractor', 'html_link_extractor');
    $config->save();

    foreach ($urls as $url) {
      $link = $this->createDummyLink($url);
      $link->setParentEntity($node);
      $link->setParentEntityFieldName($config->getName());
      $link->save();
    }

    // So, given we have 2 link entities that seemingly belong to this new
    // entity, and then we run the cleanup function to see which links should
    // really be there, we now expect it to be 1 link, since only one of them
    // are found in the node body.
    $this->assertCount(2, $this->linkCheckerLinkStorage->loadMultiple(NULL));
    $this->linkCleanUp->cleanUpForEntity($node);
    $this->assertCount(1, $this->linkCheckerLinkStorage->loadMultiple(NULL));
  }

  /**
   * @covers ::removeAllBatch
   */
  public function testRemoveAllBatch() {
    $urls = [
      'https://existing.com',
      'https://not-existing.com',
      'https://example.com/existing',
    ];

    foreach ($urls as $url) {
      $this->createDummyLink($url);
    }

    $this->assertCount(3, $this->linkCheckerLinkStorage->loadMultiple(NULL));

    $this->linkCleanUp->removeAllBatch();
    $this->runBatch();

    $this->assertEmpty($this->linkCheckerLinkStorage->loadMultiple($this->linkCheckerLinkStorage->loadMultiple(NULL)));
  }

  /**
   * Helper function for link creation.
   */
  protected function createDummyLink($url) {
    /** @var \Drupal\linkchecker\Entity\LinkCheckerLink $link */
    $link = LinkCheckerLink::create([
      'url' => $url,
      'parent_entity_type_id' => 'dummy_type',
      'parent_entity_id' => 1,
      'entity_field' => 'dummy_field',
      'entity_langcode' => 'en',
    ]);
    $link->save();
    return $link;
  }

  /**
   * Runs the currently set batch, if any exists.
   */
  protected function runBatch() {
    $batch = &batch_get();
    if ($batch) {
      $batch['progressive'] = FALSE;
      batch_process();
    }
  }

}
