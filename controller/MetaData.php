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
   
   function MetaDataType () { return $this->metadataFileType; }
   
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
      if ($newSchoolTheme !== $newSchoolTheme)
         $this->schoolTheme = $newSchoolTheme;
         
      return $this->schoolTheme;
   }
   
   function ObjectPlataform ($newPlatform = NULL) {
      if ($newPlatform !== NULL)
         $this->objectPlatform = $newPlatform;
         
      return $this->objectPlatform;
   }
   
   function MetaData ($metadataFilePath) {
      $this->metadataFileType = self::$MD_NO_METADATA;
      
      $this->projectTitle        = NULL;
      $this->objectTitle         = NULL;
      $this->objectDescription   = NULL; // SimpleXMLElement
      $this->objectKeywords      = NULL;        // SimpleXMLElement
      $this->objectThumbnails    = NULL;
      $this->objectCredits       = NULL;
      $this->objectInfo          = NULL;
      $this->schoolLevel         = NULL;
      $this->schoolArea          = NULL;
      $this->schoolTheme         = NULL;        // SimpleXMLElement
      $this->objectPlatform      = NULL;
      
      if (file_exists($metadataFilePath)) {
         $this->LoadMetaData($metadataFilePath);
      }
      
      return $this->metadataFileType;
   }
	
   function LoadMetaData ($filePath) {
      
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
      }
   }
}