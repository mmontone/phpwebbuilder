<?

function qsort(& $a, $f) {
	echo 'Trying to sort: ' . print_r($a,true) . '<br/>';
    qsort_do(& $a, 0, Count($a) - 1, $f);
}

function qsort_do($a, $l, $r, $f) {
	if ($l < $r) {
		qsort_partition(& $a, $l, $r, & $lp, & $rp, $f);
		qsort_do(& $a, $l, $lp, $f);
		qsort_do(& $a, $rp, $r, $f);
	}
}

function qsort_partition($a, $l, $r, $lp, $rp, $f) {
	$i = $l +1;
	$j = $l +1;
	while ($j <= $r) {
		if ($f ($a[$j], $a[$l])) {
			$tmp = $a[$j];
			$a[$j] = $a[$i];
			$a[$i] = $tmp;
			$i++;
		}
		$j++;
	}
	$x = $a[$l];
	$a[$l] = $a[$i -1];
	$a[$i -1] = $x;
	$lp = $i -2;
	$rp = $i;
}

?>