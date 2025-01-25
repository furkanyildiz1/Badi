<?php 
include 'header.php';

try {
    // Diğer istatistik sorguları...
    $aylikSor = $db->prepare("SELECT DATE(ziyaret_tarih) as tarih, COUNT(*) as sayi FROM site_ziyaret WHERE ziyaret_tarih >= DATE_SUB(CURDATE(), INTERVAL 30 DAY) GROUP BY DATE(ziyaret_tarih) ORDER BY tarih");
    $aylikSor->execute();
    $aylikData = $aylikSor->fetchAll(PDO::FETCH_ASSOC);

    $yillikSor = $db->prepare("SELECT DATE_FORMAT(ziyaret_tarih, '%Y-%m') as ay, COUNT(*) as sayi FROM site_ziyaret WHERE ziyaret_tarih >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH) GROUP BY DATE_FORMAT(ziyaret_tarih, '%Y-%m') ORDER BY ay");
    $yillikSor->execute();
    $yillikData = $yillikSor->fetchAll(PDO::FETCH_ASSOC);

    // Cihaz, tarayıcı ve işletim sistemi dağılımları
    $cihazSor = $db->prepare("SELECT ziyaret_cihaz, COUNT(*) as sayi FROM site_ziyaret GROUP BY ziyaret_cihaz");
    $cihazSor->execute();
    $cihazData = $cihazSor->fetchAll(PDO::FETCH_ASSOC);

    $tarayiciSor = $db->prepare("SELECT ziyaret_tarayici, COUNT(*) as sayi FROM site_ziyaret GROUP BY ziyaret_tarayici");
    $tarayiciSor->execute();
    $tarayiciData = $tarayiciSor->fetchAll(PDO::FETCH_ASSOC);

    $osSor = $db->prepare("SELECT ziyaret_os, COUNT(*) as sayi FROM site_ziyaret GROUP BY ziyaret_os");
    $osSor->execute();
    $osData = $osSor->fetchAll(PDO::FETCH_ASSOC);

} catch(PDOException $e) {
    error_log("İstatistik sayfası hatası: " . $e->getMessage());
}
?>

<div class="right_col" role="main">
    <div class="container-fluid">
        <div class="page-title">
            <div class="title_left">
                <h3>Site İstatistikleri</h3>
            </div>
        </div>

        <!-- Tüm grafikler ve istatistikler buraya -->
        <div class="row">
            <div class="col-lg-8 col-md-12">
                <div class="x_panel">
                    <div class="x_title">
                        <h2>Son 30 Gün Ziyaretçi İstatistikleri</h2>
                        <div class="clearfix"></div>
                    </div>
                    <div class="x_content">
                        <canvas id="aylikGrafik"></canvas>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-12">
                <div class="x_panel">
                    <div class="x_title">
                        <h2>Cihaz Dağılımı</h2>
                        <div class="clearfix"></div>
                    </div>
                    <div class="x_content">
                        <canvas id="cihazGrafik"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="x_panel">
                    <div class="x_title">
                        <h2>Yıllık Ziyaretçi İstatistikleri</h2>
                        <div class="clearfix"></div>
                    </div>
                    <div class="x_content">
                        <canvas id="yillikGrafik"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="x_panel">
                    <div class="x_title">
                        <h2>Tarayıcı Dağılımı</h2>
                        <div class="clearfix"></div>
                    </div>
                    <div class="x_content">
                        <canvas id="tarayiciGrafik"></canvas>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="x_panel">
                    <div class="x_title">
                        <h2>İşletim Sistemi Dağılımı</h2>
                        <div class="clearfix"></div>
                    </div>
                    <div class="x_content">
                        <canvas id="osGrafik"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js kütüphanesi -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
// Renk paleti
const colors = {
    primary: '#4a69bd',
    secondary: '#6a89cc',
    success: '#2ecc71',
    warning: '#f1c40f',
    danger: '#e74c3c',
    info: '#3498db'
};

// Aylık Ziyaretçi Grafiği
new Chart(document.getElementById('aylikGrafik'), {
    type: 'line',
    data: {
        labels: <?php echo json_encode(array_column($aylikData, 'tarih')); ?>,
        datasets: [{
            label: 'Günlük Ziyaretçi',
            data: <?php echo json_encode(array_column($aylikData, 'sayi')); ?>,
            borderColor: colors.primary,
            backgroundColor: 'rgba(74, 105, 189, 0.1)',
            tension: 0.3,
            fill: true
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'top',
            }
        },
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});

// Cihaz Dağılımı Grafiği
new Chart(document.getElementById('cihazGrafik'), {
    type: 'doughnut',
    data: {
        labels: <?php echo json_encode(array_column($cihazData, 'ziyaret_cihaz')); ?>,
        datasets: [{
            data: <?php echo json_encode(array_column($cihazData, 'sayi')); ?>,
            backgroundColor: [colors.primary, colors.secondary],
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom',
            }
        }
    }
});

// Yıllık Ziyaretçi Grafiği
new Chart(document.getElementById('yillikGrafik'), {
    type: 'bar',
    data: {
        labels: <?php echo json_encode(array_column($yillikData, 'ay')); ?>,
        datasets: [{
            label: 'Aylık Ziyaretçi',
            data: <?php echo json_encode(array_column($yillikData, 'sayi')); ?>,
            backgroundColor: colors.info,
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'top',
            }
        },
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});

// Tarayıcı Dağılımı Grafiği
new Chart(document.getElementById('tarayiciGrafik'), {
    type: 'bar',
    data: {
        labels: <?php echo json_encode(array_column($tarayiciData, 'ziyaret_tarayici')); ?>,
        datasets: [{
            label: 'Kullanım Sayısı',
            data: <?php echo json_encode(array_column($tarayiciData, 'sayi')); ?>,
            backgroundColor: colors.success,
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'top',
            }
        },
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});

// İşletim Sistemi Dağılımı Grafiği
new Chart(document.getElementById('osGrafik'), {
    type: 'pie',
    data: {
        labels: <?php echo json_encode(array_column($osData, 'ziyaret_os')); ?>,
        datasets: [{
            data: <?php echo json_encode(array_column($osData, 'sayi')); ?>,
            backgroundColor: [colors.primary, colors.success, colors.warning, colors.danger, colors.info],
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom',
            }
        }
    }
});
</script>

<?php include 'footer.php'; ?> 