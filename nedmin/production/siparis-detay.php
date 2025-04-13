<?php 
include 'header.php';

if(!isset($_GET['fatura_id'])) {
    header("Location: siparisler.php");
    exit();
}

// Get order details
$siparis = $db->prepare("
    SELECT 
        f.*,
        u.kullanici_ad,
        u.kullanici_soyad,
        u.kullanici_mail,
        fa.*
    FROM faturalar f
    LEFT JOIN kullanici u ON f.user_id = u.kullanici_id
    LEFT JOIN fatura_adresleri fa ON f.fatura_adres_id = fa.fatura_adres_id
    WHERE f.fatura_id = ?
");
$siparis->execute([$_GET['fatura_id']]);
$siparis = $siparis->fetch(PDO::FETCH_ASSOC);

if(!$siparis) {
    header("Location: siparisler.php");
    exit();
}

// Get ordered courses
$kurslar = $db->prepare("
    SELECT 
        k.*,
        sk.fiyat as satis_fiyat
    FROM satilan_kurslar sk
    JOIN kurslar k ON sk.kurs_id = k.kurs_id
    WHERE sk.fatura_id = ?
");
$kurslar->execute([$_GET['fatura_id']]);
?>

<div class="right_col" role="main">
    <div class="">
        <div class="page-title">
            <div class="title_left">
                <h3>Sipariş Detayı</h3>
            </div>
        </div>

        <div class="clearfix"></div>

        <div class="row">
            <div class="col-md-12">
                <div class="x_panel">
                    <div class="x_title">
                        <h2>Sipariş #<?php echo $siparis['fatura_no']; ?></h2>
                        <div class="clearfix"></div>
                    </div>

                    <div class="x_content">
                        <div class="row">
                            <!-- Order Status -->
                            <div class="col-md-12 mb-3">
                                <div class="alert alert-info">
                                    <strong>Sipariş Durumu:</strong> 
                                    <?php echo ucfirst($siparis['odeme_durumu']); ?>
                                    
                                    <?php if($siparis['odeme_durumu'] == 'beklemede') { ?>
                                        <div class="pull-right">
                                            <button class="btn btn-success btn-sm" onclick="siparisOnayla(<?php echo $siparis['fatura_id']; ?>)">
                                                <i class="fa fa-check"></i> Onayla
                                            </button>
                                            <button class="btn btn-danger btn-sm" onclick="siparisIptal(<?php echo $siparis['fatura_id']; ?>)">
                                                <i class="fa fa-times"></i> İptal
                                            </button>
                                        </div>
                                    <?php } ?>
                                </div>
                            </div>

                            <!-- Customer Info -->
                            <div class="col-md-6">
                                <div class="x_panel">
                                    <div class="x_title">
                                        <h2>Müşteri Bilgileri</h2>
                                        <div class="clearfix"></div>
                                    </div>
                                    <div class="x_content">
                                        <table class="table">
                                            <tr>
                                                <th>Ad Soyad:</th>
                                                <td><?php echo $siparis['kullanici_ad'] . ' ' . $siparis['kullanici_soyad']; ?></td>
                                            </tr>
                                            <tr>
                                                <th>E-posta:</th>
                                                <td><?php echo $siparis['kullanici_mail']; ?></td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <!-- Billing Info -->
                            <div class="col-md-6">
                                <div class="x_panel">
                                    <div class="x_title">
                                        <h2>Fatura Bilgileri</h2>
                                        <div class="clearfix"></div>
                                    </div>
                                    <div class="x_content">
                                        <table class="table">
                                            <tr>
                                                <th>Fatura Adı:</th>
                                                <td><?php echo $siparis['ad_soyad']; ?></td>
                                            </tr>
                                            <tr>
                                                <th>Adres:</th>
                                                <td>
                                                    <?php echo $siparis['adres']; ?><br>
                                                    <?php echo $siparis['ilce'] . '/' . $siparis['il']; ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>Telefon:</th>
                                                <td><?php echo $siparis['telefon']; ?></td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <!-- Ordered Courses -->
                            <div class="col-md-12">
                                <div class="x_panel">
                                    <div class="x_title">
                                        <h2>Satın Alınan Kurslar</h2>
                                        <div class="clearfix"></div>
                                    </div>
                                    <div class="x_content">
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Kurs</th>
                                                    <th>Eğitmen</th>
                                                    <th>Fiyat</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php while($kurs = $kurslar->fetch(PDO::FETCH_ASSOC)) { ?>
                                                    <tr>
                                                        <td><?php echo $kurs['baslik']; ?></td>
                                                        <td><?php echo $kurs['egitmen']; ?></td>
                                                        <td><?php echo number_format($kurs['satis_fiyat'], 2); ?> TL</td>
                                                    </tr>
                                                <?php } ?>
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <th colspan="2" class="text-right">Ara Toplam:</th>
                                                    <td><?php echo number_format($siparis['ara_toplam'], 2); ?> TL</td>
                                                </tr>
                                                <?php if($siparis['indirim_tutari'] > 0) { ?>
                                                    <tr>
                                                        <th colspan="2" class="text-right">İndirim:</th>
                                                        <td>-<?php echo number_format($siparis['indirim_tutari'], 2); ?> TL</td>
                                                    </tr>
                                                <?php } ?>
                                                <tr>
                                                    <th colspan="2" class="text-right">Toplam:</th>
                                                    <td><strong><?php echo number_format($siparis['toplam_tutar'], 2); ?> TL</strong></td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Same JavaScript functions as in siparisler.php
function siparisOnayla(faturaId) {
    Swal.fire({
        title: 'Emin misiniz?',
        text: "Sipariş onaylanacak ve müşteriye erişim verilecek!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Evet, Onayla',
        cancelButtonText: 'İptal'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = '../netting/islem.php?siparis_onay=' + faturaId;
        }
    });
}

function siparisIptal(faturaId) {
    Swal.fire({
        title: 'Emin misiniz?',
        text: "Sipariş iptal edilecek!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Evet, İptal Et',
        cancelButtonText: 'Vazgeç'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = '../netting/islem.php?siparis_iptal=' + faturaId;
        }
    });
}
</script>

<?php include 'footer.php'; ?> 