<?php

/** ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 * 								class Repository_Transaction
 * 						Classe de gestion d'une transaction d'accès au données
 *  										
 * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 * @author samy etienne
 * @copyright 2008 2009 
 * @name ADO4php
 * @package  ADO4php
 * @see README Doc
 * @version 0.1.0
 * 
 * @require log4php_Log4PHP
 * @require xml_simpleXML
 * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 * Cet objet permet de crée une connexion à  une base données de nom $name, de type $type, localiser sur un serveur $server
 * avec le compte de connexion $user//$password.
 * Cet objet permet d'encapsuler différents objets d'interogation, d'insertion, de mise à  jour dans une transaction et s'occupant de
 * toutes exceptions de bas niveau.
 * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 * 
 * @see 
 *
 * 
 * $products   = Mage::getModel('catalog/product')->setStoreId($storeId)->getCollection()
 * ->addFieldToFilter('visibility', array('eq' => Mage_Catalog_Model_Product_Visibility::VISIBILITY_IN_SEARCH ))
 * ->addFieldToFilter('dad_date_of_publication', array('notnull' => 1 ))
 * ->addAttributeToSelect(array('visibility', 'dad_date_of_publication', 'name', 'price', 'small_image', 'image', 'url_key'), 'left')
 * ->addAttributeToSelect(array('special_price', 'special_from_date', 'special_to_date'), 'left')

*
* @todo for next version(1.0.0):
* 						-->[THIS]gestion des transaction:_ _pour chaque implementation d'un objet Repository_transaction, une transaction sera lancée, 
* 																			puis validée ou invalidée à la destruction de l'object, afin d'avoir une assurance sur la cohérence des données
* 																			_les transactions n'étant pas gérées par tous les types de sgbd, il faudra prendre en compte ce paramètres
* 																			_
* 																					*fct concernés:
* 																						->start_transaction: a executer à la création de l'objet --> débute une transaction
* 																						->rollback_transaction: a executer à la destruction de l'objet si une exception à été catché--> invalide  une transaction et par conséquent ne réalise pas les opérations prévues dans celle-ci,revient à 0!
* 																						->commit_transaction: a executer à la destruction de l'objet si aucun exceptions n'a été catché--> valide une transaction et par conséquent execute les opérations de celle-ci
* 															
* 						-->Rapport de test sur le support multibases!
* 
* 
* 
* 						-->[FINDER]gestion des jointures...
* 
* 
* 						-->[THIS]gestion plus fine des dsn_vars :
* 																			_gerer le problemes des DSN_VARS provenant du dsn_patterns.xml :
* 																										->soit suppression de dsn_vars, du parsing etc.....; switch dans le setDsn_patterns
* 																										->soit gestion plus fine : réussir à récuperer la variable $+$vars[$element]!!!
* 
* 
* 
* 						-->[MAJER]gestion des updates et delete sur les données:
* 																			_permettre de réaliser toutes les opérations CRUD classique d'un sgbd en créant un nouvel objet majers permettant l'update et le delete
* 																			_... [en reflection]
*/




class Repository_Transaction
{
	/**
	 * Declaration des constantes
	 *  
	 * @var 
	 */
	//define(ET," AND ");

	/**
	 * Declaration des attributs.
	 *
	 * @var $uid				identifiant unique de la transaction
	 * @var $server			url du serveur de la base Ã  utiliser	
	 * @var $type			type de la base Ã  utiliser (mysql,pgsql,sqlite,firebird,informix,odbc,odbc:ibm,oci)
	 * @var $name			nom de la base Ã  utiliser
	 * @var $user			login du compte de connexion Ã  utiliser
	 * @var $assword			password du compte de connexion Ã  utiliser
	 * @var $dsn			Data Source Name
	 * @var $dbh			Data ....
	 * @var $logger			Représente le loger de la session en cours
	 * 
	 * @var $error			tableau contenant la derniere erreur (code,message,line)
	 * @var $dsn_patterns objet de correspondance $type->dsn pattern
	 */
	private $uid;
	private $server;
	private $type;
	private $name;
	private $user;
	private $password;
	private $dsn;
	public $dbh;
	public $dsn_patterns;
	public $logger;
	
	private $error;

	/**~~~~~~~~~~~~~~~~~~~~~
	* Declaration des methodes
	~~~~~~~~~~~~~~~~~~~~~*/

	/**
 	 * __construct()
 	 * Constructeur vide
 	 *
 	 */
	/*
	public function __construct()
	{

	}
	*/
	/**
 	 * __construct($server,$type,$user,$password)
 	 * Constructeur d'une Repository_Transaction
 	 * @example 
 	 * @param  $server		url du serveur de la base donnÃ©es
 	 * @param  $type			type de la base de donn&eacutes;es
 	 * @param  $ user			login du compte de connexion Ã  utiliser
 	 * @param  $password		password du compte de connexion Ã  utiliser
 	 * @param $name			nom de la base Ã  utiliser
 	 * 
 	 * @return  $bool			booleen de verification de la creation de l'objet 
 	 */
	public function __construct($server,$type,$user,$password,$name)
	{
		/**
		 * on creer tout d'abord une session pour les objets Repository_Transaction
		 */
		//$_SESSION['Repository_Transaction']=array();
		//On instancie notre logger
		$this->logger=new Log4PHP();
			
			
		//on charge tout d'abord les schema de nom de dsn_patterns.xml dans notre variable dsn_patterns
		//en passant par un objet du type SimpleXML_object->toArray()
		$all_patern=simplexml_load_file(BASE_URL."assets/config/dsn_patterns.xml");
		$SimpleXML_object=new SimpleXML_object($all_patern);
		$this->dsn_patterns=$SimpleXML_object->DsnPatternstoArray();

		$this->setUID();
		$this->server=$server;
		$this->type=$type;
		$this->user=$user;
		$this->password=$password;
		$this->name=$name;
		$this->setDatabaseDSN($this->server,$this->type,$this->name);
		if($this->connect()){
			$bool=true;
		}else{
			$bool=false;
		}
		return $bool;
	}




