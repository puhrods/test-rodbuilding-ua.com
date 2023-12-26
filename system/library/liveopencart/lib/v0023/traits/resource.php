<?php
namespace liveopencart\lib\v0023\traits;
trait resource {
	
	protected $resource_prefix_catalog = 'catalog/';
	protected $resource_route_admin = '';
	protected $resource_route_catalog = '';
	
	protected function getResourceFullPath($path) {
		return DIR_APPLICATION.$path;
	}
	
	protected function getResourceFullPathCatalog($path) {
		return DIR_APPLICATION.$this->getResourcePathCatalog($path);
	}
	
	protected function getResourceThemeDirRelatedPathByFullPath($full_path) {
		if ( substr($full_path, 0, strlen(DIR_TEMPLATE)) == DIR_TEMPLATE ) {
			return substr($full_path, strlen(DIR_TEMPLATE));
		} else {
			return $full_path;
		}
		
	}
	
	protected function getResourcePathCatalog($path) {
		return $this->resource_route_catalog.$path;
	}
	
	protected function resourceExists($path) {
		return file_exists( $this->getResourceFullPath($path) );
	}
	
	//protected function resourceExistsCatalog($path) {
	//	return file_exists( $this->getResourceFullPathCatalog($path) );
	//}
	
	public function getResourceLinkWithVersion($path, $prefix='') {
		// for catalog $prefix should be set to 'catalog/';
		return $prefix.$path.'?v='.filemtime( $this->getResourceFullPath($path) );
	}
	
	public function getResourceLinkWithVersionIfExists($path, $prefix='') {
		// for catalog $prefix should be set to 'catalog/';
		if ( $this->resourceExists($path) ) {
		//if ( $this->resourceExists($prefix.$path) ) {
			return $this->getResourceLinkWithVersion($path, $prefix);
		}
	}
	
	protected function getResourceLinkWithVersionCatalog($path) {
		// for catalog $prefix should be set to 'catalog/'; 
		return $this->getResourceLinkWithVersion($path, $this->resource_prefix_catalog);
	}
	
	protected function getResourceLinkWithVersionIfExistsCatalog($path) {
		// for catalog $prefix should be set to 'catalog/'; 
		return $this->getResourceLinkWithVersionIfExists($path, $this->resource_prefix_catalog);
	}
	
	protected function addDocumentScriptByPathIfExists($path, $prefix='') {
		if ( $this->resourceExists($path) ) {
			$this->document->addScript( $this->getResourceLinkWithVersion($path, $prefix) );
		}
	}
	
	protected function addDocumentScriptByPathIfExistsCatalog($path) {
		$this->addDocumentScriptByPathIfExists($path, $this->resource_prefix_catalog);
	}
	
	protected function addDocumentStyleByPathIfExists($path, $prefix='') {
		if ( $this->resourceExists($path) ) {
			$this->document->addStyle( $this->getResourceLinkWithVersion($path, $prefix) );
		}
	}
	
	protected function addDocumentStyleByPathIfExistsCatalog($path) {
		$this->addDocumentStyleByPathIfExists($path, $this->resource_prefix_catalog);
	}
	
	protected function addScripts($scripts_links) {
		foreach ( $scripts_links as $script_link ) {
			$this->document->addScript( $script_link );
		}
	}
	
	protected function addStyles($links) {
		foreach ( $links as $link ) {
			$this->document->addStyle( $link );
		}
	}
	
	protected function getLinksForResources($scripts, $prefix='') {
		$results = [];
		foreach ( $scripts as $script ) {
			$results[] = $this->getResourceLinkWithVersion($script, $prefix);
		}
		return $results;
	}
	
	protected function getLinksForResourcesCatalog($scripts) {
		$this->getLinksForResources($scripts, $this->resource_prefix_catalog);
	}
	
	protected function getResourceLocalFilePath($basic_path) {
		if ( $this->inAdminSection() ) { // admin section
			return DIR_CATALOG.$basic_path;
		} else { // customer section
			return DIR_APPLICATION.$basic_path;
		}
	}
	
	public function getCatalogResourceLinkPathWithVersion($basic_path) {
		
		$file_path = $this->getResourceLocalFilePath($basic_path);
		if ( $this->inAdminSection() ) {
			$script_path = HTTP_CATALOG.$this->resource_prefix_catalog.$basic_path;
			$remove_prefixes = array('http:', 'https:');
			foreach ( $remove_prefixes as $remove_prefix ) {
				if ( strpos($script_path, $remove_prefix) === 0 ) {
					$script_path = substr($script_path, strlen($remove_prefix));
				}
			}
		} else {
			$script_path = $this->getResourceLinkWithVersionCatalog($basic_path);
			//$script_path = $this->resource_prefix_catalog.$basic_path;
		}
		
		$modified = filemtime( $file_path );
		
		return $script_path.'?v='.$modified; 
	}
	
}