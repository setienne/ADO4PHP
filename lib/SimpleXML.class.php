<?php

/** ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 * 								class SimpleXML_object
 * 						Classe de gestion d'un objet simpleXML
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
 * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 * Cet objet permet de r�aliser diff�rents traitements sur un objet de type simpleXML afin de faciliter la vie de l'utilisateur
 * transformation en un array....
 * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 * 
 */


 

class SimpleXML_object
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
		 * @var $object				Objet simpleXML � traiter
		 * @var $error			tableau contenant la derniere erreur (code,message,line)
		 */
		private $object;
		
		//private $error;
	
		/**~~~~~~~~~~~~~~~~~~~~~
		* Declaration des methodes
		~~~~~~~~~~~~~~~~~~~~~*/

		/**
	 	 * __construct()
	 	 * Constructeur vide
	 	 *
	 	 */
	public function __construct($object)
	{
		$this->object=$object;
	}

	/**
	 * toArray2level()
	 * fonction permettant de transformer un objet SimpleXML du type:
	 * 															->fils
	 * 																->attributes
	 * 																->attributes
	 *	   															->fils
	 * 																->attributes
	 * 																->attri[...]
	 *  en tableau associatif du type array(attr1=>attr2,attr1=>...
	 * 
	 * @return $arrray	tableau d�riv� de l'objet simpleXML
	 */
	public function OperatorstoArray()
	{
		foreach($this->object->children() as $children)
		{
			$a=0;
			foreach($children->attributes() as $childchildren)
			{
				$a++;
				if($a==1):	$index=$childchildren[0];	else:$array[(string) $index]=(string) $childchildren[0];endif;
			}
		}
		
		return $array;
	}

/**
	 * toArray3level()
	 * fonction permettant de transformer un objet SimpleXML du type:
	 * 															->fils
	 * 																->attributes
	 * 																->attributes
	 *	   															->fils
	 * 																->attributes
	 * 																->attri[...]
	 *  en tableau associatif du type array(attr1=>attr2,attr1=>...
	 * 
	 * @return $arrray	tableau d�riv� de l'objet simpleXML
	 */
	public function DsnPatternstoArray()
	{
		if(DEBUG):
			$zd->dump($this->object);
		endif; 
		$i=0;
		foreach($this->object->children() as $children)
		{
			if(DEBUG): echo "<br/>".$i."<br/>";endif;
			$i++;
			if($i==1):
			//DSN_vars
				$a=0;
				foreach($children as $childchildren)
				{
					$a++;
					//echo "<br/>vars".$a."<br/>";
					//@todo rajouter un niveau on en est la ac un tableau contenant des objets simplesmxl a pars� a nouveau pour 
					// recuperer les 2 attributs id et value
					$b=0;
					foreach($childchildren->attributes() as $attributes)
					{
						//$zd->dump($attributes);
						$b++;
						if($b==1):$index=$attributes;else:$array_vars[(string) $index]= (string) $attributes;endif;
					}	
				}
			else:
			//DSN_patterns
		$a=0;
				foreach($children as $childchildren)
				{
					$a++;
					//echo "<br/>patterns".$a."<br/>";
					//@todo rajouter un niveau on en est la ac un tableau contenant des objets simplesmxl a pars� a nouveau pour 
					// recuperer les 2 attributs id et value
					$b=0;
					foreach($childchildren->attributes() as $attributes)
					{
						//$zd->dump($attributes);
						//echo "<br/>patterns_attributes".$a."_".$b."<br/>";
						$b++;
						if($b==1):	$index=$attributes;	else: $array_patterns[(string) $index]=(string) $attributes;endif;
					}	
				}
			endif;
			
		}
		
		$array=array(1=>$array_vars,2=>$array_patterns);
		return $array;
	}













}

?>