<?php

class MetaData {

   // Tipo de metadatos
	public static  $MD_PROJECT 	      = 0x01;
	public static 	$MD_LEARNING_OBJECT 	= 0x02;
   public static  $MD_NO_METADATA      = 0x03;

   // Plataforma
   public static  $PLAT_COMMON         = 0x00;
   public static  $PLAT_DESCARTES      = 0x01;

   // Nivel escolar
   public static  $SL_UNKNOWN          = 0x00;
   public static  $SL_PRIMARY          = 0x01;
   public static  $SL_SECONDARY        = 0x02;
   public static  $SL_PREPSCHOOL       = 0x03;
   public static  $SL_UNIVERSITY       = 0x04;

   // Propiedades
   // Archivo de metadatos
   protected $metadataFileType;  // <node type="">...</node>
   protected $metadataFilePath;  // url;

   // Información del proyecto
   protected $projectTitle;      // <proyecto></proyecto>
   protected $projectId;         // NO SE USA ACTUALMENTE

   // Datos generales del objeto
	protected $objectTitle;       // <title></title>
   protected $objectDescription; // <description></descripction>
   protected $objectKeywords;    // <tags></tags> valores separados por comas
	protected $objectThumbnails;  // <Vistas-previas> urls separados por comas
   protected $objectCredits;     // <credits> url
   protected $objectInfo;        // <info> url
   protected $schoolLevel;       // <nivel></nivel>
   protected $schoolArea;        // <area></area>
   protected $schoolTheme;

   protected $objectPlatform;    // <plataforma>Descartes</plataforma>
   protected $objectURL;

   function MetaDataType () { return $this->metadataFileType; }

   function MetadataFilePath () { return $this->metadataFilePath; }

   function Project ($newProject = NULL) {

      if ($newProject !== NULL)
         $this->projectTitle = $newProject;

      return $this->projectTitle;
   }

   function Title ($newTitle = NULL) {

      if ($newTitle !== NULL)
         $this->objectTitle = $newTitle;

      return $this->objectTitle;
   }

   function Description ($newDescription = NULL) {

      if ($newDescription !== NULL)
         $this->objectDescription = $newDescription;

      return $this->objectDescription;
   }

   function Keywords ($keywords = NULL) {

      if($keywords !== NULL)
         $this->objectKeywords = $keywords;

      return $this->objectKeywords;
   }

   function Thumbnails ($thumbnails = NULL) {

      if($thumbnails !== NULL)
         $this->objectThumbnails = $thumbnails;

      return $this->objectThumbnails;
   }

   function Credits ($newUrl = NULL) {
      if ($newUrl !== NULL)
         $this->objectCredits = $newUrl;

      return $this->objectCredits;
   }

   function Info ($newInfo = NULL) {
      if ($newInfo !== NULL)
         $this->objectInfo = $newInfo;

      return $this->objectInfo;
   }

   function SchoolLevel ($newSchoolLevel = NULL) {
      if ($newSchoolLevel !== NULL)
         $this->schoolLevel = $newSchoolLevel;

      return $this->schoolLevel;
   }

   function SchoolArea ($newSchoolArea = NULL) {
      if ($newSchoolArea !== NULL)
         $this->schoolArea = $newSchoolArea;

      return $this->schoolArea;
   }

   function SchoolTheme ($newSchoolTheme = NULL) {
      if ($newSchoolTheme !== NULL)
         $this->schoolTheme = $newSchoolTheme;

      return $this->schoolTheme;
   }

   function ObjectPlataform ($newPlatform = NULL) {
      if ($newPlatform !== NULL)
         $this->objectPlatform = $newPlatform;

      return $this->objectPlatform;
   }

   function objectURL () {
      return $this->objectURL;
   }

