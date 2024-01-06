function testSumAccuracy($match, $iterations = 3000000) {
	 if (!function_exists('bcadd')) {
		return "BC Math functions are not available.";
	 }

	 $discrepancies = [];

	 $p = 10000;
	 for ($i = 0; $i < $iterations; $i++) {
		$decimalPart = strrchr($match, ".");
		$d = $decimalPart ? strlen(substr($decimalPart, 1)) : 0;
		bcscale($d);

		$m = bcmul($match, (string)$p);

		$r1 = bcdiv((string)random_int(0, (int)$m), (string)$p);
		$r2 = bcdiv((string)random_int(0, (int)bcsub($m, bcmul($r1, (string)$p))), (string)$p);
		$r3 = bcdiv((string)random_int(0, (int)bcsub($m, bcmul(bcadd($r1, $r2), (string)$p))), (string)$p);

		$r4 = bcsub($match, bcadd(bcadd($r1, $r2), $r3));

		$r1 = bcadd($r1, '0', $d);
		$r2 = bcadd($r2, '0', $d);
		$r3 = bcadd($r3, '0', $d);
		$r4 = bcadd($r4, '0', $d);

		$sum = bcadd(bcadd(bcadd($r1, $r2), $r3), $r4);
		$parts = array_filter([$r1, $r2, $r3, $r4], function($value) {
			return bccomp($value, '0', $d) !== 0;
		});
		$expression = "(" . implode("+", $parts) . ")";
		// Check if the absolute difference between $sum and $match exceeds the threshold
		if (bccomp($sum, $match) !== 0) {
				$discrepancies[] = [
					 'Iteration' => $i + 1,
					 'Sum' => $sum,
					 'Match' => $match,
				];
		}
	 }

	 return $discrepancies;
}

// Test the sum accuracy and list discrepancies
$match = '12345678901234567890.12345678901234567890'; // Replace with your original number
$discrepancies = testSumAccuracy($match);

foreach ($discrepancies as $discrepancy) {
	 echo "Iteration {$discrepancy['Iteration']}: Sum = {$discrepancy['Sum']}, Match = {$discrepancy['Match']}\n";
}

print_r($discrepancies);

/* Result:
Array
(
)
*/
