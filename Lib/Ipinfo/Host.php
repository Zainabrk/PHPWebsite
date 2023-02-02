<?php

#namespace DavidePastore\Ipinfo_Ipinfo;

/**
 * Represent an host with all the details.
 * @author davidepastore 
 *
 */
class Ipinfo_Host {
	
	/**
	 * Contains all the properties of the host.
	 * @var array
	 */
	protected $properties;
	
	/**
	 * Create an Host object with all the properties.
	 * @param unknown $properties
	 */
	public function __construct($properties = array()){
		if (!$properties) {
			$properties = array();
		}
		//Merge default values
		$this->properties = array_merge(array(
				Ipinfo_Ipinfo::CITY => "",
				Ipinfo_Ipinfo::COUNTRY => "",
				Ipinfo_Ipinfo::HOSTNAME => "",
				Ipinfo_Ipinfo::IP => "",
				Ipinfo_Ipinfo::LOC => "",
				Ipinfo_Ipinfo::ORG => "",
				Ipinfo_Ipinfo::PHONE => "",
				Ipinfo_Ipinfo::POSTAL => "",
				Ipinfo_Ipinfo::REGION => ""
		), $properties);
	}
	
	/**
	 * Get the city value.
	 */
	public function getCity(){
		return $this->properties[Ipinfo::CITY];
	}
	
	/**
	 * Get the country value.
	 */
	public function getCountry(){
		return $this->properties[Ipinfo_Ipinfo::COUNTRY];
	}
	
	/**
	 * Get the hostname value.
	 */
	public function getHostname(){
		return $this->properties[Ipinfo_Ipinfo::HOSTNAME];
	}

	/**
	 * Get the ip value.
	 */
	public function getIp(){
		return $this->properties[Ipinfo_Ipinfo::IP];
	}
	
	/**
	 * Get the loc value.
	 */
	public function getLoc(){
		return $this->properties[Ipinfo_Ipinfo::LOC];
	}

	/**
	 * Get the org value.
	 */
	public function getOrg(){
		return $this->properties[Ipinfo_Ipinfo::ORG];
	}
	
	/**
	 * Get the phone value.
	 */
	public function getPhone(){
		return $this->properties[Ipinfo_Ipinfo::PHONE];
	}
    
    /**
	 * Get the postal value.
	 */
	public function getPostal(){
		return $this->properties[Ipinfo_Ipinfo::POSTAL];
	}

	/**
	 * Get the region value.
	 */
	public function getRegion(){
		return $this->properties[Ipinfo_Ipinfo::REGION];
	}
	
	/**
	 * Get all the properties.
	 * @return array An associative array with all the properties.
	 */
	public function getProperties(){
		return $this->properties;
	}
}