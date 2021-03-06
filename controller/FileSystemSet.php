﻿<?php

require_once ($GLOBALS["libdir"] . "/Utils.php");
require_once ($GLOBALS["controller"] . "/FileSystemEntry.php");
require_once ($GLOBALS["controller"] . "/MetaData.php");

class FileSystemSet {

	protected $fileSystemSetID;

	protected $entrySetBaseDirectory;

	protected $entrySetBaseEntry;
	protected $fileSystemChildSets;

	protected $hasIndex;
	protected $hasBrowsables;

	protected $indexEntry;
   protected $pngEntries;
	protected $browsableEntries;
	protected $fileSystemEntries;

   protected $metaData;

	/* Propiedades */
	// Documento inicial del FileSystemSet
	function IndexEntry () {
		return $this->hasIndex ? $this->indexEntry : NULL;
	}

   function IndexEntryTitle () {
   	$entryTitle = NULL;
   	$parentPathName = $this->entrySetBaseDirectory;

   	if ($this->hasIndex) {
      	$parentPathName = str_replace($GLOBALS["repository"] . "/", "",
                           $this->indexEntry->EntryParentPath());
      	$entryTitle = Utils::GetHTMLTitle ($this->indexEntry->EntryPath());
      }

      return $entryTitle == NULL ? $parentPathName : $entryTitle;
   }

	// PNGs dentro del FileSystemSet
	function PngEntries () {
		return $this->pngEntries;
	}

   function Title () {
      $loTitle = $this->metaData->Title() !== NULL ?
                  $this->metaData->Title() : $this->IndexEntryTitle();
      return $loTitle;
   }

	function Metadata () { return $this->metaData; }

	function Description () { return $this->metaData->Description(); }

	function Thumbnails () { return $this->metaData->Thumbnails(); }

	function Area () { return $this->metaData->SchoolArea(); }

	function Level () { return $this->metaData->SchoolLevel(); }

	function Project () { return $this->metaData->Project(); }

	function CreditsUrl ($isRelative = true) {
		$url = "";
		if($isRelative) $url = $this->metaData->Credits();
		else {
			$tmp = str_ireplace($this->BaseDirectoryName(), "", $this->BaseDirectory()) .
						$this->metaData->Credits();
			$url = Utils::GetFileUrl ($GLOBALS["path_rootdir"], $GLOBALS["wwwroot"], $tmp);
		}

		return $url;
	}

	function InfoUrl ($isRelative = true) {
		$url = "";
		if($isRelative) $url = $this->metaData->Info();

		else {
			$tmp = str_ireplace($this->BaseDirectoryName(), "", $this->BaseDirectory()) .
						$this->metaData->Info();
			$url = Utils::GetFileUrl ($GLOBALS["path_rootdir"], $GLOBALS["wwwroot"], $tmp);
		}

		return $url;
	}

	// Documentos "navegables" del FileSystemSet
	function BrowsableEntries () {
		return $this->browsableEntries;
	}

	// Documentos "navegables" del FileSystemSet
	function GetBrowsableEntry ($entryId) {
		return $this->hasBrowsables ? $this->browsableEntries[$entryId] : NULL;
	}

	function GetSetId () { return $this->fileSystemSetID; }

	function BaseDirectory () { return $this->entrySetBaseDirectory; }

	function BaseDirectoryName () {
		$pieces = explode("/", $this->entrySetBaseDirectory);
		return array_pop($pieces);
	}

	// Tiene documento de inicio?
	function HasIndex () {
		return $this->hasIndex;
	}

	// Tiene documentos navegables?
	function HasBrowsables () {
		return $this->hasBrowsables;
	}

	/* Constructor */
	function FileSystemSet ($baseDirectory) {

		$this->fileSystemChildSets = Array();
		$this->browsableEntries = Array();
		$this->fileSystemEntries = Array();
		$this->pngsEntries = Array();

		$this->indexEntry = NULL;
		$this->hasIndex = false;

		$this->entrySetBaseDirectory = $baseDirectory;
		$this->SetFileSystemSetId();
		$this->entrySetBaseEntry = new FileSystemEntry($this->entrySetBaseDirectory,
			$this->entrySetBaseDirectory);
		$this->SearchEntries($this->entrySetBaseDirectory);

      $this->metaData = new MetaData ($baseDirectory . "/manifest.xml");

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
			$this->fileSystemEntries[$fileSystemEntry->EntryId()] = $fileSystemEntry;

			$currentFileSystemEntries[$fileSystemEntry->EntryId()] = $fileSystemEntry;

			if($baseDirectory == $this->entrySetBaseDirectory) {
				if ($fileSystemEntry->EntryType() === FileSystemEntry::$FST_INDEX
					&& !$this->hasIndex)
					$this->hasIndex = true;

				if ($fileSystemEntry->EntryType() === FileSystemEntry::$FST_BROWSABLE
					&& !$this->hasBrowsables)
					$this->hasBrowsables = true;
			} else {
				if ($fileSystemEntry->EntryType() === FileSystemEntry::$FST_BROWSABLE
					&& !$this->hasBrowsables)
					$this->hasBrowsables = true;
			}

		}

