<?php

namespace Export2pdf;

require 'lib/helper.php';
require_once 'lib/tcpdf.php';

use \PicoFarad\Router;
use \PicoFarad\Response;
use \PicoFarad\Request;
use \PicoFarad\Session;
use \PicoTools\Template;
use \Model;

function enable() {
    if (!Model\get_plugin_option('export2pdf_enabled')) {
        Model\add_plugin_option('export2pdf_enabled', '1');
    }
}

function disable() {
    Model\remove_plugin_option('export2pdf_enabled', '1');
}

function get_description() {
    return 'export your poched links to PDF.';
}

Router\get_action('export2pdf', function() {

    if (!Model\get_plugin_option('export2pdf_enabled')) {
        Session\flash_error(t('This plugin is disabled.'));
        Response\redirect('?action=config');
    }

    $unread_items = Model\get_items('unread', $_SESSION['user']['id']);
    $bookmarked_items = Model\get_bookmarks($_SESSION['user']['id']);
    $read_items = Model\get_items('read', $_SESSION['user']['id']);

    Response\html(Template\layout('export2pdf/templates/export2pdf', array(
        'unread_items' => $unread_items,
        'bookmarked_items' => $bookmarked_items,
        'read_items' => $read_items,
        'title' => t('Export to PDF')
    )));

});


Router\post_action('export2pdf', function() {

    if (!Model\get_plugin_option('export2pdf_enabled')) {
        Session\flash_error(t('This plugin is disabled.'));
        Response\redirect('?action=config');
    }

    $values = Request\values();
    $items = array();

    foreach ($values as $key => $value) {
        $items[] = Model\get_item($value, $_SESSION['user']['id']);
    }

    export_items_to_pdf($items);
});
