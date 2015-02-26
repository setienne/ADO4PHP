<?php

/*~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
						Programme de test de la librairie ADO4php
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~*/
/**
 * On debut la session 
 */
session_start();

/**
 * On inclut autoload qui nous permettra de charger les classes a la volée
 */
require("autoload.php");
/*
 * On inclut DataSettings pour recuperer les infos de connexion
 */
require("config/DataSettings.properties.php");
require("config/ADO4PHP.properties.php");

/**
 * On includ Zend->debug: classe de debuggage
 */
$debug=new Zend_Debug();
/**~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 * Instanciation d'un objet Repository_Transaction
 */

if($Repository_Transaction=new Repository_Transaction($GLOBALS["DataSettings"]["server"],$GLOBALS["DataSettings"]["type"],$GLOBALS["DataSettings"]["user"],$GLOBALS["DataSettings"]["password"],$GLOBALS["DataSettings"]["dataBase"]))
{
	echo "<pre>Instanciation d'un Repository_Transaction avec succ&egrave;s!</pre>";
}else{
	echo "<pre>Probl&egrave;me lors de l'instanciation du Repository_Transaction!</pre>";
	$debug->dump($Repository_Transaction->getErrorStr());
}
/**
 * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 */

/**
 * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 * Instanciation d'un objet Finder
 */
if($Finder=new Finder("reference",$Repository_Transaction)):
	echo "<pre>Instanciation d'un Finder avec succ&egrave;s!</pre>";
else:
	echo "<pre>Probl&egrave;me lors de l'instanciation du Fnder!</pre>";
	$debug->dump($Finder->getErrorStr());
endif;
/**~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 * Tentative de réalisation d'une requete complexe
 */
//$fields=array(1=>"id",2=>"titre_ref");
//$criteria=array(1=>array(1=>"id",2=>"eq",3=>1));
//$Finder->addFieldToSelect($fields);
//$Finder->addFieldToFilter($criteria);
//if($Finder->find())
//{
//	echo "<pre>Réalisation d'une  interrogation avec succès!</pre>";
//}else{
//	echo "<pre>Probl&egrave;me lors de l'éxecution du  Finder!</pre>";
//	$debug->dump($Finder->getErrorStr());
//}
/**
 * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 */

/**~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 * Tentative de réalisation d'une requete find(id)
 */
if($Finder->find(3))
{
	echo "<pre>R&eacute;alisation d'une  interrogation avec succ&eacute;s!</pre>";
}else{
	echo "<pre>Probl&egrave;me lors de l'&eacute;xecution du  Finder!</pre>";
	$debug->dump($Finder->getErrorStr());
}
/*
 * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 */

/**~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 * Tentative de réalisation d'une requete  findAll()
 *//*
if($Finder->findAll())
{
	echo "<pre>Réalisation d'une  interrogation avec succès!</pre>";
}else{
	echo "<pre>Probl&egrave;me lors de l'éxecution du  Finder!</pre>";
	$debug->dump($Finder->getErrorStr());
}
/**
 * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 */



echo "<br/><br/>";
echo "R&eacute;sultat(s):";
echo "<br/>";
echo "~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~<br/>";
$debug->dump($Finder->getRequestData());
echo "~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~<br/>";


//$debug->dump($Repository_Transaction->dbh);
/*
echo "Session:<br/>~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~<br/>";
*/


/**
 * Destruction de la Repository_Transaction
 *//*
if($Repository_Transaction->__destruct())
{
	echo "<pre>Destruction de la Repository_Transaction avec succ&egrave;s!</pre>";
}else{
	echo "<pre>Probl&egrave;me lors de la destruction du Repository_Transaction!</pre>";
	$debug->dump($Repository_Transaction->getErrorStr());
}

//$debug->dump($Repository_Transaction->dbh);
/**
 * @debug setUID()
 */
/*
$debug->dump($_SESSION);
*
echo "objet Repository_transaction->dsn_patterns:<br/>";
echo "~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~<br/>";
$debug->dump($Repository_Transaction->dsn_patterns);
echo "~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~<br/>";
echo "objet Finder->operators:<br/>";
echo "~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~<br/>";
$debug->dump($Finder->operators);
echo "~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~<br/>";

*/


/*
 * Utilisation d'objets référence
 * 
 * 
 * 
 */
require_once("model/Reference.php");
echo "~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~<br/>";
echo "Objet réference:<br/>";
$data=$Finder->getRequestData();
	$reference=new Reference(20,"test persistance ",$data[0]["url_ref"],$data[0]["description_ref"],$data[0]["short_description_ref"],"1142425290499c9e47a15a00.77191713");
		$reference->height_logo=240;
		$reference->width_logo=340;
		$reference->logo="img/jpg.jpg";
		$reference->width_logo=340;
		$reference->height_logo=300;
		$reference->apercu_url1=$data[0]["apercu_url1"];
		$reference->titre_url1=$data[0]["titre_url1"];
		$reference->description_url1=$data[0]["description_url1"];
		$reference->height_url1=$data[0]["height_url1"];
		$reference->width_url1=$data[0]["width_url1"];
		$reference->apercu_url2=$data[0]["apercu_url2"];
		$reference->titre_url2=$data[0]["titre_url2"];
		$reference->description_url2=$data[0]["description_url2"];
		$reference->height_url2=$data[0]["height_url2"];
		$reference->width_url2=$data[0]["width_url2"];
		$reference->apercu_url3=$data[0]["apercu_url3"];
		$reference->titre_url3=$data[0]["titre_url3"];
		$reference->description_url3=$data[0]["description_url3"];
		$reference->height_url3=$data[0]["height_url3"];
		$reference->width_url3=$data[0]["width_url3"];
		$reference->apercu_url4=$data[0]["apercu_url4"];
		$reference->titre_url4=$data[0]["titre_url4"];
		$reference->description_url4=$data[0]["description_url4"];
		$reference->height_url4=$data[0]["height_url4"];
		$reference->width_url4=$data[0]["width_url4"];
		$reference->rubrique=$data[0]["rub"];		
$reference->makePersistent();
$debug->dump($reference);
echo "~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~<br/>";

echo "~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~<br/>";








/**
 * On kill la session 
 */
session_destroy();

?>