		foreach ($currentFileSystemEntries as $fileSystemEntry) {
			$this->AssignEntry ($fileSystemEntry);
		}
	}

	private function AssignEntry ($fileSystemEntry) {
		switch ($fileSystemEntry->EntryType()) {

			case (FileSystemEntry::$FST_DIRECTORY):

				if($this->hasIndex) {
					$this->SearchEntries($fileSystemEntry->EntryPath());
				} else {
					$fileSystemSet = new FileSystemSet ($fileSystemEntry->EntryPath());
					$this->fileSystemChildSets[$fileSystemSet->GetSetId()] = $fileSystemSet;
				}
				break;

			case (FileSystemEntry::$FST_BROWSABLE):
				$this->browsableEntries[$fileSystemEntry->EntryId()] = $fileSystemEntry;
				break;

			case (FileSystemEntry::$FST_INDEX):
				if ($this->indexEntry == NULL &&
					$fileSystemEntry->EntryParentPath() == $this->entrySetBaseDirectory)
					$this->indexEntry = $fileSystemEntry;
				else
					$this->browsableEntries[$fileSystemEntry->EntryId()] = $fileSystemEntry;
				break;

         case (FileSystemEntry::$FST_PNGS):
               $this->pngEntries[$fileSystemEntry->EntryId()] = $fileSystemEntry;
            break;
			case (FileSystemEntry::$FST_MISC):
				break;

		}
	}

   function PrintInfo () {
      if ($this->hasIndex) {
         $this->PrintLOInfo();
      } else {
			printf ("<div class=\"Project\"> " .
					"<input type='checkbox' id='ProjectCheckboxGroup[]' name='ProjectCheckboxGroup[]' " .
					"value='%s' /><span class=\"ProjectTitle\">&nbsp;<span class='ProjectIcon'></span>&nbsp;%s</span><br />",
					$this->entrySetBaseDirectory . "|" . $this->fileSystemSetID,
					$this->Title());
         foreach ($this->fileSystemChildSets as $fileSystemSet)
				$fileSystemSet->PrintInfo ();
			printf ("</div>\n\r");
      }
   }

   function PrintLOInfo () {

      printf("<div class='LearningObject'> " .
               "<div class='LearningObjectTitle'>" .
               "<input type='checkbox' id='UnitsCheckboxGroup[]' name='UnitsCheckboxGroup[]' value='%s' />" .
			   "<span onClick='ToggleContentView(\"%s\")' id='LOT_%s'>%s</span></div>\n",
			   	$this->entrySetBaseDirectory . "|" . $this->fileSystemSetID,
               "loContents_" . $this->fileSystemSetID,
               $this->fileSystemSetID,
               $this->Title());

      printf("<div class='LearningObjectContent' id='%s'>\n",
               "loContents_" . $this->fileSystemSetID);

      $descartesScene = "";
      $descartesClass = "HiddenClass";
      $disabled = "style=\"visibility: hidden;\"";

      if ($this->indexEntry->IsDescartes()) {
         $descartesScene = " - [Escena]";
         $descartesClass = "DescartesClass";
         $disabled = "";
      }

      printf("\t<div class='BrowsableEntry'> " .
               "<input type='checkbox' id='ScenesCheckboxGroup[]' " .
               "name='ScenesCheckboxGroup[]' value='%s' %s /> " .
               "<span class='%s'></span> " .
               "\t\t<a href='javascript:SetContentFrame(\"%s\")'>%s</a></div>\n",
               $this->entrySetBaseDirectory . "|" . $this->indexEntry->EntryId () . "|" . $this->fileSystemSetID,
               $disabled, $descartesClass,
               $this->indexEntry->EntryUrl(),
               "Ver recurso: " . $this->Title());

      foreach ($this->browsableEntries as $browsableEntry) {
         $browsableEntryTitle =  Utils::GetHTMLTitle ($browsableEntry->EntryPath());

         $descartesScene = "";
         $descartesClass = "HiddenClass";
         $disabled = "";

         if ($browsableEntry->IsDescartes()) {
            $descartesClass = "DescartesClass";
         }

         printf("\t<div class='BrowsableEntry'> " .
                  "<input type='checkbox' id='ScenesCheckboxGroup[]' " .
                  "name='ScenesCheckboxGroup[]' value='%s' %s /><span class='%s'></span> " .
                  "\t\t<a href='javascript:SetContentFrame(\"%s\")'>%s</a></div>\n",
                  $this->entrySetBaseDirectory . "|" . $browsableEntry->EntryId () . "|" . $this->fileSystemSetID,
                  $disabled, $descartesClass,
                  $browsableEntry->EntryUrl (),
                  ($browsableEntryTitle == NULL ? "Documento navegable" : $browsableEntryTitle) . $descartesScene);
      }
      printf("</div>\n</div>\n");
   }

   function Duplicate ($targetDir, $removeBaseDir) {

      $targetPath = $targetDir;

      if (!$removeBaseDir) {
         $pieces = explode("/", $this->entrySetBaseDirectory);
         $targetFolder = array_pop($pieces);
         $targetPath = $targetDir . "/" . $targetFolder;
      }

      $this->entrySetBaseEntry->Duplicate($this->entrySetBaseDirectory, $targetPath);

      foreach ($this->fileSystemEntries as $fileSystemEntry) {
         if($fileSystemEntry->EntryType() == FileSystemEntry::$FST_DIRECTORY)
         $fileSystemEntry->Duplicate($this->entrySetBaseDirectory, $targetPath);
      }

      foreach ($this->fileSystemEntries as $fileSystemEntry) {
         if($fileSystemEntry->EntryType() != FileSystemEntry::$FST_DIRECTORY)
         $fileSystemEntry->Duplicate($this->entrySetBaseDirectory, $targetPath);
      }

      return $targetPath;
   }

}