	/**
 	 * setDataBaseDSN($name,$user,$password)
	 * Etabli les parametres de connexion a la base passee en parametre
	*
	* @param  $server		url du serveur de la base données
 	* @param  $type			type de la base de données
 	* @param $name			nom de la base à utiliser
 	* @todo realiser le support multi bases via le fichier xml de patterns
 	*/
	private function setDatabaseDSN($server,$type,$name)
	{
		if(DEBUG):
			echo "server type name ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~<br/>";
			echo $server." ".$type." ".$name."<br/>";
		endif;
		$pattern=$this->dsn_patterns[2][$this->type];
		//On réalise le formatage de la dsn selon le type de database fournit 
		$elements=explode("#",$pattern);
		$pattern="";
		foreach($elements as $element)
		{
			if(is_numeric($element)):
					switch($this->dsn_patterns[1] [$element])
					{
						case "type":$pattern.=$this->type;
							break;
						case "name":$pattern.=$this->name;
							break;
						case "server":$pattern.=$this->server;
							break;
						case "user":$pattern.=$this->user;
							break;
						case "password";$pattern.=$this->password;
							break;
					}
			else:
					$pattern.=$element;
			endif;
		}
		if(DEBUG):
			echo "pattern formatter:<br/>~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~<br/>";
			echo $this->type."-->".$pattern."<br/>";
		endif;
		$this->dsn =$pattern;
	}

	/**
 	 * connect()
 	 * Connexion à  la base 
 	 * 
 	 * @return  $bool		booleen de verification de la connexion 
 	 */
	private function connect()
	{
		/**
		 * Gestion de la connexion
		 */
		$bool=true;
		try {
			$this->dbh = new PDO($this->dsn, $this->user, $this->password);
			$this->dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, TRUE);
			$this->dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$this->logger->SUCCESS("Connexion etablie avec succes!");
		} catch (PDOException $e) {
			$this->error[0]=$e->getCode();
			$this->error[1]=$e->getMessage();
			$this->error[2]=$e->getLine();
			$bool=false;
			$this->logger->ERROR('Erreur PDO :lors de l"initialisation de l objet PDO:'.$this->error[0].' at line '.$this->error[2].' <br/><pre>'. $e->getMessage().'</pre>');
			die('Erreur PDO :lors de l"initialisation de l objet PDO<pre>'. $e->getMessage().'</pre>');
		}
		return $bool;
	}

	/**
 	 * deconnect()
 	 * Deconnexion de la base
 	 * 
 	 * @return  $bool 		booleen de verification de la deconnexion
 	 */
	private function deconnect()
	{
		if($this->dbh=null){
			$bool= true;
		}else{
			$this->error[0]="";
			$this->error[1]="Problème lors de la déconnexion de  la base";
			$this->error[2]="171";
			$bool=false;
		}
		return $bool;
	}
	/**~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	* 					Méthodes liés aux transactions
	~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~*/
	/**
 	 * startTransaction()
 	 * Debut de transaction
 	 * 
 	 * @return  $bool 		booleen de verification que la transaction Ã  debuter correctement
 	 */
	public function startTransaction()
	{
		$this->dbh->begintransaction();
	}

	/**
 	 * commitTransation()
 	 * Valide une transaction
 	 * 
 	 */
	public function commitTransaction()
	{
		$this->dbh->commit();
	}

	/**
 	 * rollbackTransaction()
 	 * Invalide une transaction
 	 * 
 	 */
	public function  rollbackTransaction()
	{
		$this->dbh->rollBack();
	}
	/*~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~*/


	/**
 	 * SetUID()
 	 * DÃ©finit Ã  la transaction un identifiant unique
 	 * BasÃ© sur le timstamp du moment de la crÃ©ation de l'objet et sur un identifiant unique stockÃ©e en session
 	 * 
 	 */
	private function setUID()
	{
		if(!$Rt_array_lenght=@count($_SESSION['Repository_Transaction'])){$Rt_array_lenght=0;}
		if(DEBUG):
			echo "SESSION:~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~<br/>";
			var_dump($_SESSION);
			echo "-->size:".$Rt_array_lenght."<br/>";
		endif;
		$this->uid=(time()."_".($Rt_array_lenght+1));
		if(DEBUG):
			echo "<br/><br/>-->uid".$this->uid."<br/>";
		endif;
		$_SESSION['Repository_Transaction'][($Rt_array_lenght+1)]=$this->uid;
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
	 * getErrorNumber()
	 * retourne le dernier numéro d'erreur
	 * 
	 * @return  $this->error[0]			variable contenant le dernier numéro d'erreur 
	 */
	 
	public function getErrorNumber()
	{
		return $this->error[0];
	}
	
	/**
	 * __destruct()
	 * detruit l'objet en cours
	 * 
	 * @return $bool		booleen de verification de la destruction de l'objet
	 */
	public function __destruct()
	{
		if($this->deconnect()){
			$bool=true;
		}else{
			$bool=false;
		}
		$this->server=null;
		$this->type=null;
		$this->name=null;
		$this->user=null;
		$this->password=null;
		$this->dsn=null;
		return $bool;
	}
}
