<?php

function getFooter() {
	require_once __DIR__ . '/templates/footer.phtml';
}

function getHeader() {
	require_once __DIR__ . '/templates/header.phtml';
}

function url($params, $get = array()) {
	$params = array_filter($params);
	return implode('/', $params) . ($get ? '?' . http_build_query($get) : null);
}

function getFilters($params) {
	require_once __DIR__ . '/templates/filters.phtml';
}