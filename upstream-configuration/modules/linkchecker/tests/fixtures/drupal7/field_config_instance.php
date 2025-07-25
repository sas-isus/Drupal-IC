<?php
// phpcs:ignoreFile
/**
 * @file
 * A database agnostic dump for testing purposes.
 *
 * This file was generated by the Drupal 9.2.6 db-tools.php script.
 */

use Drupal\Core\Database\Database;

$connection = Database::getConnection();

$connection->schema()->createTable('field_config_instance', array(
  'fields' => array(
    'id' => array(
      'type' => 'serial',
      'not null' => TRUE,
      'size' => 'normal',
    ),
    'field_id' => array(
      'type' => 'int',
      'not null' => TRUE,
      'size' => 'normal',
    ),
    'field_name' => array(
      'type' => 'varchar',
      'not null' => TRUE,
      'length' => '32',
      'default' => '',
    ),
    'entity_type' => array(
      'type' => 'varchar',
      'not null' => TRUE,
      'length' => '32',
      'default' => '',
    ),
    'bundle' => array(
      'type' => 'varchar',
      'not null' => TRUE,
      'length' => '128',
      'default' => '',
    ),
    'data' => array(
      'type' => 'blob',
      'not null' => TRUE,
      'size' => 'big',
    ),
    'deleted' => array(
      'type' => 'int',
      'not null' => TRUE,
      'size' => 'tiny',
      'default' => '0',
    ),
  ),
  'primary key' => array(
    'id',
  ),
  'indexes' => array(
    'field_name_bundle' => array(
      'field_name',
      'entity_type',
      'bundle',
    ),
    'deleted' => array(
      'deleted',
    ),
  ),
  'mysql_character_set' => 'utf8mb3',
));

