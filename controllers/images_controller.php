<?php
class ImagesController extends AssetAppController {

  var $name = 'Images';
  var $scaffold;
  var $helpers = array('Asset.Images');
  var $uses = array();

  private function send_status($code, $desc) {
    $this->header('HTTP/1.1 ' . $code . ' ' . $desc);
    $this->header('Content-Type: image/png');

    $im = new Imagick(implode(DS, array(dirname(__FILE__), '..', 'vendors', 'img', $code . '.png')));
    echo $im;

    exit;
  }

  function thumbnail() {
    // Little hack to make things simpler
    $width = (int)@$this->params['named']['w'];
    $height = (int)@$this->params['named']['h'];

    if( empty($this->params['pass']) || 
	empty($this->params['named']) || 
	($width < 0) || 
	($height < 0) ||
	($width == 0 && $height == 0) ) {
      $this->send_status(400, 'Bad Request');
    }

    $path = str_replace('/', DS, WWW_ROOT . implode('/', $this->params['pass']));

    if( file_exists($path) ) {
      // Conditional GET magic :)
      // We'll use the simple If-Modified-Since method

      $last_modified = substr(date('r', filemtime($path)), 0, -5).'GMT';

      $if_modified_since = isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) ?
        stripslashes($_SERVER['HTTP_IF_MODIFIED_SINCE']) :
        false;

      if( $if_modified_since && $if_modified_since == $last_modified ) {
	$this->header('304 Not Modified');
      } else {
	$im = new Imagick($path);

	if( $width > $height ) {
	  $im->thumbnailImage($width, 0);
	} else {
	  $im->thumbnailImage(0, $height);
	}

	$data = getimagesize($path);
	$this->header('Content-Type: ' . $data['mime']);
	$this->header('Last-Modified: ' . $last_modified);

	echo $im;
      }
    } else {
      $this->send_status(404, 'Not Found');
    }

    exit;
  }
}
?>