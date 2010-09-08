<?php 
class ImageHelper extends Helper {

    var $helpers = array('Html');
	var $cacheDir = 'imagecache';
	var $expires  = '+1 week';
	
	/**
	 * Automatically resizes an image and returns formatted IMG tag
	 *
	 * @param string $path Path to the image file, relative to the webroot/img/ directory.
	 * @param integer $width Image of returned image
	 * @param integer $height Height of returned image
	 * @param boolean $aspect Maintain aspect ratio (default: true)
	 * @param array	$htmlAttributes Array of HTML attributes.
	 * @param boolean $return Wheter this method should return a value or output it. This overrides AUTO_OUTPUT.
	 * @return mixed Either string or echos the value, depends on AUTO_OUTPUT and $return.
	 * @access public
	 */
	public function resize($path, $width, $height, $aspect = true, $cut = false, $stretch = false, $returnPath = false, &$cached = false) {
		
		$types = array(1 => "gif", "jpeg", "png", "swf", "psd", "wbmp");
		$fullpath = $this->fullPath();
		$url = $path;
		
		# veirifica se o arquivo existe
		if ( !file_exists($path) )
			return;
		
		# verifica se o arquivo e uma imagem
		if ( !($size = getimagesize($url)) )
			return;
		
		list($origiwidth, $origiheight) = $size;
		list($iwidth, $iheight) = $size;
		
		# cria o diretorio imagecache caso não exista
		if ( !file_exists($fullpath . $this->cacheDir) )
			mkdir($fullpath . $this->cacheDir);
			
		# obtem a largura e altura exata da thumb que será armazenada
		list($iwidth, $iheight, $width, $height) = $this->_resize($iwidth, $iheight, $width, $height, $aspect, $cut, $stretch);
		
		# seta se será gerada uma nova imagem, [bool type]
		$cached = $this->cached($path, $width, $height, $aspect, $cut);
		
		$relfile = $this->relFile($path, $width, $height, $aspect, $cut); // relative file
		$cachefile = $this->cacheFile($path, $width, $height, $aspect, $cut); // location on server
		
		# se for necessário criar uma nova imagem, verifica se é necessario redimensionar a imagem
		if (!$cached)
			$resize = ($size[0] != $width) || ($size[1] != $height);
		else
			$resize = false;
		
		if ($resize) {
			$image = call_user_func('imagecreatefrom' . $types[$size[2]], $url);
			if (function_exists("imagecreatetruecolor") && ($temp = imagecreatetruecolor ($width, $height))) {
				imagecopyresampled ($temp, $image, 0, 0, 0, 0, $width, $height, $iwidth, $iheight);
	  		} else {
				$temp = imagecreate ($width, $height);
				imagecopyresized ($temp, $image, 0, 0, 0, 0, $width, $height, $iwidth, $iheight);
			}
			call_user_func("image" . $types[$size[2]], $temp, $cachefile, 100);
			imagedestroy ($image);
			imagedestroy ($temp);
		} elseif (!$cached)
			copy($url, $cachefile);
		
		if ($returnPath)
			return $cachefile;
		else
			return $relfile;
	}
	
	function cached($path, $width, $height, $aspect = true, $cut = false, $stretch = false) {
		
		# retorna apenas a localização do cache no servidor
		$cachefile = $this->cacheFile($path, $width, $height, $aspect, $cut, $stretch);
		
		# verifica se existe uma imagem na mesmo localização
		if ( file_exists($cachefile) ) {
			
			# pega dimensão da imagem
			$csize = getimagesize($cachefile);
			
			# verifica se largura e altura da imagem em cache é a mesma da que desejamos criar
			$cached = ($csize[0] == $width && $csize[1] == $height);
			
			/* verifica se a data de modificação da cache é menor que a da imagem que estamos criando
			   verifica se houve alguma modificação na imagem original
			   se verdadeiro, a cache é apagada e será gerada novamente a imagem com a mesmo dimensão */
			
			if (@filemtime($cachefile) < @filemtime($path)) {
				@unlink($cachefile);
				$cached = false;
			}
		} else
			$cached = false;
		
		return $cached;
	}

	function clearCache($expires = null) {
		$now = time();
		if (empty($expires)) {
			$expires = $this->expires;
		}

		if (!is_numeric($expires)) {
			$expires = strtotime($expires, $now);
		}
		
		$timediff = $expires - $now;
		
		foreach (glob($this->fullPath() . $this->cacheDir . DS . '*.*') as $filename) {
			$filetime = @filemtime($filename);
			if ($filetime !== false) {
				if ($filetime + $timediff < $now) {
					@unlink($filename);
				}
			}
		}
	}

