<?php


/*function compressAndResizeImage($imageUrl) {
	// Validate the URL
  if (!filter_var($imageUrl, FILTER_VALIDATE_URL)) {
    return '';
  }
  // Load the image from the given URL
  $imageData = file_get_contents($imageUrl);

  // Determine the image type using getimagesize
  $imageInfo = getimagesizefromstring($imageData);
  $imageType = $imageInfo[2];

  // Create the image using the appropriate function
  switch ($imageType) {
    case IMAGETYPE_JPEG:
      $image = imagecreatefromjpegfromstring($imageData);
      break;
    case IMAGETYPE_PNG:
      $image = imagecreatefromstring($imageData);
      break;
    case IMAGETYPE_GIF:
      $image = imagecreatefromgiffromstring($imageData);
      break;
    case IMAGETYPE_WEBP:
      // Convert WebP images using imagecreatefromwebp function
      if (function_exists('imagecreatefromwebp')) {
        $image = imagecreatefromwebp($imageUrl);
      } else {
        // WebP support not available, raise an error here
        throw new Exception('WebP image format is not supported.');
      }
      break;
    default:
      throw new Exception('Unsupported image format: ' . $imageType);
  }

  $width = imagesx($image);
  $height = imagesy($image);
  $newWidth = 60;
  $newHeight = 60;

  // Create a new thumbnail image
  $thumb = imagecreatetruecolor($newWidth, $newHeight);

  // Resize the image and copy it to the new thumbnail image
  imagecopyresampled($thumb, $image, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

  // Save the compressed image in JPEG or PNG or GIF or WebP format depending on the original format
  switch ($imageType) {
    case IMAGETYPE_JPEG:
      ob_start();
      imagejpeg($thumb, null, 80);
      $compressedImage = ob_get_contents();
      ob_end_clean();
      break;
    case IMAGETYPE_PNG:
      ob_start();
      imagepng($thumb, null, 9);
      $compressedImage = ob_get_contents();
      ob_end_clean();
      break;
    case IMAGETYPE_GIF:
      ob_start();
      imagegif($thumb);
      $compressedImage = ob_get_contents();
      ob_end_clean();
      break;
    case IMAGETYPE_WEBP:
      ob_start();
      imagewebp($thumb, null, 80);
      $compressedImage = ob_get_contents();
      ob_end_clean();
      break;
    default:
      throw new Exception('Unsupported image format: ' . $imageType);
  }
  // Return the compressed image as a link
  $last_compressedImage =  'data:' . $imageInfo['mime'] . ';base64,' . base64_encode($compressedImage);
  return $last_compressedImage  ;

}

function get_compresed_image($id){
    $last_compressedImage = get_post_meta($id,'compressedImage',1);
    if($last_compressedImage == ''){
        $imageUrl = get_the_post_thumbnail_url($id);
        $last_compressedImage = compressAndResizeImage($imageUrl);
        update_post_meta($id,'compressedImage',$last_compressedImage);
    }
 return $last_compressedImage ;
}
*/