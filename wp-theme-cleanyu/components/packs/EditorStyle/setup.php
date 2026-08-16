<?
function a4h_editor_css() {
    add_editor_style('editor-dashboard.css');
}
add_action('after_setup_theme', 'a4h_editor_css');