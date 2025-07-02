<?php
if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists('\Elementor\Widget_Base')) {
    return;
}

class Elementor_Conductor_Calc_Widget extends \Elementor\Widget_Base {
    public function get_name() {
        return 'conductor_calc';
    }

    public function get_title() {
        return __('Cálculo de Conductores', 'plugin-name');
    }

    public function get_icon() {
        return 'eicon-calculator';
    }

    public function get_categories() {
        return ['general'];
    }

    protected function render() {
        ?>
        <style>
    #conductor-calc-form {
        max-width: 600px;
        background: #fff;
        padding: 20px 25px;
        border-radius: 12px;
        box-shadow: 0 0 15px rgba(0,0,0,0.1);
        font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
    }

    #conductor-calc-form label {
        display: block;
        margin-top: 15px;
        font-weight: 600;
        color: #333;
    }

    #conductor-calc-form select,
    #conductor-calc-form input {
        width: 100%;
        padding: 10px;
        margin-top: 5px;
        border: 1px solid #ccc;
        border-radius: 8px;
        font-size: 14px;
        background-color: #f9f9f9;
        transition: border-color 0.3s;
    }

    #conductor-calc-form select:focus,
    #conductor-calc-form input:focus {
        outline: none;
        border-color: #007cba;
        background-color: #fff;
    }

    #conductor-calc-form button {
        margin-top: 20px;
        background-color: #007cba;
        color: #fff;
        border: none;
        padding: 12px 20px;
        font-size: 16px;
        border-radius: 8px;
        cursor: pointer;
        transition: background-color 0.3s;
    }

    #conductor-calc-form button:hover {
        background-color: #005fa3;
    }

    #conductor-result {
        max-width: 600px;
        margin-top: 30px;
        padding: 20px;
        background: #f1f5f9;
        border-left: 5px solid #007cba;
        border-radius: 8px;
        font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
    }

    #conductor-result h3,
    #conductor-result h4 {
        margin-top: 0;
        color: #1e293b;
    }

    #conductor-result p {
        margin: 10px 0;
        color: #334155;
    }

    #conductor-result ul {
        margin-top: 10px;
        padding-left: 20px;
    }

    #conductor-result li {
        margin-bottom: 5px;
        color: #1f2937;
    }

    @media (max-width: 640px) {
        #conductor-calc-form, #conductor-result {
            padding: 15px;
        }
    }
