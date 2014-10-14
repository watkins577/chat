<?php

$dataProvider = array_reverse($dataProvider);
foreach ($dataProvider as $data) {
	echo sprintf('%s: %s<br/>', $data['user']['username'], $data['message']);
}