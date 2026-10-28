<?php

// https://codemia.io/knowledge-hub/path/topological_sorting_in_php

function topoSort(array $nodes, array $edges): array {
    $adj = [];
    $indegree = [];

    foreach ($nodes as $node) {
        $adj[$node] = [];
        $indegree[$node] = 0;
    }

    foreach ($edges as [$from, $to]) {
        $adj[$from][] = $to;
        $indegree[$to]++;
    }

    $queue = new SplQueue();
    foreach ($indegree as $node => $deg) {
        if ($deg === 0) {
            $queue->enqueue($node);
        }
    }

    $order = [];
    while (!$queue->isEmpty()) {
        $node = $queue->dequeue();
        $order[] = $node;

        foreach ($adj[$node] as $next) {
            $indegree[$next]--;
            if ($indegree[$next] === 0) {
                $queue->enqueue($next);
            }
        }
    }

    if (count($order) !== count($nodes)) {
        throw new RuntimeException('Cycle detected. Topological sort not possible.');
    }

    return $order;
}

/*
$nodes = ['compile', 'test', 'clean', 'package', 'deploy'];
$edges = [
    ['clean', 'compile'],
    ['compile', 'test'],
    ['test', 'package'],
    ['package', 'deploy'],
];

print_r(topoSort($nodes, $edges));
*/
