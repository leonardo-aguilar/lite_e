<?php

require_once ($_SERVER['DOCUMENT_ROOT'] . "/lite_e/lib/pclzip.lib.php");

class Utils {

	public static function GetFileUrl ($baseDir, $httpDir, $fullPath) {
		return str_replace($baseDir, $httpDir, $fullPath);
	}

	public static function GetHTMLTitle ($htmlFile) {
		try {
			$html = utf8_encode(file_get_contents ($htmlFile));
			return preg_match('!<title>(.*?)</title>!i', $html, $matches) ?
				$matches[1] : NULL;
		} catch (Exception $e) {
			return NULL;
		}
	}

   public static function IsDescartes ($htmlFile) {
      $html = utf8_encode(file_get_contents ($htmlFile));
      return preg_match("/(ajs|descartes-min\.js)/i", $html, $matches);
   }

   public static function CompressFolder ($folderPath, $fileName) {
     
      if (file_exists($fileName))
         unlink($fileName);
      
      $archive = new PclZip($fileName);
      $archive->add(realpath($folderPath), PCLZIP_OPT_REMOVE_PATH, realpath($folderPath))
                  or DIE ("ERROR: Unable to create file: $fileName");
   }
   
   public static function RemoveDirAndContents ($folderPath) {
      $files = array_diff(scandir($folderPath), array('.','..')); 
      
      foreach ($files as $file) { 
         (is_dir("$folderPath/$file")) ? self::RemoveDirAndContents ("$folderPath/$file")
            : unlink("$folderPath/$file"); 
      }
      
      return rmdir($folderPath); 
   }
   
   public static function FileExists () {}
   
   public static function CreateDir () {}

}
