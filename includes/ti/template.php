<?php
/**
 * Simple PHP Template Inheritance
 * 
 * This file provides functions for template inheritance in PHP.
 */

// Store blocks
$GLOBALS['_ti_blocks'] = array();
$GLOBALS['_ti_base'] = null;

/**
 * Start a new block
 * 
 * @param string $name Block name
 */
function startblock($name) {
    ob_start();
    $GLOBALS['_ti_blocks'][$name] = array();
}

/**
 * End a block
 */
function endblock() {
    $content = ob_get_clean();
    $name = array_pop(array_keys($GLOBALS['_ti_blocks']));
    $GLOBALS['_ti_blocks'][$name][] = $content;
}

/**
 * Display a block
 * 
 * @param string $name Block name
 */
function displayblock($name) {
    if (isset($GLOBALS['_ti_blocks'][$name])) {
        echo $GLOBALS['_ti_blocks'][$name][count($GLOBALS['_ti_blocks'][$name]) - 1];
    }
}

/**
 * Check if a block exists
 * 
 * @param string $name Block name
 * @return bool True if block exists, false otherwise
 */
function hasblock($name) {
    return isset($GLOBALS['_ti_blocks'][$name]);
}

/**
 * Set the base template
 * 
 * @param string $path Base template path
 */
function setbase($path) {
    $GLOBALS['_ti_base'] = $path;
}

/**
 * Load the base template
 */
function loadbase() {
    if ($GLOBALS['_ti_base']) {
        include $GLOBALS['_ti_base'];
    }
}
?>