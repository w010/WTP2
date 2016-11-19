<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2015 wolo <wolo.wolski@gmail.com>
*  All rights reserved
*
*  This script is part of the TYPO3 project. The TYPO3 project is
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
*  (at your option) any later version.
*
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/

namespace WTP\WTools;

/**
 * wolo.pl '.' studio 2015
 * Social plugins
 */



/**
 * borrowed from ext:cal
 * v 0.2 wtp mod
 *
 * point to the registry BEFORE setting value. in other case must make a copy
 * todo: dodac opcje flush registry, wolac na koniec plugina w razie co
 *
 * warning - may misbehave if used two instances of this plugin on one page! check this
 * todo: sprawdzic pluginInstances czy dziala jak trzeba
 *
 * to jest uzyte tylko w defaultAddEdit (we wszystkich 3 extach)
 * wiec przerobic tak, zeby bylo kompatybilne z tamtym w miare mozliwosci
 */
class Registry		{

	// wolo mod:
	// set this on first call in plugin and set pi1 as first and it will set this id. best would be just some random to be sure it's different
	static $pluginInstanceId = 'defaultInstance';

	/**
	 * Usage:
	 *   $myfoo = & Registry('MySpace', 'Foo');
	 *   $myfoo = 'something';
	 *
	 *   $mybar = & Registry('MySpace', 'Bar');
	 *   $mybar = new Something();
	 *
	 * @param  string $namespace A namespace to prevent clashes
	 * @param  string $var       The variable to retrieve.
	 * @param  string $pluginInstanceId	 optional - the idea is to set this on first call when register Pi instance
	 * @return mixed A reference to the variable. If not set it will be null.
	 */
	static function &Cell($namespace, $var, $pluginInstanceId = '') {
		if ($pluginInstanceId)		self::$pluginInstanceId = $pluginInstanceId;
		//debugster($namespace);
		//debugster($var);
		static $instances = [];
		// comment to get case-insensitive namespace
		//$namespace = strtolower($namespace);
		//$var = strtolower($var);
		return $instances[self::$pluginInstanceId][$namespace][$var];
		//return $instances[$namespace][$var];
	}
}


// jesli by to sie nie sprawdzilo, sprobowac to
// http://www.sitecrafting.com/blog/php-patterns-part/
/*class Registry {
	protected $_objects = array();

	function set($name, &$object) {
		$this->_objects[$name] =& $object;
	}

	function &get($name) {
		return $this->_objects[$name];
	}
}*/


?>