	function fullPath() {
		return WWW_ROOT . $this->themeWeb . IMAGES_URL;
	}
	
	function relFile($path, $width, $height, $aspect = true, $cut = false) {
		if ($cut) {
			$cut = 'cut_';
		} else {
			$cut = null;
		}
		if ($aspect) {
			$cut .= 'asp_';
		}
		# return  imagecache    /     100x100     hash for directory path           asp_|cut_          filename.ext
		return $this->cacheDir . '/' . $width . 'x' . $height . '_' . md5(dirname($path)) . '_' . $cut . basename($path);
	}
	
	function cacheFile($path, $width, $height, $aspect = true, $cut = false) {
		if ($cut)
			$cut = 'cut_';
		else
			$cut = null;
		
		if ($aspect)
			$cut .= 'asp_';
			
		# return  webroot/img/      imagecache    /     100x100     hash for directory path           asp_|cut_          filename.ext
		return $this->fullPath() . $this->cacheDir . DS . $width . 'x' . $height . '_' . md5(dirname($path)) . '_' . $cut . basename($path);
	}

	function watermark($path, $mark, $width = null, $height = null, $aspect = true, $cut = false, $stretch = false, $returnPath = false) {
		$types = array(1 => "gif", "jpeg", "png", "swf", "psd", "wbmp"); // used to determine image type

		if (empty($width) || empty($height)) {
			list ($iwidth, $iheight) = getimagesize($path);
			$width = empty($width) ? $iwidth : $width;
			$height = empty($height) ? $iheight : $height;
		}
		$cached = false;
		$imagePath = $this->resize($path, $width, $height, $aspect, $cut, $stretch, true, $cached);
		$imageUrl = $this->resize($path, $width, $height, $aspect, $cut, $stretch);
		$extension = pathinfo($imagePath, PATHINFO_EXTENSION);

		$watermarkFile = dirname($imagePath) . DS . basename($imagePath, '.' . $extension) . '_watermark.' . $extension;
		$watermarkUrl = preg_replace('/\.' . $extension . '$/', '_watermark.' . $extension, $imageUrl);

		if ($cached) {
			if ($returnPath) {
				return $watermarkFile;
			}
			return $watermarkUrl;
		}
		
		list ($markWidthOriginal, $markHeightOriginal, $markType) = getimagesize($mark);
		$markType = $types[$markType];
		$mark = call_user_func('imagecreatefrom' . $markType, $mark);

		list ($imageWidth, $imageHeight, $imageType) = getimagesize($imagePath);
		$imageType = $types[$imageType];
		$image = call_user_func('imagecreatefrom' . $imageType, $imagePath);

		list($markWidth, $markHeight) = $this->_resize($markWidthOriginal, $markHeightOriginal, $imageWidth, $imageHeight, $aspect, $cut, $stretch);

		$x = ceil(($imageWidth - $markWidth) / 2);
		$y = ceil(($imageHeight- $markHeight) / 2);
		while($x > 0) {
			$x -= $markWidth;
		}
		
		while($x < $imageWidth) {
			imagecopyresampled($image, $mark, $x, $y, 0, 0, $markWidth, $markHeight, $markWidthOriginal, $markHeightOriginal);
			$x += $markWidth;
		}
		call_user_func("image" . $imageType, $image, $watermarkFile, 100);
		unlink($imagePath);
		
		if ($returnPath) {
			return $watermarkFile;
		}
		return $watermarkUrl;
	}
	
	function _resize($width, $height, $toWidth, $toHeight, $aspect = true, $cut = false, $stretch = false) {
		if ($cut) {
			if ($aspect) {
				if (($width / $toWidth) > ($height / $toHeight)) {
					$delta = ($height / $toHeight);
				} else {
					$delta = ($width / $toWidth);
				}
				$width = $toWidth * $delta;
				$height = $toHeight * $delta;
			} else {
				$width = $toWidth;
				$height = $toHeight;
			}
		} else {
			if ($aspect) {
				if (($width / $toWidth) > ($height / $toHeight)) {
					$delta = ($width / $toWidth);
				} else {
					$delta = ($height / $toHeight);
				}
				$toWidth = $width / $delta;
				$toHeight = $height / $delta;
			} else {
				# Não faz nada
			}
		}
		if (!$stretch) {
			$toWidth = min($width, $toWidth);
			$toHeight = min($height, $toHeight);
		}
		
		return array(ceil($width), ceil($height), ceil($toWidth), ceil($toHeight));
	}
}
?>