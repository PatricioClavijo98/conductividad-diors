<?php
/**
 * Plugin Name: Cálculo de Conductores para Elementor
 * Description: Widget de Elementor para calcular la sección mínima de conductores eléctricos.
 * Version: 1.0.0
 * Author: Patricio Clavijo
 */

if (!defined('ABSPATH')) {
    exit; // Salir si se accede directamente
}

// Verificar si Elementor está activo antes de registrar el widget
function check_elementor_loaded() {
    if (!did_action('elementor/loaded')) {
        add_action('admin_notices', function() {
            echo '<div class="notice notice-error"><p><strong>Error:</strong> El plugin <strong>Cálculo de Conductores</strong> requiere Elementor para funcionar.</p></div>';
        });
        deactivate_plugins(plugin_basename(__FILE__));
        return false;
    }
    return true;
}
add_action('plugins_loaded', 'check_elementor_loaded');

// Registrar el widget en Elementor cuando Elementor esté disponible
function register_conductor_calc_widget($widgets_manager) {
    require_once plugin_dir_path(__FILE__) . 'conductor-widget.php';
    $widgets_manager->register(new Elementor_Conductor_Calc_Widget());
}
add_action('elementor/widgets/register', 'register_conductor_calc_widget');

// Incluir scripts y estilos (si los necesitas en el futuro)
function conductor_calc_enqueue_scripts() {
    wp_enqueue_script('conductor-calc-js', plugins_url('/assets/js/conductor-calc.js', __FILE__), array('jquery'), null, true);
    wp_enqueue_style('conductor-calc-css', plugins_url('/assets/css/conductor-calc.css', __FILE__));
}
add_action('wp_enqueue_scripts', 'conductor_calc_enqueue_scripts');

// Cargar datos desde JSON
function load_conductor_data() {
    $json_file = plugin_dir_path(__FILE__) . 'data/conductor_data.json';
    if (file_exists($json_file)) {
        $json_data = file_get_contents($json_file);
        return json_decode($json_data, true);
    }
    return [];
}
