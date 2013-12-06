<?php

class Utils {

	public static function GetFileUrl ($baseDir, $httpDir, $fullPath) {
	    // printf("%s<br/>%s<br/>%s", $baseDir, $httpDir, $fullPath);
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

	public static function FileExists () {}

	public static function CreateDir () {}

}
