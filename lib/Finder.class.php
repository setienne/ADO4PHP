<?php


/** ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 * 									class Finder
 * 						Classe d'interrogation d'une base de donnÃ©es
 *  										
 * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 * @author samy etienne
 * @copyright 2008 2009 
 * @name ADO4php
 * @package  ADO4php
 * @see README Doc
 * @version 0.1.0
 * 
 * 
 * @require log4php_Log4PHP
 * @require xml_simpleXML
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
* @todo for next version(1.0.0):
* 						-->gestion des jointures:_permettre de réaliser des jointures sur plusieurs tables  
* 																	_les transactions n'étant pas gérées par tous les types de sgbd, il faudra prendre en compte ce paramètres
* 																	_
* 																			*fct concernés:
* 																					->addJoinToSelect: méthode permettant de définir les tables et alias ainsi que le type de jointure à éfféctuée
* 																					-> ...															
* 	
* 						-->
* 				
*
*
* $products   = Mage::getModel('catalog/product')->setStoreId($storeId)->getCollection()
* ->addFieldToFilter('visibility', array('eq' => Mage_Catalog_Model_Product_Visibility::VISIBILITY_IN_SEARCH ))
* ->addFieldToFilter('dad_date_of_publication', array('notnull' => 1 ))
* ->addAttributeToSelect(array('visibility', 'dad_date_of_publication', 'name', 'price', 'small_image', 'image', 'url_key'), 'left')
* ->addAttributeToSelect(array('special_price', 'special_from_date', 'special_to_date'), 'left')

*/
	
	
	
	
	class Finder
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
		 * @var $table_name						Nom de la table à interroger	
		 * @var $transaction_uid				identifiant de la transaction qui encapsule le finder
		 * @var $criteria						Tableau de criteres sous la forme : ((0=>champ,1=>operateur,2=>valeur),(0=>... ou ()=none
		 * @var $field							Tableau de champs sous la forme :(0=>champ1,1=>champ2...) ou ()=*
		 * @var $request						Objet stockant la requête réalisée par le Finder
		 * @var $request_data					Données retournées par le finder
		 * @var $Repository_Transaction			Objet Repository_Transaction encapsulant le finder
		 * @var $operators						Tableau de raprochement entre opérateurs friendly et opérateurs SQL
		 * @var $logger							Représente le loger de la session en cours
		 * @var $error							Chainede récupération d'une erreur
		 */
		private $table_name;
		//private $transaction_uid;
		private $criteria;
		private $field;
		private $request;
		private $request_data;
		private $Repository_Transaction;
		private $logger;
		
		private $error;
		
		/** 
		 * How to define an array as constant with define?
		 *	there is a simple way to define an array of values as constant...just
		 * define ("constant-name","value1,value2,value3,value4");
         * $t_array = preg_split('/,/', constant(constant-name), -1, PREG_SPLIT_NO_EMPTY);
		 * var_dump($t_array);
		 * array(4) { [0]=> string(6) "value1" [1]=> string(6) "value2" [2]=> string(6) "value3" [3]=> string(6) "value4" }
		 * Now you can iterate over the array defined as constant ...
		 * 
		 * 
		 */
		public $operators;/*=array("eq"=>" = ",
									"sup"=>" > ",
									"inf"=>" < ",
									"supeq"=>" >= ",
									"infeq"=>" <= ",
									"li"=>" like ",
									 "in"=>" in ",
									 "notin"=>" not in ");*/
		/**************************
		 * Déclaration des méthodes
		 **************************/
		
		/**
		 * __construct(table)
		 * constructeur de l'objet 
		 *
		 * @param $table_name 		nom de la table sur lequelle le finder agira
		 * 
		 */
		public function __construct($table_name,$Repository_Transaction)
		{
			//On instancie notre logger
			
			$this->logger=new Log4PHP();
			//on charge tout d'abord les opérateurs de operators.xmll dans notre variable operators
			//en passant par un objet du type SimpleXML_object->toArray()
			
			/**
			 * @todo trouver un moyen dynamique de récuperer le fichier de config 
			 */
			$all_operators=simplexml_load_file(BASE_URL."assets/config/operators.xml");
			$SimpleXML_object=new SimpleXML_object($all_operators);
			$this->operators=$SimpleXML_object->OperatorstoArray();
			
			$this->table_name=$table_name;
			$this->Repository_Transaction=$Repository_Transaction;
		}
		
		/**
		 * __construct(table)
		 * constructeur de l'objet 
		 *
		 * @param $table_name 		nom de la table sur lequelle le finder agira
		 * 
		 */		
		/*
		public function __construct($table_name,$Repository_Transaction,$criteria,$field)
		{
			
		}
		*/
		
		/**
		 * find($id)
		 * réalise un SELECT * FROM $this->table WHERE id=$id;
		 * et stock le resultat dans $this->request_data
		 * 
		 * @param  $id			identifiant de l'enregistrement a retourner
		 * @return $bool 		booleen de verification de la bonne execution de la requete
		 * 
		 * find()
		 * fonction appelé pour récupérer les résultats de la requête non construite 
		 * mais définit par field et criteria 
		 *
		 * @return $bool		booleen de verification de l'éxecution de la requete
		 */
		public function find()
		{
			//on test le nombre d'argument passé en paramètres
			//surcharge de la fonction find ->find(id)
			if(func_num_args ()>0)
			{
					//On recupere l'id passé en paramètre
					$Args = func_get_args ();
					$id=$Args[0];
					//Booléen de vérification du fonctionnement de la requete
					$bool=true;
					
					 //Construction de la requête , les enregistrements seront classés par  ordre ascendant de l'id
					$this->request="SELECT * FROM ".$this->table_name." WHERE id=".$id." ORDER BY id";
					 $bool=$this->execute();
					return $bool;
			
			}else{

				//On construit la requête et on appelle execute
				$this->request=" SELECT ";
				//fields
				if(count($this->field)!=0)
				{
						$i=0;
						foreach($this->field as $field)
						{
							$i++;
							$this->request.=" ".$field;
							if($i!=count($this->field)){$this->request.=",";}
						}
				}else{
					$this->request.=" * ";
				}
			
				$this->request.=" FROM ".$this->table_name." ";
				//Criteria
				if(count($this->criteria)!=0)
				{
						$this->request.=" WHERE ";
						$i=0;
						foreach($this->criteria as $crit)
						{
							$i++;
							$this->request.=" ".$crit[1].$this->operators[$crit[2]].$crit[3];
							if($i!=count($this->criteria)){$this->request.=" AND ";}
						}
				}
				//echo "<br/>Requete:".$this->request."<br/>";
				//execution de la requête 
				return $bool=$this->execute();
				
			}
			
		}
		
		/**
		 * findAll()
		 * réalise un SELECT * FROM $this->table;
		 * et stock le resultat dans $this->request_data
		 * 
		 * @return $bool			Booléen de vérification
		 */
		public function findAll()
		{
			//Booléen de vérification du fonctionnement de la requete
			$bool=true;
			
			 //Construction de la requête , les enregistrements seront classés par  ordre ascendant de l'id
			$this->request="SELECT * FROM ".$this->table_name." ORDER BY id";
			$bool=$this->execute();
			return $bool;
		}
		
		/**
		 * getRequestData()
		 * getter du tableau de résultats request_data
		 * @return request_data			tableau de résultats de la requête
		 */
		public function getRequestData()
		{
			return $this->request_data;
		}
	
		/**
		 * getErrorStr()
		 * retourne le dernier message d'erreur
		 * 
		 * @return  $this->error[1]			variable contenant le dernier message d'erreur 
		 */
		 
		public function getErrorStr()
		{
			return $this->error[1];
		}
		
		/**
		 * addFieldToSelect($fields[])
		 * set les champs à retourner ($this->field[])
		 * @param $fields 		tableau de champs à retourner 
		 * 
		 */
		public function addFieldToSelect($fields)
		{
			$this->field=$fields;
		}
		
	/**
		 * addFieldToFilter($criteria[])
		 * set les critères de la requête($this->criteria[])
		 * @param $criteria 		tableau de critères
		 * 
		 */
		public function addFieldToFilter($criteria)
		{
			$this->criteria=$criteria;
		}
		
		/**
		 * addJoinToSelect
		 * 
		 * 
		 * @todo implementation de cette fonction dans version 1.0.1
		 *
		 * @param unknown_type $array
		 *//*
		public function addJoinToSelect($array)
		{
			//????
		}
		*/

		
		/**
		 * execute()
		 * fonction executant la requete et stockant le resultat dans $this->request_data
		 *
		 * @return $bool 		booleen de verification de la bonne execution de la requete
		 */
		private function execute()
		{
			$bool=true;
			// On execute le finder en utilisant le repository_transaction associé
				try {
					
				$query=$this->Repository_Transaction->dbh->prepare($this->request);
				$query->execute();
				$this->request_data=$query->fetchAll();
				$this->logger->SUCCESS("Traitement de la requête réussi!req=> ".$this->request);
				} catch (PDOException $e) {
					$this->error[1]="Problème à 'execution de la requête";
					$this->logger->ERROR($this->error);
					$bool=false;
				    die ('Erreur PDO : traitement de la requete<pre>'. $e->getMessage().'</pre>');
				  }
				  
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
			
			$this->server=null;
			$this->type=null;
			$this->name=null;
			$this->user=null;
			$this->password=null;
			$this->dsn=null;
		}
	
	}


?>