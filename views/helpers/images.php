<?php
class ImagesHelper extends AppHelper {
  var $helpers = array('Html');

  function thumbnail_src($path, $width, $height) {
    if( $path[0] === '/' ) $path = substr($path, 1);
    return sprintf('/asset/images/thumbnail/w:%d/h:%d/%s/', $width, $height, $path);
  }

  function thumbnail($path, $width, $height, $options = array()) {
    if( strpos($path, '://') === false ) {
      $path = $this->thumbnail_src($path, $width, $height);
    } else {
      $this->log(__('Not creating a thumbnail for a remote file'), 'warning');
    }

    return $this->Html->image($path, $options);
  }
}
?>