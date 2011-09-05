<?php
    class imageResize {
        public  $img = false;
        private $file = NULL;
        private $ext = NULL;
        private $imageSize;
        private $compression = NULL;
        
        function imageResize($file=NULL)
        {
            if($file != NULL)
                return $this->loadImg($file);
            else
                return true;
        }
        
        public function loadImg($file)
        {
            if(!file_exists($file)) {
                //echo("File not found in loadImg().");
                return false;
            }
                
            $this->imageSize = getimagesize($file);

            if(!defined("IMAGETYPE_JPG"))
                define("IMAGETYPE_JPG", IMAGETYPE_JPEG);
            
            switch($this->imageSize[2]) {
                case IMAGETYPE_JPG:
                    $this->img = imagecreatefromjpeg($file);
                    $this->file = $file;
                    return true;
                case IMAGETYPE_PNG:
                    $this->img = imagecreatefrompng($file);
					imageAlphaBlending($this->img, false);
					imageSaveAlpha($this->img, true);
                    $this->file = $file;
                    return true;
                case IMAGETYPE_GIF:
                    $this->img = imagecreatefromgif($file);
                    $this->file = $file;
                    break;
			case IMAGETYPE_BMP:
				$this->img = ImageCreateFromBMP($file);
				$this->file = $file;
                default:
                    //coreIMG::trace("Unknown file format in loadImg().");
                    return false;
            }
        }
        
        /* Sets compression level. Note that for different image types different compression ranges are given by php.
        this function will automatically calculate the adequate compression value according to your input. The compression
        range accepted by this function is 0 (none) - 100 (maximum); */
        
        public function setCompression($val)
        {
            $val = intval($val);
            if($val < 0 || $val > 100) {
                //coreIMG::trace("Invalid compression value in setCompression().");
                return false;
            }
            else {
                $this->compression = $val;
                return true;
            }
            
        }
		
        public function rotate($angle, $background=NULL)
        {
            if(!$this->img) {
                //coreIMG::trace("No image has been loaded in rotate().");
                return false;
            }
            
            $this->img = imagerotate($this->img, $angle, $background, false);
            $this->imageSize[0] = imagesx($this->img);
            $this->imageSize[1] = imagesy($this->img);
            return true;
        }
        
        public function resize($newWidth, $newHeight=0)
        {
            if(!$this->img) {
                //coreIMG::trace("No image has been loaded in resize().");
                return false;
            }
                
            else if(!$newWidth && !$newHeight) {
                //coreIMG::trace("Either width or height has to be specified in resize().");
                return false;
            }
            
            else {
                $ratio = $this->imageSize[0]/$this->imageSize[1];
                if($newWidth == 0)
                    $newWidth = $ratio*$newHeight;
                else if($newHeight == 0)
                    $newHeight = (1/$ratio)*$newWidth;
					$new_img = imagecreatetruecolor($newWidth, $newHeight);
					
					// --- um die transparents des Bildes zu behalten
					imagealphablending($new_img,false);
					imagesavealpha($new_img,true);
					imagepalettecopy($new_img,$this->img);
					$transparent = imagecolorallocate($new_img,0,0,0);
					imagefill($new_img,0,0,$transparent);
					imagecolortransparent($new_img,$transparent);
					// ---
					
					imagecopyresampled($new_img, $this->img, 0, 0, 0, 0, $newWidth, $newHeight, $this->imageSize[0], $this->imageSize[1]);
				
				
				$verhaeltnis = $this->imageSize[0]/$newWidth;
		        $staerke = $verhaeltnis*24;
		        
		        if ($staerke > 100) {
		        	$staerke = 100;
		        }
		        
		        $new_img = $this->unsharp($new_img, $staerke, 1, 0);
				
				
				
                $this->img = $new_img;
                $this->imageSize[0] = $newWidth;
                $this->imageSize[1] = $newHeight;
                return true;
            }
        }
        
        private function checkType($type)
        {
            switch($type) {
                case IMAGETYPE_PNG:
                case IMAGETYPE_JPG:
                case IMAGETYPE_GIF:
                    return true;
                default:
                    return false;
            }
        }
        
        public function raw($type=NULL)
        {
            if(!$this->img) {
                //coreIMG::trace("No image loaded in raw().");
                return false;
            }
            if(!$this->checkType($type)) {
                //coreIMG::trace("Type given is not supported in raw().");
                return false;
            }
                
            $this->img($type);
        }
        
        private function getTypeFromExtension($ext)
        {
            switch(strtolower($ext)) {
                case "png":
                    return IMAGETYPE_PNG;
                case "jpg":
                case "jpeg":
                    return IMAGETYPE_JPG;
                case "gif":
                    return IMAGETYPE_GIF;
            }
            
            return false;
        }
        
        public function extract($x1, $y1, $x2, $y2)
        {
                if(($x1 >= $x2) || ($y1 >= $y2)) {
                    //coreIMG::trace("Invalid coordinates in extract().");
                    return false;
                }
                
                $newWidth = $x2 - $x1;
                $newHeight = $y2 - $y1;
                
                $newImg = imagecreatetruecolor($newWidth, $newHeight);
				
				// --- um die transparenz des Bildes zu behalten
					imagealphablending($newImg,false);
					imagesavealpha($newImg,true);
					imagepalettecopy($newImg,$this->img);
					$transparent = imagecolorallocate($newImg,0,0,0);
					imagefill($newImg,0,0,$transparent);
					imagecolortransparent($newImg,$transparent);
				// ---
				
                imagecopyresampled($newImg, $this->img, 0, 0, $x1, $y1, $newWidth, $newHeight, $newWidth, $newHeight);
                
		        
		        $verhaeltnis = $this->imageSize[0]/$newWidth;
		        $staerke = $verhaeltnis*24;
		        
		        if ($staerke > 100) {
		        	$staerke = 100;
		        }
		        
		        $newImg = $this->unsharp($newImg, $staerke, 1, 0);
		       	
                
                $this->img = $newImg;
                $this->imageSize[0] = $newWidth;
                $this->imageSize[1] = $newHeight;
                return true;
        }
        
        public function clip($targetWidth, $targetHeight)
        {
        	$targetRatio = round($targetWidth/$targetHeight, 2);
        	$ratio = $this->getRatio();
			
			
	        if($targetRatio == $ratio) 
				$this->resize($targetWidth, $targetHeight);
		
			else {
				$newHeight = (1/$ratio)*$targetWidth;
				if($newHeight < $targetHeight) {
					$this->resize(0, $targetHeight);
					$offsetX = round(($this->imageSize[0]-$targetWidth)/2);
					$this->extract($offsetX, 0, $offsetX+$targetWidth, $targetHeight);
				}
				else {
					$this->resize($targetWidth);
					$offsetY = round(($this->imageSize[1] - $targetHeight)/2);
					$this->extract(0, $offsetY, $targetWidth, $offsetY+$targetHeight);
				}
			}
        }
        
        private function getRatio()
        {
        	return round($this->imageSize[0]/$this->imageSize[1], 2);
        }
        
        public function save($filename)
        {
            if(!$this->img) {
                //coreIMG::trace("No image loaded in save().");
                return false;
            }
            
            if(!$type = $this->getTypeFromExtension(substr(basename($filename), strrpos(basename($filename), ".")+1))) {
                //coreIMG::trace("File extension not supported in save().");
                return false;
            }
            
            if(!$this->checkType($type)) {
                //coreIMG::trace("Type given is not supported in save().");
                return false;
            }
            
            $this->img($type, $filename);
            
        }
		
		public function getSize()
		{
			return array($this->imageSize[0], $this->imageSize[1], ($this->imageSize[0]/$this->imageSize[1]));
		}

        private function img($type=NULL, $filename=NULL)
        {
            if($type == NULL)
                $type = $this->type;
                
            if($filename == NULL)
                header('Content-type: '.image_type_to_mime_type($this->imageSize[2]));
            
            if(!$this->compression)
                $this->compression = 100;
            
            switch($type) {
                case IMAGETYPE_JPG:
					imageinterlace($this->img);
                    imagejpeg($this->img, $filename, $this->compression);
                    break;
                case IMAGETYPE_PNG:
                    $compression = floor(($this->compression-1)/10);
					imageinterlace($this->img);
                    imagepng($this->img, $filename, $compression);
                    break;
                case IMAGETYPE_GIF:
					imageinterlace($this->img);
					// --- für Alpha in PNG:
					$compression = floor(($this->compression-1)/10);
                    imagepng($this->img, $filename, $compression);					
					
                    //imagegif($this->img, $filename);
                    break;
            }        
        }
    	
        function unsharp($img, $amount, $radius = 0.5, $threshold = 0)    {

		//////////////////////////////////////////////////////////////////
		////
		////                  p h p U n s h a r p M a s k
		////
		////    Unsharp mask algorithm by Torstein Hønsi 2003.
		////             thoensi_at_netcom_dot_no.
		////               Please leave this notice.
		////
		///////////////////////////////////////////////////////////////////
		
		
		    // $img is an image that is already created within php using
		    // imgcreatetruecolor. No url! $img must be a truecolor image.
		
		    // Attempt to calibrate the parameters to Photoshop:
		    if ($amount > 500)    $amount = 500;
		    $amount = $amount * 0.016;
		    if ($radius > 50)    $radius = 50;
		    $radius = $radius * 2;
		    if ($threshold > 255)    $threshold = 255;
		     
		    $radius = abs(round($radius));     // Only integers make sense.
		    if ($radius == 0) {
		        return $img; imagedestroy($img); break;        }
		    $w = imagesx($img); $h = imagesy($img);
		    $imgCanvas = imagecreatetruecolor($w, $h);
		    $imgCanvas2 = imagecreatetruecolor($w, $h);
		    $imgBlur = imagecreatetruecolor($w, $h);
		    $imgBlur2 = imagecreatetruecolor($w, $h);
		    imagecopy ($imgCanvas, $img, 0, 0, 0, 0, $w, $h);
		    imagecopy ($imgCanvas2, $img, 0, 0, 0, 0, $w, $h);
		     
		
		    // Gaussian blur matrix:
		    //                         
		    //    1    2    1         
		    //    2    4    2         
		    //    1    2    1         
		    //                         
		    //////////////////////////////////////////////////
		
		    // Move copies of the image around one pixel at the time and merge them with weight
		    // according to the matrix. The same matrix is simply repeated for higher radii.
		    for ($i = 0; $i < $radius; $i++)    {
		        imagecopy ($imgBlur, $imgCanvas, 0, 0, 1, 1, $w - 1, $h - 1); //up left
		        imagecopymerge ($imgBlur, $imgCanvas, 1, 1, 0, 0, $w, $h, 50); //down right
		        imagecopymerge ($imgBlur, $imgCanvas, 0, 1, 1, 0, $w - 1, $h, 33.33333); //down left
		        imagecopymerge ($imgBlur, $imgCanvas, 1, 0, 0, 1, $w, $h - 1, 25); //up right
		        imagecopymerge ($imgBlur, $imgCanvas, 0, 0, 1, 0, $w - 1, $h, 33.33333); //left
		        imagecopymerge ($imgBlur, $imgCanvas, 1, 0, 0, 0, $w, $h, 25); //right
		        imagecopymerge ($imgBlur, $imgCanvas, 0, 0, 0, 1, $w, $h - 1, 20 ); //up
		        imagecopymerge ($imgBlur, $imgCanvas, 0, 1, 0, 0, $w, $h, 16.666667); //down
		        imagecopymerge ($imgBlur, $imgCanvas, 0, 0, 0, 0, $w, $h, 50); //center
		        imagecopy ($imgCanvas, $imgBlur, 0, 0, 0, 0, $w, $h);
		
		        // During the loop above the blurred copy darkens, possibly due to a roundoff
		        // error. Therefore the sharp picture has to go through the same loop to
		        // produce a similar image for comparison. This is not a good thing, as processing
		        // time increases heavily.
		        imagecopy ($imgBlur2, $imgCanvas2, 0, 0, 0, 0, $w, $h);
		        imagecopymerge ($imgBlur2, $imgCanvas2, 0, 0, 0, 0, $w, $h, 50);
		        imagecopymerge ($imgBlur2, $imgCanvas2, 0, 0, 0, 0, $w, $h, 33.33333);
		        imagecopymerge ($imgBlur2, $imgCanvas2, 0, 0, 0, 0, $w, $h, 25);
		        imagecopymerge ($imgBlur2, $imgCanvas2, 0, 0, 0, 0, $w, $h, 33.33333);
		        imagecopymerge ($imgBlur2, $imgCanvas2, 0, 0, 0, 0, $w, $h, 25);
		        imagecopymerge ($imgBlur2, $imgCanvas2, 0, 0, 0, 0, $w, $h, 20 );
		        imagecopymerge ($imgBlur2, $imgCanvas2, 0, 0, 0, 0, $w, $h, 16.666667);
		        imagecopymerge ($imgBlur2, $imgCanvas2, 0, 0, 0, 0, $w, $h, 50);
		        imagecopy ($imgCanvas2, $imgBlur2, 0, 0, 0, 0, $w, $h);
		         
		        }
		
		    // Calculate the difference between the blurred pixels and the original
		    // and set the pixels
		    for ($x = 0; $x < $w; $x++)    { // each row
		        for ($y = 0; $y < $h; $y++)    { // each pixel
		                 
		            $rgbOrig = ImageColorAt($imgCanvas2, $x, $y);
		            $rOrig = (($rgbOrig >> 16) & 0xFF);
		            $gOrig = (($rgbOrig >> 8) & 0xFF);
		            $bOrig = ($rgbOrig & 0xFF);
		             
		            $rgbBlur = ImageColorAt($imgCanvas, $x, $y);
		             
		            $rBlur = (($rgbBlur >> 16) & 0xFF);
		            $gBlur = (($rgbBlur >> 8) & 0xFF);
		            $bBlur = ($rgbBlur & 0xFF);
		             
		            // When the masked pixels differ less from the original
		            // than the threshold specifies, they are set to their original value.
		            $rNew = (abs($rOrig - $rBlur) >= $threshold)
		                ? max(0, min(255, ($amount * ($rOrig - $rBlur)) + $rOrig))
		                : $rOrig;
		            $gNew = (abs($gOrig - $gBlur) >= $threshold)
		                ? max(0, min(255, ($amount * ($gOrig - $gBlur)) + $gOrig))
		                : $gOrig;
		            $bNew = (abs($bOrig - $bBlur) >= $threshold)
		                ? max(0, min(255, ($amount * ($bOrig - $bBlur)) + $bOrig))
		                : $bOrig;
		             
		             
		                         
		            if (($rOrig != $rNew) || ($gOrig != $gNew) || ($bOrig != $bNew)) {
		                    $pixCol = ImageColorAllocate($img, $rNew, $gNew, $bNew);
		                    ImageSetPixel($img, $x, $y, $pixCol);
		                }
		            }
		        }
		
		    imagedestroy($imgCanvas);
		    imagedestroy($imgCanvas2);
		    imagedestroy($imgBlur);
		    imagedestroy($imgBlur2);
		     
		    return $img;
		
		    }
    }
    
	    function ImageCreateFromBMP($filename)
	{

	   if (! $f1 = fopen($filename,"rb")) return FALSE;


	   $FILE = unpack("vfile_type/Vfile_size/Vreserved/Vbitmap_offset", fread($f1,14));
	   if ($FILE['file_type'] != 19778) return FALSE;


	   $BMP = unpack('Vheader_size/Vwidth/Vheight/vplanes/vbits_per_pixel'.
				  '/Vcompression/Vsize_bitmap/Vhoriz_resolution'.
				  '/Vvert_resolution/Vcolors_used/Vcolors_important', fread($f1,40));
	   $BMP['colors'] = pow(2,$BMP['bits_per_pixel']);
	   if ($BMP['size_bitmap'] == 0) $BMP['size_bitmap'] = $FILE['file_size'] - $FILE['bitmap_offset'];
	   $BMP['bytes_per_pixel'] = $BMP['bits_per_pixel']/8;
	   $BMP['bytes_per_pixel2'] = ceil($BMP['bytes_per_pixel']);
	   $BMP['decal'] = ($BMP['width']*$BMP['bytes_per_pixel']/4);
	   $BMP['decal'] -= floor($BMP['width']*$BMP['bytes_per_pixel']/4);
	   $BMP['decal'] = 4-(4*$BMP['decal']);
	   if ($BMP['decal'] == 4) $BMP['decal'] = 0;


	   $PALETTE = array();
	   if ($BMP['colors'] < 16777216)
	   {
	    $PALETTE = unpack('V'.$BMP['colors'], fread($f1,$BMP['colors']*4));
	   }


	   $IMG = fread($f1,$BMP['size_bitmap']);
	   $VIDE = chr(0);

	   $res = imagecreatetruecolor($BMP['width'],$BMP['height']);
	   $P = 0;
	   $Y = $BMP['height']-1;
	   while ($Y >= 0)
	   {
	    $X=0;
	    while ($X < $BMP['width'])
	    {
		if ($BMP['bits_per_pixel'] == 24)
		   $COLOR = unpack("V",substr($IMG,$P,3).$VIDE);
		elseif ($BMP['bits_per_pixel'] == 16)
		{ 
		   $COLOR = unpack("n",substr($IMG,$P,2));
		   $COLOR[1] = $PALETTE[$COLOR[1]+1];
		}
		elseif ($BMP['bits_per_pixel'] == 8)
		{ 
		   $COLOR = unpack("n",$VIDE.substr($IMG,$P,1));
		   $COLOR[1] = $PALETTE[$COLOR[1]+1];
		}
		elseif ($BMP['bits_per_pixel'] == 4)
		{
		   $COLOR = unpack("n",$VIDE.substr($IMG,floor($P),1));
		   if (($P*2)%2 == 0) $COLOR[1] = ($COLOR[1] >> 4) ; else $COLOR[1] = ($COLOR[1] & 0x0F);
		   $COLOR[1] = $PALETTE[$COLOR[1]+1];
		}
		elseif ($BMP['bits_per_pixel'] == 1)
		{
		   $COLOR = unpack("n",$VIDE.substr($IMG,floor($P),1));
		   if     (($P*8)%8 == 0) $COLOR[1] =  $COLOR[1]        >>7;
		   elseif (($P*8)%8 == 1) $COLOR[1] = ($COLOR[1] & 0x40)>>6;
		   elseif (($P*8)%8 == 2) $COLOR[1] = ($COLOR[1] & 0x20)>>5;
		   elseif (($P*8)%8 == 3) $COLOR[1] = ($COLOR[1] & 0x10)>>4;
		   elseif (($P*8)%8 == 4) $COLOR[1] = ($COLOR[1] & 0x8)>>3;
		   elseif (($P*8)%8 == 5) $COLOR[1] = ($COLOR[1] & 0x4)>>2;
		   elseif (($P*8)%8 == 6) $COLOR[1] = ($COLOR[1] & 0x2)>>1;
		   elseif (($P*8)%8 == 7) $COLOR[1] = ($COLOR[1] & 0x1);
		   $COLOR[1] = $PALETTE[$COLOR[1]+1];
		}
		else
		   return FALSE;
		imagesetpixel($res,$X,$Y,$COLOR[1]);
		$X++;
		$P += $BMP['bytes_per_pixel'];
	    }
	    $Y--;
	    $P+=$BMP['decal'];
	   }


	   fclose($f1);

	 return $res;
	}
?>