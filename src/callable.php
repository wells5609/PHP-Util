<?php
/**
 * @package wells5609\PHP-Util
 */

/** ================================
			Callables
================================= */

/**
 * Invokes an callback (if callable) or returns the unmodified argument.
 * 
 * @author FuelPHP
 * 
 * @param wild $var Anything - Executed if Closure or object with __invoke().
 * @return mixed Result of callback if callable, otherwise original value.
 */
function result($var) {
	if ($var instanceof \Closure || method_exists($var, '__invoke')) {
		return $var();
	}
	return $var;
}

/**
 * Invokes a callback using array of arguments.
 * 
 * Uses the Reflection API to invoke an arbitrary callable.
 * Thus, arguments can be named and/or not in the proper 
 * order for calling (they will be correctly ordered).
 * 
 * Use case: url routing, where the order of route variables may
 * create an "unordered" array of callback parameters.
 * 
 * @param callable $callback Callable callback.
 * @param array $args Array of callback parameters.
 * @return mixed Result of callback.
 * @throws LogicException on invalid callable
 * @throws RuntimeException on missing callback param
 */
function invoke($callback, array $args = array()) {
	
	$type = null;
	
	if ($callback instanceof \Closure || is_string($callback)) {
		$refl = new \ReflectionFunction($callback);
		$type = 'func';
	} else if (is_array($callback)) {
		$refl = new \ReflectionMethod($callback[0], $callback[1]);
		$type = 'method';
	} else if (is_object($callback)) {
		$refl = new \ReflectionMethod(get_class($callback), '__invoke');
		$type = 'object';
	} else {
		throw new \LogicException("Unknown callback type, given ".gettype($callback));
	}
	
	$params = array();
	
	foreach($refl->getParameters() as $i => $param) {
		
		$name = $param->getName();
		
		if (isset($args[$name])) {
			$params[$name] = $args[$name];
		} else if (isset($args[$i])) {
			$params[$name] = $args[$i];
		} else if ($param->isDefaultValueAvailable()) {
			$params[$name] = $param->getDefaultValue();
		} else {
			throw new \RuntimeException("Missing parameter '$param'.");
		}
	}
	
	switch($type) {
	
		case 'func' :
			return $refl->invokeArgs($params);
	
		case 'method' :
			return $refl->isStatic() 
				? call_user_func_array($callback, $params) 
				: $refl->invokeArgs($callback[0], $params);
	
		case 'object' :
			return $refl->invokeArgs($callback, $params);
	}
}

/**
 * Returns human-readable identifier for a callable.
 * @param callable $fn Callable.
 * @return string Human-readable callable identifier, or NULL if invalid.
 */
function callable_id($fn) {
	
	switch(gettype($fn)) {
		
		case 'string':
			return $fn;
		
		case 'array':
			list($c, $m) = $fn;
			return is_object($c) ? get_class($c).'->'.$m : $c.'::'.$m;
		
		case 'object':
			if ($fn instanceof \Closure) {
				return 'Closure_'.spl_object_hash($fn);
			}
			return get_class($fn).'::__invoke';
			
		default:
			return null;
	}
}
