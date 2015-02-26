
<?php


/** ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 * 									class Log4PHP
 * 							Logger pour php 
 *  										
 * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 * @author samy etienne
 * @copyright 2008 2009 
 * @name Log4PHP
 * @package  Log4PHP
 * @see README Doc
 * @version 1.0.0
 * 
 * 
 * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 * Log4PHP est un logger pour le langage php , il se contente de rediger un fichier de log dicté par les types de log 
 * dans un fichier monProjet.log au path donné
 * @example      
 * 				le nom du projet, le path, le nombre de ligne max est defini dans un fichier properties Log4PHP.properties
 * 				$logger=new Log4PHP();
 * 
 * 
 * 
 * #############################################!!!!!!##############################################################################################################
 * @see Log4PHP necessite que le path du dossier de log defini dans config/Log4PHP.properties.php existe et soit autoriser en lecture ecriture a tous le monde (777)
 * #################################################################################################################################################################
 * 
 *  
 * 
 * 
 * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 */
	
	require 'config/Log4PHP.properties.php';
	
	
	class Log4PHP
	{
		/************
		 * CONSTANTES
		 ************/
		//define(ET," AND ");
		
		/**
		 * Déclaration des attributs.
		 *
		 * @var $file							Fichier de log dans lequel sera inscris toutes les erreurs	( en réalité pointeur vers le fichier)
		 */
		
		private $file;

		/***********
		 * METHODES
		 ***********/
		
		/**
		 * __construct()
		 * constructeur de l'objet 
		 *il créer le fichier de log s'il n'existe pas et le met dans $file
		 * 
		 * 
		 */
		public function __construct()
		{
			//on affecte à $file la valeur de la properties file
			$log_file=$GLOBALS['path'].$GLOBALS['file'].".log";			
			//Si le fichier de log est trop gros, suerieur a Log4PHP.properties.php->sizeLog, alors on le renomme en xxxx.x 
			//et on recréra un vierge afin de ne pas avoir un fichier illlisible
			if(file_exists($log_file))
			{
				
				//Si notre log est supérieur ou égal à la logSize max definit dans le Log4PHP.properties.php alors on va le renommer en vue d'en creer un autre
				if(filesize($log_file)>$GLOBALS["logSize"])
				{
					//On detecte les fichier de log archivés et on le desplace de n+1
					//On peut avoir 5 fichiers de log au max
					for($i=5;$i>=1;$i--)
					{
						if(file_exists($log_file.".".$i))
						{
							if($i==5)
							{
								unlink($log_file.".".$i);	
							}else{
								$e=$i+1;
								rename($log_file.".".$i,$log_file.".".$e);
							}
						}
					}
					rename($log_file,$log_file.".1");
					
				}
			}
			//On ouvre le fichier en ecriture seule ,
			//s'il n'existe pas on tente de le creer (d'ou l'importance du 777 sur /log/)
			//Le curseur est positionner automatiquement à la fin du fichier
			$this->file=fopen($log_file,'a');
		}
		
		
		
		
		
		/**
		 * function log($type,$log)
		 * 
		 * @param $type	type de log a inscrire : ERROR, INFO , WARNING ou SUCCESS , information présente en debut de ligne
		 * @param $log	erreur à inscrire
		 * 
		 * log() permet d'inscrire un log du type $type au contenu $log, il inscrira ce log 
		 * et stock le resultat dans $this->request_data
		 * 
		 *
		 * @return $bool		booleen de verification de l'éxecution de la requete
		 */
		private  function log($type,$log)
		{
			$date="[".date("D M j H:i:s Y" )."] ";
			$typ=" [".$type."] ";
			$string=$date.$typ.$log."\n";
			fwrite($this->file,	$string);
		}
		
		
		
		/**
		 * find($id)
		 * @return $bool		booleen de verification de l'éxecution de la requete
		 */
		public function  ERROR($log)
		{
			$this->log("ERROR",$log);
		}
		
		/**
		 * find($id)
		 * @return $bool		booleen de verification de l'éxecution de la requete
		 */
		public function WARNING($log)
		{
			$this->log("WARNING",$log);
		}
		
				/**
		 * find($id)
		 * @return $bool		booleen de verification de l'éxecution de la requete
		 */
		public function  INFO($log)
		{
			$this->log("INFO",$log);
		}
		
		/**
		 * find($id)
		 * @return $bool		booleen de verification de l'éxecution de la requete
		 */
		public function SUCCESS($log)
		{
			$this->log("SUCCESS",$log);
		}
		
		
		/**
		 * __destruct()
		 * detruit l'objet en cours
		 * set tout à null
		 * 
		 */
		public function __destruct()
		{
			fclose($this->file);
			$this->file=null;
		}
	
	}
?>