<?php
// 🔥 afficher erreurs
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>

<?php include(__DIR__ . "/includes/header.php"); ?>
<?php include(__DIR__ . "/includes/sidebar.php"); ?>

<div class="main">

    <h1>Dashboard</h1>

    <!-- 🔷 CARDS -->
    <div class="cards">
        <div class="card green">
            € 42,500<br>
            <span>Chiffre d'affaires</span>
        </div>

        <div class="card blue">
            320<br>
            <span>Utilisateurs</span>
        </div>

        <div class="card orange">
            145<br>
            <span>Produits</span>
        </div>

        <div class="card red">
            8<br>
            <span>Alertes Stock</span>
        </div>
    </div>

    <!-- 📊 + 📦 EN LIGNE -->
    <div class="row">

        <!-- 📊 GRAPH -->
        <div class="left">
            <div class="section">
                <h2>Ventes Mensuelles</h2>
                <canvas id="salesChart"></canvas>
            </div>
        </div>

        <!-- 📦 DROITE -->
        <div class="right">

            <!-- PRODUITS EN ALERTE -->
            <div class="section">
                <h2>Produits en Alerte</h2>
                <table>
                    <tr>
                        <th>Nom</th>
                        <th>Stock</th>
                        <th>Fournisseur</th>
                    </tr>
                    <tr>
                        <td>Souris</td>
                        <td style="color:red;">2</td>
                        <td>Logitech</td>
                    </tr>
                    <tr>
                        <td>Carte SD</td>
                        <td style="color:orange;">5</td>
                        <td>Kingston</td>
                    </tr>
                    <tr>
                        <td>Stylos</td>
                        <td>8</td>
                        <td>Bic</td>
                    </tr>
                </table>
            </div>

            <!-- STOCK FAIBLE -->
            <div class="box red-box">
                <h3>Stock Faible</h3>
                <p>8 produits en rupture</p>
            </div>

        </div>

    </div>

    <!-- 📄 TABLE -->
    <div class="section">
        <h2>Ventes Récentes</h2>

        <table>
            <tr>
                <th>Client</th>
                <th>Date</th>
                <th>Total</th>
            </tr>

            <tr>
                <td>Ali</td>
                <td>23/04/2024</td>
                <td>210€</td>
            </tr>

            <tr>
                <td>Sara</td>
                <td>22/04/2024</td>
                <td>180€</td>
            </tr>

            <tr>
                <td>Mohamed</td>
                <td>21/04/2024</td>
                <td>300€</td>
            </tr>
        </table>
    </div>

</div>

<!-- 📊 SCRIPT CHART -->
<script>
const ctx = document.getElementById('salesChart');

new Chart(ctx, {
    type: 'line',
    data: {
        labels: ['Jan', 'Fev', 'Mar', 'Avr', 'Mai', 'Juin', 'Juil'],
        datasets: [{
            label: 'Ventes (€)',
            data: [1200, 1900, 800, 1500, 2200, 3000, 2700],
            borderColor: '#3b82f6',
            backgroundColor: 'rgba(59,130,246,0.2)',
            fill: true,
            tension: 0.4
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false
    }
});
</script>

<?php include(__DIR__ . "/includes/footer.php"); ?>