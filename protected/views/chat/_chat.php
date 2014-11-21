<?php

$dataProvider = array_reverse($dataProvider);
foreach ($dataProvider as $data) {
	echo sprintf('<b>%s</b>: %s<br/>', $data['user']['username'], $data['message']);
}