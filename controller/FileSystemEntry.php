<?php

class FileSystemEntry {
   
	public static 	$FST_MISC		   	= 0x00;
	public static 	$FST_DIRECTORY 		= 0x01;
	public static 	$FST_BROWSABLE 		= 0x02;
	public static 	$FST_INDEX 		   	= 0x03;
	public static 	$FST_PNGS		   	= 0x04;
	

	protected $fileSystemEntryPath;
	protected $fileSystemEntryType;
	protected $fileSystemEntryParentPath;

	protected $fileSystemEntryID;
	
	protected $isDescartes;
	
	function IsDescartes () { return $this->isDescartes; }

	function EntryType () { return $this->fileSystemEntryType; }

	function EntryId () { return $this->fileSystemEntryID; }

	function EntryPath () { return $this->fileSystemEntryPath; }

	function EntryUrl () {
		return $url = Utils::GetFileUrl ($GLOBALS["path_rootdir"], $GLOBALS["wwwroot"],
			$this->fileSystemEntryPath);
	}

	function EntryRelativeUrl ($fileSystemSetBaseDir) {
		$url = Utils::GetFileUrl ($GLOBALS["path_rootdir"], "", $this->fileSystemEntryPath);
		return substr($url, strpos($url, $fileSystemSetBaseDir));
	}

	function EntryParentPath () { return $this->fileSystemEntryParentPath; }

	function FileSystemEntry ($entryPath, $entryParentPath) {

		$this->fileSystemEntryPath 		= $entryPath;
		$this->fileSystemEntryParentPath = $entryParentPath;

		$this->isDescartes = false;
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
			if (stripos($this->fileSystemEntryPath, ".htm") !== false) {
			   $entryType = self::$FST_BROWSABLE;

			if (stripos($this->fileSystemEntryPath, "index.ht") != false) {
				$entryType = self::$FST_INDEX;
			}
		} else {
			$entryType = self::$FST_MISC;
		}

		$this->fileSystemEntryType = $entryType;
		$this->CheckScene ();

	}

   private function SetFileSystemEntryId () {
   
      $this->fileSystemEntryID = md5 ($this->fileSystemEntryPath . "+" .
      $this->fileSystemEntryType);
   
   }

   private function CheckScene () {
      if ($this->EntryType() == self::$FST_BROWSABLE ||
            $this->EntryType() == self::$FST_INDEX)
         $this->isDescartes = Utils::IsDescartes($this->fileSystemEntryPath) === 1 ? true : false;
   }
	
   function Duplicate ($sourceDir, $targetDir) {
      $targetPath = str_replace($sourceDir, $targetDir, $this->fileSystemEntryPath);
      if($this->fileSystemEntryType == self::$FST_DIRECTORY)
         @mkdir($targetPath, 0777);
      else {
         if (stripos($this->fileSystemEntryPath, ".php") == false)
            copy($this->fileSystemEntryPath, $targetPath);
      }
   }
}