</style>
        <form id="conductor-calc-form">
            <label for="voltage">Tensión Nominal (V):</label>
            <select id="voltage" name="voltage">
                <option value="110">110V</option>
                <option value="230" selected>230V</option>
                <option value="400">400V</option>
            </select>

            <label for="connection_type">Tipo de Conexión:</label>
            <select id="connection_type" name="connection_type">
                <option value="monofasica">Monofásica</option>
                <option value="trifasica" selected>Trifásica</option>
            </select>

            <label for="load">Carga:</label>
            <input type="number" id="load" name="load" value="10" step="any">

            <label for="load_unit">Unidad de Carga:</label>
            <select id="load_unit" name="load_unit">
                <option value="A">Amperios</option>
                <option value="kW" selected>Kilovatios</option>
                <option value="CV">Caballos de Vapor</option>
                <option value="HP">Horsepower</option>
            </select>

            <label for="conductor_type">Tipo de Conductor:</label>
            <select id="conductor_type" name="conductor_type">
                <option value="cobre" selected>Cobre</option>
                <option value="aluminio">Aluminio</option>
            </select>

            <label for="pole_count">Cantidad de Polos:</label>
            <select id="pole_count" name="pole_count">
                <option value="1">1 Polo</option>
                <option value="2">2 Polos</option>
                <option value="3" selected>3 Polos</option>
                <option value="4">4 Polos</option>
                <option value="5">5 Polos</option>
            </select>

            <label for="installation_type">Tipo de Instalación:</label>
            <select id="installation_type" name="installation_type">
                <option value="aire" selected>Aire</option>
                <option value="canio">Caño</option>
                <option value="enterrado">Enterrado</option>
            </select>

            <label for="usage_type">Tipo de Uso:</label>
            <select id="usage_type" name="usage_type">
                <option value="alumbrado">Alumbrado (3%)</option>
                <option value="otros" selected>Otros (5%)</option>
            </select>

            <label for="line_length">Longitud de la Línea (m):</label>
            <input type="number" id="line_length" name="line_length" value="30" step="any">

            <label for="conductor_temperature">Temperatura de los Conductores (°C):</label>
            <input type="number" id="conductor_temperature" name="conductor_temperature" value="20">

            <button type="submit">Calcular</button>
        </form>

        <div id="conductor-result"></div>

        <script>
        document.getElementById('conductor-calc-form').addEventListener('submit', async function(event) {
            event.preventDefault();

            const voltage = parseFloat(document.getElementById('voltage').value);
            const connectionType = document.getElementById('connection_type').value;
            const load = parseFloat(document.getElementById('load').value);
            const loadUnit = document.getElementById('load_unit').value;
            const conductorType = document.getElementById('conductor_type').value;
            const poleCount = parseInt(document.getElementById('pole_count').value);
            const installationType = document.getElementById('installation_type').value;
            const usageType = document.getElementById('usage_type').value;
            const lineLength = parseFloat(document.getElementById('line_length').value);
            const temperature = parseFloat(document.getElementById('conductor_temperature').value);

            let powerWatts = 0;
            let currentAmps = 0;
            const sqrt3 = Math.sqrt(3);

            if (loadUnit === 'A') {
                currentAmps = load;
                powerWatts = connectionType === 'trifasica' ? sqrt3 * voltage * currentAmps : voltage * currentAmps;
            } else {
                if (loadUnit === 'kW') powerWatts = load * 1000;
                if (loadUnit === 'HP') powerWatts = load * 746;
                if (loadUnit === 'CV') powerWatts = load * 735.5;
                currentAmps = connectionType === 'trifasica' ? powerWatts / (sqrt3 * voltage) : powerWatts / voltage;
            }

            const e = usageType === 'alumbrado' ? 0.03 * voltage : 0.05 * voltage;
            const K = conductorType === 'cobre' ? 56.9 : 34.7;

            let S = 0;
            if (connectionType === 'trifasica') {
                S = (lineLength * powerWatts) / (K * e * voltage);
            } else {
                S = (2 * lineLength * powerWatts) / (K * e * voltage);
            }

            const resistivityBase = conductorType === 'cobre' ? 0.0172 : 0.02826;
            const resistivityNew = resistivityBase * (1 + (0.004 * (temperature - 20)));
            const conductivity = 1 / resistivityNew;

            const response = await fetch('<?php echo plugins_url("/data/conductor_data_v2.json", __FILE__); ?>');
            const data = await response.json();
            const conductors = data.materials[conductorType].conductors;
            
            console.log(conductors);
            
            const validConductors = conductors.filter(c =>
                c.section >= S &&
                c.max_current[installationType] != null &&
                c.max_current[installationType] >= currentAmps &&
                c.num_conductors == poleCount
            );

            let html = `<h3>Resultados</h3>
                <p><strong>Corriente Nominal:</strong> ${currentAmps.toFixed(2)} A</p>
                <p><strong>Resistividad (${conductorType}) a ${temperature}°C:</strong> ${resistivityNew.toFixed(6)} Ω·mm²/m</p>
                <p><strong>Conductividad:</strong> ${conductivity.toFixed(6)} S/m</p>
                <p><strong>Sección mínima requerida:</strong> ${S.toFixed(2)} mm²</p>
                <h4>Conductores compatibles:</h4>`;

            if (validConductors.length === 0) {
                html += `<p style="color: red;">No se encontraron conductores compatibles con ${poleCount} polos para instalación en "${installationType === 'canio' ? 'caño' : installationType}".</p>`;
            } else {
                html += '<ul>' + validConductors.map(c => `<li>${c.type} - ${c.num_conductors}x${c.section} mm² (Máx: ${c.max_current[installationType]} A, instalación: ${installationType === 'canio' ? 'caño' : installationType})</li>`).join('') + '</ul>';
            }

            document.getElementById('conductor-result').innerHTML = html;
        });
        </script>
        <?php
    }
}