   function MetaData ($metadataFilePath) {

      $this->metadataFileType = self::$MD_NO_METADATA;

      $this->projectTitle        = NULL;
      $this->objectTitle         = NULL;
      $this->objectDescription   = NULL; // SimpleXMLElement
      $this->objectKeywords      = NULL; // SimpleXMLElement
      $this->objectThumbnails    = NULL;
      $this->objectCredits       = NULL;
      $this->objectInfo          = NULL;
      $this->schoolLevel         = NULL;
      $this->schoolArea          = NULL;
      $this->schoolTheme         = NULL;        // SimpleXMLElement
      $this->objectPlatform      = NULL;

      $this->metadataFilePath    = $metadataFilePath;
      $this->objectURL           = preg_replace("/manifest.xml/", "index.html", $metadataFilePath);

      if (file_exists($metadataFilePath)) {
         $this->LoadMetadata($metadataFilePath);
      }

      return $this->metadataFileType;
   }

   function LoadMetadata ($filePath) {

      $xml = simplexml_load_file($filePath);

      if (isset ($xml)) {

         $this->projectTitle        = trim($xml->proyecto);
         $this->objectTitle         = trim($xml->title);
         $this->objectDescription   = trim($xml->description); // SimpleXMLElement
         $this->objectKeywords      = trim($xml->tags);        // SimpleXMLElement
         $this->objectThumbnails    = trim($xml->{'Vistas-previas'});
         $this->objectCredits       = trim($xml->credits);
         $this->objectInfo          = trim($xml->info);
         $this->schoolLevel         = trim($xml->nivel);
         $this->schoolArea          = trim($xml->area);
         $this->schoolTheme         = trim($xml->tema);        // SimpleXMLElement
         $this->objectPlatform      = trim($xml->plataforma);
         $this->objectURL           = trim($xml->URL);
      }
   }

   function BuildMetadataFileString () {

      $fileStringTemplate   = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n" .
                                 "<node>\n" .
                                 "\t<Vistas-previas>PREVIEW_REPLACEMENT</Vistas-previas>\n" .
                                 "\t<title>TITLE_REPLACEMENT</title>\n" .
                                 "\t<description>DESCRIPTION_RELPACEMENT</description>\n" .
                                 "\t<nivel>LEVEL_REPLACEMENT</nivel>\n" .
                                 "\t<tema>THEME_REPLACEMENT</tema>\n" .
                                 "\t<area>AREA_REPLACEMENT</area>\n" .
                                 "\t<plataforma>PLATFORM_REPLACEMENT</plataforma>\n" .
                                 "\t<proyecto>PROJECT_REPLACEMENT</proyecto>\n" .
                                 "\t<tags>TAGS_REPLACEMENT</tags>\n" .
                                 "\t<URL>URL_REPLACEMENT</URL>\n" .
                                 "\t<credits>CREDITS_REPLACEMENT</credits>\n" .
                                 "\t<info>INFO_REPLACEMENT</info>\n" .
                                 "</node>";

      $patterns = array("/PREVIEW_REPLACEMENT/", "/TITLE_REPLACEMENT/", "/DESCRIPTION_RELPACEMENT/",
                        "/LEVEL_REPLACEMENT/", "/THEME_REPLACEMENT/", "/AREA_REPLACEMENT/",
                        "/PLATFORM_REPLACEMENT/", "/PROJECT_REPLACEMENT/", "/TAGS_REPLACEMENT/",
                        "/URL_REPLACEMENT/", "/CREDITS_REPLACEMENT/", "/INFO_REPLACEMENT/");

      $sustitutions = array($this->objectThumbnails, $this->objectTitle, $this->objectDescription,
                           $this->schoolLevel, $this->schoolTheme, $this->schoolArea,
                           $this->objectPlatform, $this->projectTitle, $this->objectKeywords,
                           $this->objectURL, $this->objectCredits, $this->objectInfo);

      $metadataFileString = preg_replace($patterns, $sustitutions, $fileStringTemplate);

      return $metadataFileString;
   }

   function SaveChanges () {
      $fp = fopen($this->metadataFilePath . ".tmp", "w");

      if ($fp != FALSE) {
         if (fwrite($fp, $this->BuildMetadataFileString()) != FALSE) {

            fclose($fp);
            rename ($this->metadataFilePath . ".tmp", $this->metadataFilePath);

            printf ("<p style='align: center;'>El archivo se ha guardado exitosamente.</p>");
         }
      }
   }
}
