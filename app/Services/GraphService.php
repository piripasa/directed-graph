<?php
/**
 * Created by PhpStorm.
 * User: piripasa
 * Date: 21/6/18
 * Time: 3:59 AM
 */

namespace App\Services;


class GraphService {
    
    protected $graph;
    
    protected $distance;
    
    protected $previous;
    
    protected $queue;
    
    public function __construct($graph) {
        $this->graph = $graph;
    }

    /**
     * Process the next (i.e. closest) entry in the queue
     *
     * @param string[] $exclude A list of nodes to exclude - for calculating next-shortest paths.
     *
     * @return void
     */
    protected function processNextNodeInQueue(array $exclude) {
        // Process the closest vertex
        $closest = array_search(min($this->queue), $this->queue);
        if (!empty($this->graph[$closest]) && !in_array($closest, $exclude)) {
            foreach ($this->graph[$closest] as $neighbor => $cost) {
                if (isset($this->distance[$neighbor])) {
                    if ($this->distance[$closest] + $cost < $this->distance[$neighbor]) {
                        // A shorter path was found
                        $this->distance[$neighbor] = $this->distance[$closest] + $cost;
                        $this->previous[$neighbor] = [$closest];
                        $this->queue[$neighbor]    = $this->distance[$neighbor];
                    } elseif ($this->distance[$closest] + $cost === $this->distance[$neighbor]) {
                        // An equally short path was found
                        $this->previous[$neighbor][] = $closest;
                        $this->queue[$neighbor]      = $this->distance[$neighbor];
                    }
                }
            }
        }
        unset($this->queue[$closest]);
    }

    /**
     * Extract all the paths from $source to $target as arrays of nodes.
     *
     * @param string $target The starting node (working backwards)
     *
     * @return string[][] One or more shortest paths, each represented by a list of nodes
     */
    protected function extractPaths($target) {
        $paths = [[$target]];

        while (current($paths) !== false) {
            $key  = key($paths);
            $path = current($paths);
            next($paths);

            if (!empty($this->previous[$path[0]])) {
                foreach ($this->previous[$path[0]] as $previous) {
                    $copy = $path;
                    array_unshift($copy, $previous);
                    $paths[] = $copy;
                }
                unset($paths[$key]);
            }
        }

        return array_values($paths);
    }

    /**
     * Calculate the shortest path through a a graph, from $source to $target.
     *
     * @param string   $source  The starting node
     * @param string   $target  The ending node
     * @param string[] $exclude A list of nodes to exclude - for calculating next-shortest paths.
     *
     * @return string[][] Zero or more shortest paths, each represented by a list of nodes
     */
    public function shortestPaths($source, $target, array $exclude = []) {
        // The shortest distance to all nodes starts with infinity...
        $this->distance = array_fill_keys(array_keys($this->graph), INF);
        // ...except the start node
        $this->distance[$source] = 0;

        // The previously visited nodes
        $this->previous = array_fill_keys(array_keys($this->graph), []);

        // Process all nodes in order
        $this->queue = [$source => 0];
        while (!empty($this->queue)) {
            $this->processNextNodeInQueue($exclude);
        }

        if ($source === $target) {
            // A null path
            return [[$source]];
        } elseif (empty($this->previous[$target])) {
            // No path between $source and $target
            return [];
        } else {
            // One or more paths were found between $source and $target
            return $this->extractPaths($target);
        }
    }
}