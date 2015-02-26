<?php

/** ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 * 									class Majer
 * 						Classe de persistance d'objets 
 *  										
 * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 * @author samy etienne
 * @copyright 2008 2009 
 * @name ADO4php
 * @package  ADO4php
 * @see README Doc
 * @version 1.0.0
 * 
 * 
 * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 * Cet objet permet de realiser une interrogation sur la table $table, en récupérant les champs $field[] répondant aux criteres $criteria[], tout objet 
 * finder est encapsulé dans un objet Repository_Finder identifier par $uid afin de gérer automatiquement les transactions et exceptions levées 
 *2 facons d'utiliser un finder:
 * 	requete simpliste (find(id) ou findAll()->return all fields):
 * @example      $finder->findAll();  $request_data=$finder->getData();s
 * 	requete complexe (criteres...)
 * @example 
 * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 * 
* @todo for current version(1.0.0):
* 						-->gerer une interface de persistance d'objet 
* 						-->
* 				
*
*
*@require RepositoryTransaction
*@require Finder
*/
	
	
	
	
	class Majer
	{
		/**
		 * Déclaration des constantes
		 * 
		 * @var 
		 */
		//define(ET," AND ");
		
		/**
		 * Déclaration des attributs.
		 *
		 * @var $table_name					Nom de la table à interroger	
		 * @var $request				
		 * @var $query_result								
		 * @var $logger						Représente le loger de la session en cours
		 * @var $operators						Tableau de raprochement entre opérateurs friendly et opérateurs SQL	
		 * @var $Repository_Transaction			Objet Repository_Transaction encapsulant le finder		 					
		 * */
		
		private $table_name;
		private $request;
		private $query_result;
		private $logger;
		private $operators;
		private $Repository_Transaction;
		
		
		
		/**
		 * __construct(table)
		 * constructeur de l'objet : traitement de la requete

		 * 
		 *
		 * @param $table_name 		nom de la table sur lequelle le finder agira
		 * 
		 */
		public function __construct()
		{
			//On instancie notre logger
			$this->logger=new Log4PHP();
			
			//on charge tout d'abord les opèrateurs de operators.xmll dans notre variable operators
			//en passant par un objet du type SimpleXML_object->toArray()
			$all_operators=simplexml_load_file(BASE_URL."assets/config/operators.xml");
			$SimpleXML_object=new SimpleXML_object($all_operators);
			$this->operators=$SimpleXML_object->OperatorstoArray();
			
			/***************************************************\
			|**Instanciation d'un objet Repository_Transaction**|
			\***************************************************/
			if(!$this->Repository_Transaction=new Repository_Transaction($GLOBALS["DataSettings"]["server"],$GLOBALS["DataSettings"]["type"],$GLOBALS["DataSettings"]["user"],$GLOBALS["DataSettings"]["password"],$GLOBALS["DataSettings"]["dataBase"])):
				echo "<pre>Probl&egrave;me lors de l'instanciation du Repository_Transaction!</pre>";
				if(DEBUG):$debug->dump($Repository_Transaction->getErrorStr());endif;
			endif;
		}
		
		
		
		/**
		 * Enter description here...
		 *
		 * @param unknown_type $object
		 */
		public function prepare ($object)
		{
			
			/**
			 *INSERT: generation uid et inscription en base docn pas d'uid sétté
			 *UPDATE update en base  donc uid sétté!
			 * 				--> on renseigne touts attributs a chaque fois
			 * 
			 * 
			 * 
			 * @todo securite requetes PDO_QUOTE a voir
			 * 
			 */
			if(DEBUG):
				$debug=new Zend_Debug();
				echo"~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~<br/>";
				$debug->dump($object);
			endif;
			
			/*On regarde si l'objet à un uid sétté: si oui alors c'est un update sinon on génére l'uid et on insert*/
			if($object->uid!="")
			{
				$is_update=true;
			}else{
				$is_update=false;
				$object->generateUid();				
			}
			
			$this->table_name=strtolower(get_class($object));
			/**
			 * On récupère le nom des colonnes de la table
			 **/

			/***************************************************\
			|************Instanciation d'un objet Finder********|
			\***************************************************/
			if(!$this->finder=new Finder("INFORMATION_SCHEMA.COLUMNS",$this->Repository_Transaction)):
				echo "<pre>Probl&egrave;me lors de l'instanciation du Fnder!</pre>";
				if(DEBUG):
					$debug->dump($this->finder->getErrorStr());
				endif;
			endif;
			
			/***********On récupére l'ordre par défaut (id=0)************/
			$fields=array(1=>"column_name");
			$criteria=array(1=>array(1=>"table_schema",2=>"eq",3=>"'".$GLOBALS["DataSettings"]["dataBase"]."'"),2=>array(1=>"table_name",2=>"eq",3=>"'".$this->table_name."'"));
			$this->finder->addFieldToFilter($criteria);
			$this->finder->addFieldToSelect($fields);
			$this->finder->find();
			$requestData=$this->finder->getRequestData();
			
			for($i=0;$i<=count($requestData);$i++)
			{
				$schema[$i]=$requestData[$i][0];
			}
			/**
			 * SELECT INFORMATION_SCHEMA.COLUMNS.column_name
				FROM INFORMATION_SCHEMA.COLUMNS
				WHERE INFORMATION_SCHEMA.COLUMNS.table_schema = 'kactus'
				AND INFORMATION_SCHEMA.COLUMNS.table_name = 'profil';
			 */
			if(DEBUG):
			echo "schema:~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~<br/>";
				$debug->dump($schema);
			endif;
			/*On récupère dans $vars les nom des variables en clé et leur valeur en valeur*/
			$vars=get_object_vars($object);
			if(DEBUG):
				echo "vars:~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~<br/>";
				$debug->dump($vars);
			endif;
			/*On récupère dans $varsKeys les noms des variables*/
			$varsKey=array_keys($vars);
			if(DEBUG):
				echo "varskey:~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~<br/>";
				$debug->dump($varsKey);
			endif;
			//Si l'objet n'a pas d'uid il s'agit d'un nouvel objet pour le SI
			if($is_update)
			{
				//UPDATE
				$this->request="UPDATE ".$this->table_name." SET";
				$i=0;
				foreach($vars as $var)
				{
					if(in_array($varsKey[$i],$schema)):
						if($i!=0):
							$this->request.=" ,";
						endif;
						$this->request.=" ".$varsKey[$i]."= '".mysql_escape_string($var)."' "; 						
					endif;
					$i++;
				}
				$this->request.=" WHERE uid='".$object->uid."'";
			}else{
				//INSERT
				$this->request="INSERT INTO ".$this->table_name." ( ";
				$i=0;
				for($i=0;$i<=count($vars);$i++)
				{
					if(in_array($varsKey[$i],$schema)):
						if($i!=0 && $i!=count($vars)):$this->request.=" , ";endif;
						$this->request.=" ".$varsKey[$i]." ";
					endif;
				}
				$this->request.=") VALUES(";
				$i=0;
				foreach($vars as $var)
				{
					if(in_array($varsKey[$i],$schema)):
						if($i!=0):$this->request.=" ,";endif;
						$this->request.=" \"".mysql_escape_string($var)."\" ";
					endif; 
					$i++;
				}
				$this->request.=" );";
			}
			if(DEBUG):
				echo "<br/>############################################################################################################<br/>";
				echo "<br/>requete généré: ".$this->request;
				echo "<br/>############################################################################################################<br/>";
			endif;
			}
		
		
		
		
		
			/**
		 * execute()
		 * fonction executant la requete et stockant le resultat dans $this->request_data
		 *
		 * @return $bool 		booleen de verification de la bonne execution de la requete
		 */
		public function execute()
		{
			$bool=true;
			// On execute le finder en utilisant le repository_transaction associé
				try {
				$this->Repository_Transaction->startTransaction();
				$query=$this->Repository_Transaction->dbh->prepare($this->request);
				$query->execute();
				//$this->query_result=$query->;
				$this->logger->SUCCESS("Traitement de la requête réussi!req=> ".$this->request);
				} catch (PDOException $e) {
					$this->error[1]="Problème à l'execution de la requête";
					$this->logger->ERROR($this->error);
					$this->Repository_Transaction->rollbackTransaction();
					$bool=false;
				    die ('Erreur PDO : traitement de la requete<pre>'. $e->getMessage().'</pre>');
				}
  				$this->Repository_Transaction->commitTransaction();
   			    return $bool;
		}
		
		/**
		 * __destruct()
		 * detruit l'objet en cours
		 * set tout à null
		 * 
		 */
		public function __destruct()
		{
			
			$this->request=null;
			$this->table_name=null;

		}
		
		
		
		
	}

?>