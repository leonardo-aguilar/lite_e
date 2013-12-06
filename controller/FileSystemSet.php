<?php

require_once ($GLOBALS["libdir"] . "/Utils.php");
require_once ($GLOBALS["controller"] . "/FileSystemEntry.php");

class FileSystemSet {

	protected $fileSystemSetID;

	protected $entrySetBaseDirectory;

	protected $entrySetBaseEntry;
	protected $fileSystemChildSets;

	protected $hasIndex;
	protected $hasBrowsables;

	protected $indexEntry;
	protected $browsableEntries;
	protected $fileSystemEntries;

	/* Propiedades */
	// Documento inicial del FileSystemSet
	function GetIndexEntry () {
		return $this->hasIndex ? $this->indexEntry : NULL;
	}

	// Documentos "navegables" del FileSystemSet
	function GetBrowsableEntries () {
		return $this->browsableEntries;
	}

	// Documentos "navegables" del FileSystemSet
	function GetBrowsableEntry ($entryId) {
		return $this->hasBrowsables ? $this->browsableEntries[$entryId] : NULL;
	}

	function GetSetId () { return $this->fileSystemSetID; }

	// Tiene documentos de inicio?
	function HasIndex () {
		return $this->hasIndex;
	}

	// Tiene documentos navegables?
	function HasBrowsables () {
		return $this->hasBrowsables;
	}

	/* Constructor */
	function FileSystemSet ($baseDirectory) {

		// printf ("Nuevo FSS en: %s<br/>", $baseDirectory);

		$this->fileSystemChildSets = Array();
		$this->browsableEntries = Array();
		$this->fileSystemEntries = Array();

		$this->indexEntry = NULL;

		$this->entrySetBaseDirectory = $baseDirectory;
		$this->SetFileSystemSetId();
		$this->entrySetBaseEntry = new FileSystemEntry($this->entrySetBaseDirectory,
			$this->entrySetBaseDirectory);
		$this->SearchEntries($this->entrySetBaseDirectory);


	}

	private function SetFileSystemSetId () {
		$this->fileSystemSetID = md5 ($this->entrySetBaseDirectory);
	}

	/* Métodos */
	private function SearchEntries ($baseDirectory) {
		$dirContentArray = array_diff(scandir($baseDirectory, 0),
			array('.', '..', '.DS_Store'));

		$currentFileSystemEntries = Array();

		foreach ($dirContentArray as $entry) {

			$fileSystemEntry = new FileSystemEntry ($baseDirectory . "/" . $entry, $baseDirectory);
			$this->fileSystemEntries[$fileSystemEntry->GetEntryId()] = $fileSystemEntry;

			$currentFileSystemEntries[$fileSystemEntry->GetEntryId()] = $fileSystemEntry;

			if($baseDirectory == $this->entrySetBaseDirectory) {
				// printf ("%s<br/>", $fileSystemEntry->GetEntryType());

				if ($fileSystemEntry->GetEntryType() === FileSystemEntry::$FST_INDEX
					&& !$this->hasIndex)
					$this->hasIndex = true;

				if ($fileSystemEntry->GetEntryType() === FileSystemEntry::$FST_BROWSABLE
					&& !$this->hasBrowsables)
					$this->hasBrowsables = true;
			} else {
				if ($fileSystemEntry->GetEntryType() === FileSystemEntry::$FST_BROWSABLE
					&& !$this->hasBrowsables)
					$this->hasBrowsables = true;
			}

		}

		foreach ($currentFileSystemEntries as $fileSystemEntry) {
			$this->AssignEntry ($fileSystemEntry);
		}
	}

	private function AssignEntry ($fileSystemEntry) {
		switch ($fileSystemEntry->GetEntryType()) {

			case (FileSystemEntry::$FST_DIRECTORY):

				if($this->hasIndex) {
					$this->SearchEntries($fileSystemEntry->GetEntryPath());
				} else {
					$fileSystemSet = new FileSystemSet ($fileSystemEntry->GetEntryPath());
					$this->fileSystemChildSets[$fileSystemSet->GetSetId()] = $fileSystemSet;
					// printf ("FileSystemChildSets: %s<br/>", count ($this->fileSystemChildSets));
				}
				// unset($this->fileSystemEntries[$fileSystemEntry->GetEntryId()]);
				break;

			case (FileSystemEntry::$FST_BROWSABLE):
				$this->browsableEntries[$fileSystemEntry->GetEntryId()] = $fileSystemEntry;
				// unset($this->fileSystemEntries[$fileSystemEntry->GetEntryId()]);
				break;

			case (FileSystemEntry::$FST_INDEX):
				if ($this->indexEntry == NULL &&
					$fileSystemEntry->GetEntryParentPath() == $this->entrySetBaseDirectory)
					$this->indexEntry = $fileSystemEntry;
				else
					$this->browsableEntries[$fileSystemEntry->GetEntryId()] = $fileSystemEntry;
				// unset($this->fileSystemEntries[$fileSystemEntry->GetEntryId()]);
				break;

			case (FileSystemEntry::$FST_MISC):
				break;

		}
	}

	protected function GetIndexEntryTitle () {
		if($this->hasIndex) {

		}
		else { return NULL; }
	}

	function PrintLOInfo () {
		if ($this->hasIndex) {
			$this->PrintInfo();
		} else {
			foreach ($this->fileSystemChildSets as $fileSystemSet)
				$fileSystemSet->PrintLOInfo ();
		}
	}

	function PrintInfo () {

		$parentPathName = str_replace($GLOBALS["repository"] . "/", "",
			$this->indexEntry->GetEntryParentPath());

		$entryTitle = Utils::GetHTMLTitle ($this->indexEntry->GetEntryPath());

		printf("<div class='LearningObject'>
			<div class='LearningObjectTitle' onClick='ToggleContentView(\"%s\")'>
				<div class='LearningObjectDisplayContents' >
					<img src='style/general/icons/accept_item.png'/></div>%s</div>",
				"loContents_" . $this->fileSystemSetID,
				$entryTitle == NULL ? $parentPathName :  $entryTitle);

		printf("<div class='LearningObjectContent' id='%s'>\n",
			"loContents_" . $this->fileSystemSetID);

		printf("\t<div class='BrowsableEntry'>
				<input type='checkbox' id='ScenesCheckboxGroup[]' value='%s' disabled />
				\t\t<a href='javascript:SetContentFrame(\"%s\")'>%s</a></div>\n\r",
				$this->entrySetBaseDirectory . "|" . $this->indexEntry->GetEntryId (),
				$this->indexEntry->GetEntryUrl(),
				"Ver recurso");

		foreach ($this->browsableEntries as $browsableEntry) {
			$browsableEntryTitle =  Utils::GetHTMLTitle ($browsableEntry->GetEntryPath());

			printf("\t<div class='BrowsableEntry'>
				<input type='checkbox' id='ScenesCheckboxGroup[]'
					name='ScenesCheckboxGroup[]' value='%s' />
				\t\t<a href='javascript:SetContentFrame(\"%s\")'>%s</a></div>\n\r",
				$this->entrySetBaseDirectory . "|" . $browsableEntry->GetEntryId (),
				$browsableEntry->GetEntryUrl (),
				$browsableEntryTitle == NULL ? "Documento navegable" : $browsableEntryTitle);
		}
		printf("</div></div>\r\n");
	}
}
