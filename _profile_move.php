<?php

if (require_get("move_up", false)) {
	// current graph
	$graph_id = require_get("move_up");
	$q = db()->prepare("SELECT graphs.* FROM graphs
		JOIN graph_pages ON graphs.page_id=graph_pages.id
		WHERE graph_pages.user_id=? AND graphs.id=?");
	$q->execute(array(user_id(), $graph_id));
	$graph = $q->fetch();

	if ($graph) {
		// previous graph - select the one with the highest page_order under this one
		$q = db()->prepare("SELECT graphs.* FROM graphs
			JOIN graph_pages ON graphs.page_id=graph_pages.id
			WHERE graph_pages.user_id=? AND graphs.page_id=? AND graphs.page_order < ?
			ORDER BY graphs.page_order DESC LIMIT 1");
		$q->execute(array(user_id(), $graph['page_id'], $graph['page_order']));
		$previous = $q->fetch();

		if ($previous) {
			// swap orders
			$q = db()->prepare("UPDATE graphs SET page_order=? WHERE id=? LIMIT 1");
			$q->execute(array($previous['page_order'], $graph['id']));
			$q = db()->prepare("UPDATE graphs SET page_order=? WHERE id=? LIMIT 1");
			$q->execute(array($graph['page_order'], $previous['id']));

			if (!require_get('undo', false))
				$messages[] = "Graph moved [<a href=\"" . htmlspecialchars(url_for('profile', array('page' => $graph['page_id'], 'move_down' => $graph['id'], 'undo' => 1))) . "\">undo</a>]";
		} else {
			// nothing to do
		}
	} else {
		throw new Exception("Could not find a graph ID " . htmlspecialchars($graph_id));
	}

}

if (require_get("move_down", false)) {
	// current graph
	$graph_id = require_get("move_down");
	$q = db()->prepare("SELECT graphs.* FROM graphs
		JOIN graph_pages ON graphs.page_id=graph_pages.id
		WHERE graph_pages.user_id=? AND graphs.id=?");
	$q->execute(array(user_id(), $graph_id));
	$graph = $q->fetch();

	if ($graph) {
		// previous graph - select the one with the lowest page_order above this one
		$q = db()->prepare("SELECT graphs.* FROM graphs
			JOIN graph_pages ON graphs.page_id=graph_pages.id
			WHERE graph_pages.user_id=? AND graphs.page_id=? AND graphs.page_order > ?
			ORDER BY graphs.page_order ASC LIMIT 1");
		$q->execute(array(user_id(), $graph['page_id'], $graph['page_order']));
		$previous = $q->fetch();

		if ($previous) {
			// swap orders
			$q = db()->prepare("UPDATE graphs SET page_order=? WHERE id=? LIMIT 1");
			$q->execute(array($previous['page_order'], $graph['id']));
			$q = db()->prepare("UPDATE graphs SET page_order=? WHERE id=? LIMIT 1");
			$q->execute(array($graph['page_order'], $previous['id']));

			if (!require_get('undo', false))
				$messages[] = "Graph moved [<a href=\"" . htmlspecialchars(url_for('profile', array('page' => $graph['page_id'], 'move_up' => $graph['id'], 'undo' => 1))) . "\">undo</a>]";
		} else {
			// nothing to do
		}
	} else {
		throw new Exception("Could not find a graph ID " . htmlspecialchars($graph_id));
	}

}

if (require_get("remove", false)) {
	// current graph
	$graph_id = require_get("remove");
	$q = db()->prepare("SELECT graphs.* FROM graphs
		JOIN graph_pages ON graphs.page_id=graph_pages.id
		WHERE graph_pages.user_id=? AND graphs.id=?");
	$q->execute(array(user_id(), $graph_id));
	$graph = $q->fetch();

	if ($graph) {
		// just remove it
		$q = db()->prepare("UPDATE graphs SET is_removed=1 WHERE id=? LIMIT 1");
		$q->execute(array($graph['id']));

		if (!require_get('undo', false))
			$messages[] = "Graph removed [<a href=\"" . htmlspecialchars(url_for('profile', array('page' => $graph['page_id'], 'restore' => $graph['id'], 'undo' => 1))) . "\">undo</a>]";
	} else {
		throw new Exception("Could not find a graph ID " . htmlspecialchars($graph_id));
	}

}

if (require_get("restore", false)) {
	// current graph
	$graph_id = require_get("restore");
	$q = db()->prepare("SELECT graphs.* FROM graphs
		JOIN graph_pages ON graphs.page_id=graph_pages.id
		WHERE graph_pages.user_id=? AND graphs.id=?");
	$q->execute(array(user_id(), $graph_id));
	$graph = $q->fetch();

	if ($graph) {
		// just remove it
		$q = db()->prepare("UPDATE graphs SET is_removed=0 WHERE id=? LIMIT 1");
		$q->execute(array($graph['id']));

		if (!require_get('undo', false))
			$messages[] = "Graph restored [<a href=\"" . htmlspecialchars(url_for('profile', array('page' => $graph['page_id'], 'remove' => $graph['id'], 'undo' => 1))) . "\">undo</a>]";
	} else {
		throw new Exception("Could not find a graph ID " . htmlspecialchars($graph_id));
	}

}