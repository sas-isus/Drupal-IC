<?php

namespace Drupal\draggableviews;

use Drupal\media\Entity\Media;
use Drupal\views\ViewExecutable;
use Drupal\Component\Utility\Html;

/**
 * Class DraggableViews.
 */
class DraggableViews {

  /**
   * The view.
   *
   * @var \Drupal\views\ViewExecutable
   */
  public $view;

  /**
   * Constructs DraggableViewsRows object.
   *
   * @param \Drupal\views\ViewExecutable $view
   *   Views object.
   */
  public function __construct(ViewExecutable $view) {
    $this->view = $view;
  }

  /**
   * Get index by id.
   */
  public function getIndex($id) {
    foreach ($this->view->result as $item) {
      if ($item->_entity instanceof Media) {
        // Media index name.
        $name = 'mid';
      }
      else {
        // Node index name. The default one.
        $name = 'nid';
      }
      if ($item->$name == $id) {
        return $item->index;
      }
    }
    return FALSE;
  }

  /**
   * Get depth by index.
   */
  public function getDepth($index): int {
    if (!isset($this->view->result[$index])) {
      return 0;
    }
    $row = $this->view->result[$index];
    $parentIndex = (int) $this->getIndex($row->draggableviews_structure_parent);

    // Prevent infinite recursion.
    if ($parentIndex === $index) {
      return 0;
    }
    // If parent is available, set parent's depth +1.
    return (!empty($row->draggableviews_structure_parent)) ? $this->getDepth($parentIndex) + 1 : 0;
  }

  /**
   * Get parent by index.
   */
  public function getParent($index) {
    return isset($this->view->result[$index]->draggableviews_structure_parent) ? $this->view->result[$index]->draggableviews_structure_parent : 0;
  }

  /**
   * Get ancestor by index.
   */
  public function getAncestor($index) {
    $row = $this->view->result[$index];
    return !empty($row->draggableviews_structure_parent) ? $this->getAncestor($this->getIndex($row->draggableviews_structure_parent)) : $index;
  }

  /**
   * Return value by it's name and index.
   */
  public function getValue($name, $index) {
    return $this->view->result[$index]->$name;
  }

  /**
   * Get HTML id for draggableviews table.
   */
  public function getHtmlId() {
    return Html::getId('draggableviews-table-' . $this->view->id() . '-' . $this->view->current_display);
  }

}
