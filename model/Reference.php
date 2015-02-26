<?php
define('DATE',date("Y-m-d"));
load("BasePersistModel","model");
class Reference extends BasePersistModel
{
	protected $id;
	protected $titre;
	protected $url;
	protected $description;
	protected $short_description;
	protected $date;
	protected $logo;
	protected $height_logo;
	protected $width_logo;
	
	protected $apercu_url1;
	protected $titre_url1;
	protected $description_url1;
	protected $height_url1;
	protected $width_url1;
	
	protected $apercu_url2;
	protected $titre_url2;
	protected $description_url2;
	protected $height_url2;
	protected $width_url2;
	
	protected $apercu_url3;
	protected $titre_url3;
	protected $description_url3;
	protected $height_url3;
	protected $width_url3;
	
	protected $apercu_url4;
	protected $titre_url4;
	protected $description_url4;
	protected $height_url4;
	protected $width_url4;
	
	protected $rubrique;

	
	public function __construct($id,$titre,$url,$description,$short_description,$uid=null,$date=DATE,$logo=null,$height_logo=null, $width_logo=null,$apercu_url1=null,$titre_url1=null, $description_url1=null,$height_url1=null,$width_url1=null, $apercu_url2=null,$titre_url2=null, $description_url2=null,$height_url2=null, $width_url2=null, $apercu_url3=null, $titre_url3=null, $description_url3=null, $height_url3=null, $width_url3=null, $apercu_url4=null, $titre_url4=null, $description_url4=null, $height_url4=null, $width_url4=null, $rubrique=0)
	{
		$this->uid=$uid;
		$this->id=$id;
		$this->titre=$titre;
		$this->url=$url;
		$this->description=$description;
		$this->short_description=$short_description;
		$this->date=$date;
		$this->logo=$logo;
		$this->width_logo=$width_logo;
		$this->height_logo=$height_logo;
		$this->apercu_url1=$apercu_url1;
		$this->titre_url1=$titre_url1;
		$this->description_url1=$description_url1;
		$this->height_url1=$height_url1;
		$this->width_url1=$width_url1;
		$this->apercu_url2=$apercu_url2;
		$this->titre_url2=$titre_url2;
		$this->description_url2=$description_url2;
		$this->height_url2=$height_url2;
		$this->width_url2=$width_url2;
		$this->apercu_url3=$apercu_url3;
		$this->titre_url3=$titre_url3;
		$this->description_url3=$description_url3;
		$this->height_url3=$height_url3;
		$this->width_url3=$width_url3;
		$this->apercu_url4=$apercu_url4;
		$this->titre_url4=$titre_url4;
		$this->description_url4=$description_url4;
		$this->height_url4=$height_url4;
		$this->width_url4=$width_url4;
		$this->rubrique=$rubrique;
	}
	
	public function makePersistent()
	{		
		parent::makePersistent($this);
	
	}
	
}


?>