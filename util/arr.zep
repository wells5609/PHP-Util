namespace Util;

class Arr
{

	/**
	 * Merges a vector of arrays.
	 *
	 * More performant than using array_merge in a loop.
	 *
	 * @author facebook/libphutil
	 *
	 * @param array $arrays Array of arrays to merge.
	 * @return array Merged arrays.
	 */
	public static function mergev(array arrays) {
		return empty arrays ? [] : call_user_func_array("array_merge", arrays);
	}

	/**
	 * Returns an array of elements that satisfy the given conditions.
	 *
	 * @param array array Array of arrays or objects.
	 * @param array conditions Associative array of keys/properties and values.
	 * @param string operator One of "AND", "OR", or "NOT". Default "AND".
	 * @return array Array elements that satisfy the conditions.
	 */
	public static function select(array arr, array conditions, string operator = "AND") -> array
    {
        var filtered, oper, numCond, key, obj, mKey, mVal;
		int match;

		if unlikely empty conditions {
			return arr;
		}

		let filtered = [];
		let oper = strtoupper(operator);
		let numCond = count(conditions);

		for key, obj in arr {

			let match = 0;

			if typeof obj == "array" {

				for mKey, mVal in conditions {
                    if array_key_exists(mKey, obj) && mVal == obj[mKey] {
						let match++;
					}
				}

			} elseif typeof obj == "object" {

				for mKey, mVal in conditions {
                    if isset obj->mKey && mVal == obj->mKey {
						let match++;
					}
				}
			}

			if ("AND" === oper && match == numCond) || ("OR" === oper && match > 0) || ("NOT" === oper && 0 == match) {
				let filtered[key] = obj;
	        }
		}

		return filtered;
	}

}
