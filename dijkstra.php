<?php
								
// https://www.programmingalgorithms.com/algorithm/dijkstra's-algorithm/php/

$INT_MAX = 0x7FFFFFFF;

function MinimumDistance($distance, $shortestPathTreeSet, $verticesCount)
{
	global $INT_MAX;
	$min = $INT_MAX;
	$minIndex = 0;

	for ($v = 0; $v < $verticesCount; ++$v)
	{
		if ($shortestPathTreeSet[$v] == false && $distance[$v] <= $min)
		{
			$min = $distance[$v];
			$minIndex = $v;
		}
	}

	return $minIndex;
}

function PrintResult($distance, $verticesCount)
{
	echo "Vertex    Distance from source" . "\n";

	for ($i = 0; $i < $verticesCount; ++$i)
		echo $i . "\t  " . $distance[$i] . "\n";
}

function Dijkstra($graph, $source, $verticesCount)
{
	global $INT_MAX;
	$distance = array();
	$shortestPathTreeSet = array();

	for ($i = 0; $i < $verticesCount; ++$i)
	{
		$distance[$i] = $INT_MAX;
		$shortestPathTreeSet[$i] = false;
	}

	$distance[$source] = 0;

	for ($count = 0; $count < $verticesCount - 1; ++$count)
	{
		$u = MinimumDistance($distance, $shortestPathTreeSet, $verticesCount);
		$shortestPathTreeSet[$u] = true;

		for ($v = 0; $v < $verticesCount; ++$v)
			if (!$shortestPathTreeSet[$v] && $graph[$u][$v] && $distance[$u] != $INT_MAX && $distance[$u] + $graph[$u][$v] < $distance[$v])
				$distance[$v] = $distance[$u] + $graph[$u][$v];
	}

	//PrintResult($distance, $verticesCount);

	return $distance;
}

/*
$graph = array(
	array(0, 4, 0, 0, 0, 0, 0, 8, 0),
	array(4, 0, 8, 0, 0, 0, 0, 11, 0),
	array(0, 8, 0, 7, 0, 4, 0, 0, 2),
	array(0, 0, 7, 0, 9, 14, 0, 0, 0),
	array(0, 0, 0, 9, 0, 10, 0, 0, 0),
	array(0, 0, 4, 0, 10, 0, 2, 0, 0),
	array(0, 0, 0, 14, 0, 2, 0, 1, 6),
	array(8, 11, 0, 0, 0, 0, 1, 0, 7),
	array(0, 0, 2, 0, 0, 0, 6, 7, 0)
);

Dijkstra($graph, 0, 9);
*/

?>
