<?php

/*
 *
 *
 * 
 * 
 * 
 */

class BasePersistModel
{
	
	public $uid;

	/**
	 * génère un uid pour l'objet
	 */
	public function generateUid() {
		$this->uid = uniqid(rand(),true);
	}
	
	/**Getter et setter génèrique **/
	
	public  function __get($attributes)
	{
		return $this->$attributes;
	}

	public function setUid($value)
	{
		$this->uid=$value;
		
	}
	public  function __set($attributes,$value)
	{
		
		//S'il s'agit de l'uid on regarde s'il existe en base et dans ce cas on set l'objet avec les valeurs adequat 
		//if($attributes=="uid" && $attributes!="")
		//{
			//@TODO recuperer le nom de la classe fille objet 
			//$finder=new Finder()
			
			
			
		//}else{
			//Sinon on set l'attribut adequat		
			$this->$attributes=$value;			
		//}
	}
	
	/**
	 * Enter description here...
	 *
	 * @param unknown_type $object
	 */
	
	public function makePersistent($object)
	{			
		$majer=new Majer();
		$vars=get_object_vars($object);
		if(DEBUG):
			echo "varsssssss: <br/>";
			$debug->dump($vars);
		endif;
		$majer->prepare($object);
		$majer->execute();		
	}
	
	/**
	 * Enter description here...
	 *
	 * @param unknown_type $object
	 */
	public function makeDelete($object)
	{	
		
	}
}


?>