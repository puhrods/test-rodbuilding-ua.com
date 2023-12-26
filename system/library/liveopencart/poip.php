<?php
//  Product Option Image PRO / Изображения опций PRO
//  Support: support@liveopencart.com / Поддержка: help@liveopencart.ru

// for comp with old adaptations to themes

namespace liveopencart;
class poip {
	
	public static function initLibrary($registry) {
		
		$registry->set('liveopencart_poip', \liveopencart\ext\poip::getInstance($registry));
		
	}
	
	public static function getLibrary($registry) {
		
		$registry->set('liveopencart_poip', \liveopencart\ext\poip::getInstance($registry));
		return $registry->get('liveopencart_poip');
	
	}
	
}