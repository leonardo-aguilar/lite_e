<?php

public static class FileUtils {

	public function GetDirectoryListing ($dirName, $recursive = false) {
		// Create recursive dir iterator which skips dot folders
		$dir = new RecursiveDirectoryIterator('./system/information',
			FilesystemIterator::SKIP_DOTS);

		// Flatten the recursive iterator, folders come before their files
		$it  = new RecursiveIteratorIterator($dir,
			RecursiveIteratorIterator::SELF_FIRST);

		// Maximum depth is 1 level deeper than the base folder
		$it->setMaxDepth(1);

		// Basic loop displaying different messages based on file or folder
		foreach ($it as $fileinfo) {
			if ($fileinfo->isDir()) {
				printf("Folder - %s\n", $fileinfo->getFilename());
			} elseif ($fileinfo->isFile()) {
				printf("File From %s - %s\n", $it->getSubPath(), $fileinfo->getFilename());
			}
		}
	}

	public function FileExists () {}

	public function CreateDir () {}

}