$connection->insert('field_config_instance')
->fields(array(
  'id',
  'field_id',
  'field_name',
  'entity_type',
  'bundle',
  'data',
  'deleted',
))
->values(array(
  'id' => '1',
  'field_id' => '1',
  'field_name' => 'comment_body',
  'entity_type' => 'comment',
  'bundle' => 'comment_node_page',
  'data' => 'a:6:{s:5:"label";s:7:"Comment";s:8:"settings";a:2:{s:15:"text_processing";i:1;s:18:"user_register_form";b:0;}s:8:"required";b:1;s:7:"display";a:1:{s:7:"default";a:5:{s:5:"label";s:6:"hidden";s:4:"type";s:12:"text_default";s:6:"weight";i:0;s:8:"settings";a:0:{}s:6:"module";s:4:"text";}}s:6:"widget";a:4:{s:4:"type";s:13:"text_textarea";s:8:"settings";a:1:{s:4:"rows";i:5;}s:6:"weight";s:1:"0";s:6:"module";s:4:"text";}s:11:"description";s:0:"";}',
  'deleted' => '0',
))
->values(array(
  'id' => '2',
  'field_id' => '2',
  'field_name' => 'body',
  'entity_type' => 'node',
  'bundle' => 'page',
  'data' => 'a:6:{s:5:"label";s:4:"Body";s:6:"widget";a:4:{s:4:"type";s:26:"text_textarea_with_summary";s:8:"settings";a:2:{s:4:"rows";i:20;s:12:"summary_rows";i:5;}s:6:"weight";i:-4;s:6:"module";s:4:"text";}s:8:"settings";a:3:{s:15:"display_summary";b:1;s:15:"text_processing";i:1;s:18:"user_register_form";b:0;}s:7:"display";a:2:{s:7:"default";a:5:{s:5:"label";s:6:"hidden";s:4:"type";s:12:"text_default";s:8:"settings";a:0:{}s:6:"module";s:4:"text";s:6:"weight";i:0;}s:6:"teaser";a:5:{s:5:"label";s:6:"hidden";s:4:"type";s:23:"text_summary_or_trimmed";s:8:"settings";a:1:{s:11:"trim_length";i:600;}s:6:"module";s:4:"text";s:6:"weight";i:0;}}s:8:"required";b:0;s:11:"description";s:0:"";}',
  'deleted' => '0',
))
->values(array(
  'id' => '3',
  'field_id' => '1',
  'field_name' => 'comment_body',
  'entity_type' => 'comment',
  'bundle' => 'comment_node_article',
  'data' => 'a:6:{s:5:"label";s:7:"Comment";s:8:"settings";a:2:{s:15:"text_processing";i:1;s:18:"user_register_form";b:0;}s:8:"required";b:1;s:7:"display";a:1:{s:7:"default";a:5:{s:5:"label";s:6:"hidden";s:4:"type";s:12:"text_default";s:6:"weight";i:0;s:8:"settings";a:0:{}s:6:"module";s:4:"text";}}s:6:"widget";a:4:{s:4:"type";s:13:"text_textarea";s:8:"settings";a:1:{s:4:"rows";i:5;}s:6:"weight";i:0;s:6:"module";s:4:"text";}s:11:"description";s:0:"";}',
  'deleted' => '0',
))
->values(array(
  'id' => '4',
  'field_id' => '2',
  'field_name' => 'body',
  'entity_type' => 'node',
  'bundle' => 'article',
  'data' => 'a:6:{s:5:"label";s:4:"Body";s:6:"widget";a:4:{s:4:"type";s:26:"text_textarea_with_summary";s:8:"settings";a:2:{s:4:"rows";i:20;s:12:"summary_rows";i:5;}s:6:"weight";s:2:"-4";s:6:"module";s:4:"text";}s:8:"settings";a:3:{s:15:"display_summary";b:1;s:15:"text_processing";i:1;s:18:"user_register_form";b:0;}s:7:"display";a:2:{s:7:"default";a:5:{s:5:"label";s:6:"inline";s:4:"type";s:17:"smart_trim_format";s:6:"weight";s:1:"0";s:8:"settings";a:9:{s:9:"trim_link";s:1:"0";s:11:"trim_length";s:3:"550";s:9:"trim_type";s:5:"chars";s:11:"trim_suffix";s:6:"...0.0";s:9:"more_link";s:1:"1";s:9:"more_text";s:12:"Read more...";s:15:"summary_handler";s:6:"ignore";s:12:"trim_options";a:1:{s:4:"text";s:4:"text";}s:18:"trim_preserve_tags";s:11:"<p><a><img>";}s:6:"module";N;}s:6:"teaser";a:5:{s:5:"label";s:6:"hidden";s:4:"type";s:23:"text_summary_or_trimmed";s:8:"settings";a:1:{s:11:"trim_length";i:600;}s:6:"module";s:4:"text";s:6:"weight";i:0;}}s:8:"required";b:0;s:11:"description";s:0:"";}',
  'deleted' => '0',
))
->values(array(
  'id' => '5',
  'field_id' => '3',
  'field_name' => 'field_tags',
  'entity_type' => 'node',
  'bundle' => 'article',
  'data' => 'a:6:{s:5:"label";s:4:"Tags";s:11:"description";s:63:"Enter a comma-separated list of words to describe your content.";s:6:"widget";a:4:{s:4:"type";s:21:"taxonomy_autocomplete";s:6:"weight";s:2:"-4";s:8:"settings";a:2:{s:4:"size";i:60;s:17:"autocomplete_path";s:21:"taxonomy/autocomplete";}s:6:"module";s:8:"taxonomy";}s:7:"display";a:2:{s:7:"default";a:5:{s:5:"label";s:6:"inline";s:4:"type";s:29:"taxonomy_term_reference_plain";s:6:"weight";s:2:"10";s:8:"settings";a:0:{}s:6:"module";s:8:"taxonomy";}s:6:"teaser";a:5:{s:4:"type";s:28:"taxonomy_term_reference_link";s:6:"weight";i:10;s:5:"label";s:5:"above";s:8:"settings";a:0:{}s:6:"module";s:8:"taxonomy";}}s:8:"settings";a:1:{s:18:"user_register_form";b:0;}s:8:"required";b:0;}',
  'deleted' => '0',
))
->values(array(
  'id' => '6',
  'field_id' => '4',
  'field_name' => 'field_image',
  'entity_type' => 'node',
  'bundle' => 'article',
  'data' => 'a:6:{s:5:"label";s:5:"Image";s:11:"description";s:40:"Upload an image to go with this article.";s:8:"required";b:0;s:8:"settings";a:9:{s:14:"file_directory";s:11:"field/image";s:15:"file_extensions";s:16:"png gif jpg jpeg";s:12:"max_filesize";s:0:"";s:14:"max_resolution";s:0:"";s:14:"min_resolution";s:0:"";s:9:"alt_field";b:1;s:11:"title_field";s:0:"";s:13:"default_image";i:0;s:18:"user_register_form";b:0;}s:6:"widget";a:4:{s:4:"type";s:11:"image_image";s:8:"settings";a:2:{s:18:"progress_indicator";s:8:"throbber";s:19:"preview_image_style";s:9:"thumbnail";}s:6:"weight";s:2:"-1";s:6:"module";s:5:"image";}s:7:"display";a:2:{s:7:"default";a:5:{s:5:"label";s:6:"hidden";s:4:"type";s:5:"image";s:6:"weight";s:2:"-1";s:8:"settings";a:2:{s:11:"image_style";s:5:"large";s:10:"image_link";s:0:"";}s:6:"module";s:5:"image";}s:6:"teaser";a:5:{s:5:"label";s:6:"hidden";s:4:"type";s:5:"image";s:8:"settings";a:2:{s:11:"image_style";s:6:"medium";s:10:"image_link";s:7:"content";}s:6:"weight";i:-1;s:6:"module";s:5:"image";}}}',
  'deleted' => '0',
))
->values(array(
  'id' => '7',
  'field_id' => '5',
  'field_name' => 'field_list',
  'entity_type' => 'node',
  'bundle' => 'article',
  'data' => 'a:7:{s:5:"label";s:4:"list";s:6:"widget";a:5:{s:6:"weight";s:1:"0";s:4:"type";s:14:"options_select";s:6:"module";s:7:"options";s:6:"active";i:1;s:8:"settings";a:1:{s:12:"apply_chosen";s:1:"1";}}s:8:"settings";a:1:{s:18:"user_register_form";b:0;}s:7:"display";a:1:{s:7:"default";a:5:{s:5:"label";s:5:"above";s:4:"type";s:8:"list_key";s:6:"weight";s:2:"11";s:8:"settings";a:0:{}s:6:"module";s:4:"list";}}s:8:"required";i:0;s:11:"description";s:0:"";s:13:"default_value";N;}',
  'deleted' => '0',
))
->values(array(
  'id' => '8',
  'field_id' => '6',
  'field_name' => 'field_broken_link',
  'entity_type' => 'node',
  'bundle' => 'article',
  'data' => 'a:6:{s:5:"label";s:10:"brokenLink";s:6:"widget";a:4:{s:4:"type";s:26:"text_textarea_with_summary";s:6:"weight";s:1:"1";s:8:"settings";a:2:{s:4:"rows";i:20;s:12:"summary_rows";i:5;}s:6:"module";s:4:"text";}s:8:"settings";a:3:{s:15:"text_processing";i:1;s:15:"display_summary";i:0;s:18:"user_register_form";b:0;}s:7:"display";a:1:{s:7:"default";a:5:{s:5:"label";s:5:"above";s:4:"type";s:12:"text_default";s:8:"settings";a:0:{}s:6:"module";s:4:"text";s:6:"weight";i:12;}}s:8:"required";b:0;s:11:"description";s:0:"";}',
  'deleted' => '0',
))
->values(array(
  'id' => '9',
  'field_id' => '1',
  'field_name' => 'comment_body',
  'entity_type' => 'comment',
  'bundle' => 'comment_node_term_merge',
  'data' => 'a:6:{s:5:"label";s:7:"Comment";s:8:"settings";a:2:{s:15:"text_processing";i:1;s:18:"user_register_form";b:0;}s:8:"required";b:1;s:7:"display";a:1:{s:7:"default";a:5:{s:5:"label";s:6:"hidden";s:4:"type";s:12:"text_default";s:6:"weight";i:0;s:8:"settings";a:0:{}s:6:"module";s:4:"text";}}s:6:"widget";a:4:{s:4:"type";s:13:"text_textarea";s:8:"settings";a:1:{s:4:"rows";i:5;}s:6:"weight";i:0;s:6:"module";s:4:"text";}s:11:"description";s:0:"";}',
  'deleted' => '0',
))
->values(array(
  'id' => '10',
  'field_id' => '2',
  'field_name' => 'body',
  'entity_type' => 'node',
  'bundle' => 'term_merge',
  'data' => 'a:6:{s:5:"label";s:4:"Body";s:6:"widget";a:4:{s:4:"type";s:26:"text_textarea_with_summary";s:8:"settings";a:2:{s:4:"rows";i:20;s:12:"summary_rows";i:5;}s:6:"weight";s:2:"-4";s:6:"module";s:4:"text";}s:8:"settings";a:3:{s:15:"display_summary";b:1;s:15:"text_processing";i:1;s:18:"user_register_form";b:0;}s:7:"display";a:2:{s:7:"default";a:5:{s:5:"label";s:6:"hidden";s:4:"type";s:12:"text_default";s:8:"settings";a:0:{}s:6:"module";s:4:"text";s:6:"weight";i:0;}s:6:"teaser";a:5:{s:5:"label";s:6:"hidden";s:4:"type";s:23:"text_summary_or_trimmed";s:8:"settings";a:1:{s:11:"trim_length";i:600;}s:6:"module";s:4:"text";s:6:"weight";i:0;}}s:8:"required";b:0;s:11:"description";s:0:"";}',
  'deleted' => '0',
))
->values(array(
  'id' => '12',
  'field_id' => '8',
  'field_name' => 'field_tax',
  'entity_type' => 'node',
  'bundle' => 'term_merge',
  'data' => 'a:7:{s:5:"label";s:11:"Ingredients";s:6:"widget";a:5:{s:6:"weight";s:2:"-3";s:4:"type";s:21:"taxonomy_autocomplete";s:6:"module";s:8:"taxonomy";s:6:"active";i:0;s:8:"settings";a:2:{s:4:"size";i:60;s:17:"autocomplete_path";s:21:"taxonomy/autocomplete";}}s:8:"settings";a:1:{s:18:"user_register_form";b:0;}s:7:"display";a:1:{s:7:"default";a:5:{s:5:"label";s:5:"above";s:4:"type";s:28:"taxonomy_term_reference_link";s:8:"settings";a:0:{}s:6:"module";s:8:"taxonomy";s:6:"weight";i:1;}}s:8:"required";i:0;s:11:"description";s:0:"";s:13:"default_value";N;}',
  'deleted' => '0',
))
->values(array(
  'id' => '13',
  'field_id' => '1',
  'field_name' => 'comment_body',
  'entity_type' => 'comment',
  'bundle' => 'comment_node_car',
  'data' => 'a:6:{s:5:"label";s:7:"Comment";s:8:"settings";a:2:{s:15:"text_processing";i:1;s:18:"user_register_form";b:0;}s:8:"required";b:1;s:7:"display";a:1:{s:7:"default";a:5:{s:5:"label";s:6:"hidden";s:4:"type";s:12:"text_default";s:6:"weight";i:0;s:8:"settings";a:0:{}s:6:"module";s:4:"text";}}s:6:"widget";a:4:{s:4:"type";s:13:"text_textarea";s:8:"settings";a:1:{s:4:"rows";i:5;}s:6:"weight";i:0;s:6:"module";s:4:"text";}s:11:"description";s:0:"";}',
  'deleted' => '0',
))
->values(array(
  'id' => '14',
  'field_id' => '2',
  'field_name' => 'body',
  'entity_type' => 'node',
  'bundle' => 'car',
  'data' => 'a:6:{s:5:"label";s:4:"Body";s:6:"widget";a:4:{s:4:"type";s:26:"text_textarea_with_summary";s:8:"settings";a:2:{s:4:"rows";i:20;s:12:"summary_rows";i:5;}s:6:"weight";s:2:"-4";s:6:"module";s:4:"text";}s:8:"settings";a:3:{s:15:"display_summary";b:1;s:15:"text_processing";i:1;s:18:"user_register_form";b:0;}s:7:"display";a:2:{s:7:"default";a:5:{s:5:"label";s:6:"hidden";s:4:"type";s:12:"text_default";s:8:"settings";a:0:{}s:6:"module";s:4:"text";s:6:"weight";i:0;}s:6:"teaser";a:5:{s:5:"label";s:6:"hidden";s:4:"type";s:23:"text_summary_or_trimmed";s:8:"settings";a:1:{s:11:"trim_length";i:600;}s:6:"module";s:4:"text";s:6:"weight";i:0;}}s:8:"required";b:0;s:11:"description";s:0:"";}',
  'deleted' => '0',
))
->values(array(
  'id' => '16',
  'field_id' => '10',
  'field_name' => 'field_car',
  'entity_type' => 'node',
  'bundle' => 'car',
  'data' => 'a:7:{s:5:"label";s:3:"car";s:6:"widget";a:5:{s:6:"weight";s:2:"-3";s:4:"type";s:21:"taxonomy_autocomplete";s:6:"module";s:8:"taxonomy";s:6:"active";i:0;s:8:"settings";a:2:{s:4:"size";i:60;s:17:"autocomplete_path";s:21:"taxonomy/autocomplete";}}s:8:"settings";a:1:{s:18:"user_register_form";b:0;}s:7:"display";a:1:{s:7:"default";a:5:{s:5:"label";s:5:"above";s:4:"type";s:28:"taxonomy_term_reference_link";s:8:"settings";a:0:{}s:6:"module";s:8:"taxonomy";s:6:"weight";i:1;}}s:8:"required";i:0;s:11:"description";s:0:"";s:13:"default_value";N;}',
  'deleted' => '0',
))
->execute();
