<?php

class FileSystemEntry {

	public static 	$FST_DIRECTORY 	= 0x01;
	public static 	$FST_BROWSABLE 	= 0x02;
	public static 	$FST_INDEX 		= 0x03;
	public static 	$FST_MISC		= 0x04;

	protected $fileSystemEntryPath;
	protected $fileSystemEntryType;
	protected $fileSystemEntryParentPath;

	protected $fileSystemEntryID;

	function GetEntryType () { return $this->fileSystemEntryType; }

	function GetEntryId () { return $this->fileSystemEntryID; }

	function GetEntryPath () { return $this->fileSystemEntryPath; }

	function GetEntryUrl () {
		return $url = Utils::GetFileUrl ($GLOBALS["path_rootdir"], $GLOBALS["wwwroot"],
			$this->fileSystemEntryPath);
	}

	function GetEntryParentPath () { return $this->fileSystemEntryParentPath; }

	function FileSystemEntry ($entryPath, $entryParentPath) {

		$this->fileSystemEntryPath 		= $entryPath;
		$this->fileSystemEntryParentPath = $entryParentPath;

		$this->SetFileSystemEntryId ();
		$this->SetEntryType ();

	}

	function PrintInfo () {

		$fileSystemEntryUrl = Utils::GetFileUrl($CFG->path_rootdir, $CFG->wwwroot,
			$this->fileSystemEntryPath);

		printf("Entry '%s' with path '%s'.<br/>", $this->fileSystemEntryID,
			$fileSystemEntryUrl);

	}

	private function SetEntryType () {
		$entryType;

		if (is_dir($this->fileSystemEntryPath)) {
			$entryType = self::$FST_DIRECTORY;
		} else
			if (stripos($this->fileSystemEntryPath, ".htm") !== false ) {
			   // && Utils::IsDescartes($this->fileSystemEntryPath)) {
			$entryType = self::$FST_BROWSABLE;

			if (stripos($this->fileSystemEntryPath, "index.ht") != false) {
				$entryType = self::$FST_INDEX;
			}
		} else {
			$entryType = self::$FST_MISC;
		}

		$this->fileSystemEntryType = $entryType;

	}

	private function SetFileSystemEntryId () {

		$this->fileSystemEntryID = md5 ($this->fileSystemEntryPath . "+" .
			$this->fileSystemEntryType);

	}

}
