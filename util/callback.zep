namespace Util;

use Closure;
use ReflectionFunction;
use ReflectionMethod;
use InvalidArgumentException;
use RuntimeException;

class Callback
{

	/**
	 * Returns the result of a closure or invokable object, or returns the argument unmodified.
	 *
	 * @author FuelPHP
	 *
	 * @param mixed $var Anything; executed if Closure or invokable object.
	 * @return mixed Result of callback if callable, otherwise original value.
	 */
	public static function result(var arg)
    {

		if typeof arg == "object" && method_exists(arg, "__invoke") {
			return call_user_func(arg);
		}

        return arg;
	}

	/**
	 * Returns a human-readable identifier for a callable.
	 *
	 * @param callable callback Callable.
	 * @return string Human-readable callable identifier, or NULL if invalid.
	 */
	public static function id(var callback) -> string|null
	{

		switch gettype(callback) {

			case "string":
				return callback . "()";

			case "array":

                if typeof callback[0] == "string" {
                    return callback[0] . "::" . callback[1] . "()";
                }

                return get_class(callback[0]) . "->" . callback[1] . "()";

			case "object":

				if (callback instanceof Closure) {
					return "Closure_" . spl_object_hash(callback);
				}

                return get_class(callback) . "::__invoke()";

			default:
				return null;
		}
	}


	/**
	 * Invokes a callback using array of arguments.
	 *
	 * Uses the Reflection API to invoke an arbitrary callable.
	 *
	 * Arguments can be named and/or not in the proper order, as they will be ordered by variable name via reflection.
	 *
	 * Use case: Ordering an array of regex matches from URI routing as callback parameters.
	 *
	 * @param callable $callback Callable callback function.
	 * @param array $args Array of callback parameters.
	 * @return mixed Result of callback function.
	 * @throws \LogicException if given an invalid callable.
	 * @throws \RuntimeException if missing a required callback parameter.
	 */
	public static function invoke(var callback, array! args = [])
	{
        var type, refl, params, idx, param, pName;

		if (typeof callback == "string" || callback instanceof Closure) {
			let refl = new ReflectionFunction(callback);
			let type = "func";

		} elseif typeof callback == "array" {
			let refl = new ReflectionMethod(callback[0], callback[1]);
			let type = "method";

        } elseif typeof callback == "object" {
			let refl = new ReflectionMethod(get_class(callback), "__invoke");
			let type = "object";

		} else {
			throw new InvalidArgumentException("Invalid callback, given: " . gettype(callback));
		}

		let params = [];

		for idx, param in refl->getParameters() {

			let pName = param->getName();

			if isset args[pName] {
				let params[pName] = args[pName];

            } elseif isset args[idx] {
				let params[pName] = args[idx];

            } elseif param->isDefaultValueAvailable() {
				let params[pName] = param->getDefaultValue();

			} else {
				throw new RuntimeException("Missing parameter: '" . pName . "'.");
			}
		}

		switch type {

			case "func" :
				return refl->invokeArgs(params);

			case "method" :
				return refl->isStatic()
					? call_user_func_array(callback, params)
					: refl->invokeArgs(callback[0], params);

			case "object" :
				return refl->invokeArgs(callback, params);
		}
	}

}
