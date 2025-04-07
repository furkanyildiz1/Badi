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

    // Generate array of last 12 months instead of 6
    $last12Months = [];
    for ($i = 11; $i >= 0; $i--) {  // Changed from 5 to 11
        $last12Months[] = date('Y-m', strtotime("-$i months"));
    }

    // Orders Statistics - Changed INTERVAL to 12 MONTH
    $siparislerSor = $db->prepare("
        SELECT 
            DATE_FORMAT(created_at, '%Y-%m') as ay,
            COUNT(*) as toplam_siparis,
            COUNT(CASE WHEN odeme_durumu = 'onaylandi' THEN 1 END) as tamamlanan,
            COUNT(CASE WHEN odeme_durumu = 'beklemede' THEN 1 END) as bekleyen,
            COUNT(CASE WHEN odeme_durumu = 'iptal_edildi' THEN 1 END) as iptal
        FROM faturalar 
        WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)
        GROUP BY DATE_FORMAT(created_at, '%Y-%m')
    ");
    $siparislerSor->execute();
    $siparisData = array_fill_keys($last12Months, ['toplam_siparis' => 0, 'tamamlanan' => 0, 'bekleyen' => 0, 'iptal' => 0]);
    
    foreach($siparislerSor->fetchAll(PDO::FETCH_ASSOC) as $row) {
        if (isset($siparisData[$row['ay']])) {
            $siparisData[$row['ay']] = $row;
        }
    }

    // Course Sales Statistics - Changed INTERVAL to 12 MONTH
    $kursSatisSor = $db->prepare("
        SELECT 
            DATE_FORMAT(f.created_at, '%Y-%m') as ay,
            COUNT(DISTINCT sk.kurs_id) as satis_sayisi
        FROM faturalar f
        LEFT JOIN satilan_kurslar sk ON f.fatura_id = sk.fatura_id
        WHERE f.created_at >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)
        AND f.odeme_durumu = 'onaylandi'
        GROUP BY DATE_FORMAT(f.created_at, '%Y-%m')
    ");
    $kursSatisSor->execute();
    $kursSatisData = array_fill_keys($last12Months, ['satis_sayisi' => 0]);
    
    foreach($kursSatisSor->fetchAll(PDO::FETCH_ASSOC) as $row) {
        if (isset($kursSatisData[$row['ay']])) {
            $kursSatisData[$row['ay']] = $row;
        }
    }

    // Revenue Statistics - Changed INTERVAL to 12 MONTH
    $gelirSor = $db->prepare("
        SELECT 
            DATE_FORMAT(created_at, '%Y-%m') as ay,
            SUM(toplam_tutar) as gelir
        FROM faturalar
        WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)
        AND odeme_durumu = 'onaylandi'
        GROUP BY DATE_FORMAT(created_at, '%Y-%m')
    ");
    $gelirSor->execute();
    $gelirData = array_fill_keys($last12Months, ['gelir' => 0]);
    
    foreach($gelirSor->fetchAll(PDO::FETCH_ASSOC) as $row) {
        if (isset($gelirData[$row['ay']])) {
            $gelirData[$row['ay']] = $row;
        }
    }

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

        <!-- Monthly Orders Chart -->
        <div class="row">
            <div class="col-md-12">
                <div class="x_panel">
                    <div class="x_title">
                        <h2>Son 12 Ay Sipariş İstatistikleri</h2>
                        <div class="clearfix"></div>
                    </div>
                    <div class="x_content">
                        <canvas id="siparisGrafik" style="height: 300px;"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="x_panel">
                    <div class="x_title">
                        <h2>Son 12 Ay Kurs Satış İstatistikleri</h2>
                        <div class="clearfix"></div>
                    </div>
                    <div class="x_content">
                        <canvas id="kursSatisGrafik" style="height: 300px;"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="x_panel">
                    <div class="x_title">
                        <h2>Son 12 Ay Gelir İstatistikleri</h2>
                        <div class="clearfix"></div>
                    </div>
                    <div class="x_content">
                        <canvas id="gelirGrafik" style="height: 300px;"></canvas>
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

// Orders Chart
new Chart(document.getElementById('siparisGrafik'), {
    type: 'line',
    data: {
        labels: <?php echo json_encode(array_keys($siparisData)); ?>,
        datasets: [{
            label: 'Toplam Sipariş',
            data: <?php echo json_encode(array_column($siparisData, 'toplam_siparis')); ?>,
            borderColor: '#4a69bd',
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

// Course Sales Chart
new Chart(document.getElementById('kursSatisGrafik'), {
    type: 'line',
    data: {
        labels: <?php echo json_encode(array_keys($kursSatisData)); ?>,
        datasets: [{
            label: 'Satılan Kurs Sayısı',
            data: <?php echo json_encode(array_column($kursSatisData, 'satis_sayisi')); ?>,
            borderColor: '#2ecc71',
            backgroundColor: 'rgba(46, 204, 113, 0.1)',
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

// Revenue Chart
new Chart(document.getElementById('gelirGrafik'), {
    type: 'line',
    data: {
        labels: <?php echo json_encode(array_keys($gelirData)); ?>,
        datasets: [{
            label: 'Aylık Gelir (TL)',
            data: <?php echo json_encode(array_column($gelirData, 'gelir')); ?>,
            borderColor: '#3498db',
            backgroundColor: 'rgba(52, 152, 219, 0.1)',
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
            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        return new Intl.NumberFormat('tr-TR', {
                            style: 'currency',
                            currency: 'TRY'
                        }).format(context.raw);
                    }
                }
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        return new Intl.NumberFormat('tr-TR', {
                            style: 'currency',
                            currency: 'TRY'
                        }).format(value);
                    }
                }
            }
        }
    }
});
</script>

<style>
.x_panel {
    margin-bottom: 30px;
}

.x_content {
    padding: 15px;
    position: relative;
    width: 100%;
    height: 300px;
}

canvas {
    width: 100% !important;
    max-height: 300px;
}
</style>

<?php include 'footer.php'; ?> 