<?php

namespace Drupal\Tests\twitter_profile_widget\Kernel;

use Drupal\KernelTests\KernelTestBase;
use Drupal\twitter_profile_widget\TwitterProfile;

/**
 * Tests the "TwitterProfile" service, which builds Twitter API queries.
 *
 * @coversDefaultClass \Drupal\twitter_profile_widget\TwitterProfile
 * @group twitter_profile_widget
 *
 * @see Drupal\twitter_profile_widget\TwitterProfile
 */
class TwitterProfileTest extends KernelTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'twitter_profile_widget',
  ];

  /**
   * The Twitter service under test.
   *
   * @var \Drupal\twitter_profile_widget\TwitterProfile
   */
  protected $twitterProfile;

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();
    $this->twitterProfile = $this->container->get('twitter_profile_widget.twitterprofile');
  }

  /**
   * Get an accessible method using reflection.
   */
  public function getAccessibleMethod($class_name, $method_name) {
    $class = new \ReflectionClass($class_name);
    $method = $class->getMethod($method_name);
    $method->setAccessible(TRUE);
    return $method;
  }

  /**
   * Test TwitterProfile::buildQuery().
   *
   * Test that an expected Twitter REST URL for the twitter timeline returns.
   * Since buildQuery() is a protected method, alter the class using reflection.
   *
   * @dataProvider queryDataProvider
   */
  public function testQuery($config, $expected) {
    $class = new \ReflectionClass($this->twitterProfile);
    $reflection_method = $class->getMethod('buildQuery');
    $reflection_method->setAccessible(TRUE);
    // Get a reflected, accessible version of the buildQuery() method.
    // $protected_method = $this->getAccessibleMethod(
    //   'Drupal\twitter_profile_widget\TwitterProfile',
    //   'buildQuery'
    // );
    // // Create a new TwitterProfile object.
    // $pp = new TwitterProfile();
    // Use the reflection to invoke on the object.
    $result = $reflection_method->invokeArgs($this->twitterProfile, [
      $config['account'],
      $config['type'],
      $config['timeline'],
      $config['search'],
      $config['replies'],
      $config['retweets'],
    ]);
    // Make an assertion.
    $this->assertEquals($expected, $result);
  }

  /**
   * Data provider for testQuery().
   */
  public function queryDataProvider() {
    return [
      [
        [
          'account' => 'testuser',
          'type' => 'timeline',
          'timeline' => 'mytimeline',
          'search' => 'search param',
          'replies' => 1,
          'retweets' => 1,
        ],
        '/lists/statuses.json?count=10&list_id=&owner_screen_name=testuser&include_rts=1',
      ],
      [
        [
          'account' => 'testuser',
          'type' => 'timeline',
          'timeline' => 'mytimeline',
          'search' => 'search param',
          'replies' => 0,
          'retweets' => 0,
        ],
        '/lists/statuses.json?count=10&list_id=&owner_screen_name=testuser&include_rts=0&exclude_replies=1',
      ],
      [
        [
          'account' => 'testuser',
          'type' => 'search',
          'timeline' => 'mytimeline',
          'search' => 'search param',
          'replies' => 1,
          'retweets' => 1,
        ],
        '/search/tweets.json?q=search+param&count=10',
      ],
      [
        [
          'account' => 'testuser',
          'type' => 'search',
          'timeline' => 'mytimeline',
          'search' => '#search . param%',
          'replies' => 1,
          'retweets' => 1,
        ],
        '/search/tweets.json?q=%23search+.+param%25&count=10',
      ],
      [
        [
          'account' => 'testuser',
          'type' => 'favorites',
          'timeline' => 'mytimeline',
          'search' => 'search param',
          'replies' => 1,
          'retweets' => 1,
        ],
        '/favorites/list.json?count=10&screen_name=testuser',
      ],
      [
        [
          'account' => 'testuser',
          'type' => 'status',
          'timeline' => 'mytimeline',
          'search' => 'search param',
          'replies' => 1,
          'retweets' => 1,
        ],
        '/statuses/user_timeline.json?count=10&screen_name=testuser&include_rts=1',
      ],
      [
        [
          'account' => 'testuser',
          'type' => 'status',
          'timeline' => 'mytimeline',
          'search' => 'search param',
          'replies' => 0,
          'retweets' => 0,
        ],
        'url' => '/statuses/user_timeline.json?count=10&screen_name=testuser&include_rts=0&exclude_replies=1',
      ],
    ];
  }

}
