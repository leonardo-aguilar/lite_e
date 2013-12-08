<?php

class Utils {

	public static function GetFileUrl ($baseDir, $httpDir, $fullPath) {
		return str_replace($baseDir, $httpDir, $fullPath);
	}

	public static function GetHTMLTitle ($htmlFile) {
		try {
			$html = file_get_contents ($htmlFile);
			return preg_match('!<title>(.*?)</title>!i', $html, $matches) ?
				$matches[1] : NULL;
		} catch (Exception $e) {
			return NULL;
		}
	}

	public static function IsDescartes ($htmlFile) {
        $html = file_get_contents ($htmlFile);
        return preg_match('<ajs(.*?)>i', $html, $matches);
	}

    public static function copyr($source, $dest)
    {
        // recursive function to copy
        // all subdirectories and contents:
        if(is_dir($source)) {
            $dir_handle=opendir($source);
            $sourcefolder = basename($source);
            mkdir($dest."/".$sourcefolder);
            while($file=readdir($dir_handle)){
                if($file!="." && $file!=".."){
                    if(is_dir($source."/".$file)){
                        self::copyr($source."/".$file, $dest."/".$sourcefolder);
                    } else {
                        copy($source."/".$file, $dest."/".$file);
                    }
                }
            }
            closedir($dir_handle);
        } else {
            // can also handle simple copy commands
            copy($source, $dest);
        }
    }

    public static function CompressFolder ($folderPath, $fileName) {
		$zip = new ZipArchive();

        if ($zip->open($fileName, ZIPARCHIVE::CREATE)!==TRUE) {
            exit("cannot open <$filename>\n");
        }

        $all= new RecursiveIteratorIterator(new RecursiveDirectoryIterator($folderPath));

        foreach ($all as $f=>$value) {
            $zip->addFile(realpath($f), $f) or die ("ERROR: Unable to add file: $f");
        }

        $zip->close();

    }

	public static function FileExists () {}

	public static function CreateDir () {}

}
