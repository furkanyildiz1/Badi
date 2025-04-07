<?php 
include 'header.php';

// Get all orders with basic filtering
$where = "1=1";
$params = [];

if(isset($_GET['durum']) && $_GET['durum'] != '') {
    $where .= " AND odeme_durumu = :durum";
    $params['durum'] = $_GET['durum'];
}

if(isset($_GET['odeme_tipi']) && $_GET['odeme_tipi'] != '') {
    $where .= " AND odeme_yontemi = :odeme_tipi";
    $params['odeme_tipi'] = $_GET['odeme_tipi'];
}

$siparisler = $db->prepare("
    SELECT 
        f.*, 
        u.kullanici_ad,
        u.kullanici_soyad,
        u.kullanici_mail,
        COUNT(sk.satis_id) as kurs_sayisi,
        fa.ad_soyad as fatura_adsoyad
    FROM faturalar f
    LEFT JOIN kullanici u ON f.user_id = u.kullanici_id
    LEFT JOIN satilan_kurslar sk ON f.fatura_id = sk.fatura_id
    LEFT JOIN fatura_adresleri fa ON f.fatura_adres_id = fa.fatura_adres_id
    WHERE $where
    GROUP BY f.fatura_id
    ORDER BY f.created_at DESC
");
$siparisler->execute($params);

?>

<div class="right_col" role="main">
    <?php 
    $islem_sonuc = isset($_GET['islem']) ? $_GET['islem'] : '';
    
    if($islem_sonuc == 'ok' || $islem_sonuc == 'no') { ?>
        <div style="
            margin: 20px 0;
            padding: 15px 25px;
            border-radius: 4px;
            background: <?php echo $islem_sonuc == 'ok' ? '#e8f5e9' : '#ffebee'; ?>;
            color: <?php echo $islem_sonuc == 'ok' ? '#2e7d32' : '#c62828'; ?>;
            border-left: 5px solid <?php echo $islem_sonuc == 'ok' ? '#4caf50' : '#ef5350'; ?>;
            font-weight: 500;
            font-size: 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            position: relative;
            z-index: 9999;
        ">
            <div>
                <i class="fa <?php echo $islem_sonuc == 'ok' ? 'fa-check-circle' : 'fa-exclamation-circle'; ?>" style="margin-right: 10px;"></i>
                <?php echo $islem_sonuc == 'ok' ? 'İşlem başarıyla gerçekleştirildi.' : 'İşlem sırasında bir hata oluştu.'; ?>
            </div>
            <button onclick="this.parentElement.style.display='none';" style="
                background: transparent;
                border: none;
                color: <?php echo $islem_sonuc == 'ok' ? '#2e7d32' : '#c62828'; ?>;
                cursor: pointer;
                font-size: 20px;
                padding: 0;
                margin-left: 15px;
            ">&times;</button>
        </div>
    <?php } ?>

    <div class="">
        <div class="page-title">
            <div class="title_left">
                <h3>Sipariş Yönetimi</h3>
            </div>
        </div>

        <div class="clearfix"></div>

        <div class="row">
            <div class="col-md-12">
                <div class="x_panel">
                    <div class="x_title">
                        <h2>Siparişler</h2>
                        <div class="clearfix"></div>
                    </div>

                    <!-- Filters -->
                    <div class="x_content">
                        <form class="form-inline mb-3" method="GET">
                            <div class="form-group mx-2">
                                <select name="durum" class="form-control">
                                    <option value="">Tüm Durumlar</option>
                                    <option value="beklemede" <?php echo isset($_GET['durum']) && $_GET['durum'] == 'beklemede' ? 'selected' : ''; ?>>Beklemede</option>
                                    <option value="onaylandi" <?php echo isset($_GET['durum']) && $_GET['durum'] == 'onaylandi' ? 'selected' : ''; ?>>Onaylandı</option>
                                    <option value="iptal_edildi" <?php echo isset($_GET['durum']) && $_GET['durum'] == 'iptal_edildi' ? 'selected' : ''; ?>>İptal Edildi</option>
                                </select>
                            </div>
                            <div class="form-group mx-2">
                                <select name="odeme_tipi" class="form-control">
                                    <option value="">Tüm Ödeme Tipleri</option>
                                    <option value="kredi_karti" <?php echo isset($_GET['odeme_tipi']) && $_GET['odeme_tipi'] == 'kredi_karti' ? 'selected' : ''; ?>>Kredi Kartı</option>
                                    <option value="havale" <?php echo isset($_GET['odeme_tipi']) && $_GET['odeme_tipi'] == 'havale' ? 'selected' : ''; ?>>Havale</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary">Filtrele</button>
                            <a href="siparisler.php" class="btn btn-default">Sıfırla</a>
                        </form>

                        <table id="datatable" class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>Fatura No</th>
                                    <th>Müşteri</th>
                                    <th>Fatura Adı</th>
                                    <th>Tutar</th>
                                    <th>Kurs Sayısı</th>
                                    <th>Ödeme Tipi</th>
                                    <th>Durum</th>
                                    <th>Tarih</th>
                                    <th>İşlem</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while($siparis = $siparisler->fetch(PDO::FETCH_ASSOC)) { ?>
                                    <tr>
                                        <td><?php echo $siparis['fatura_no']; ?></td>
                                        <td>
                                            <?php echo $siparis['kullanici_ad'] . ' ' . $siparis['kullanici_soyad']; ?><br>
                                            <small><?php echo $siparis['kullanici_mail']; ?></small>
                                        </td>
                                        <td><?php echo $siparis['fatura_adsoyad']; ?></td>
                                        <td><?php echo number_format($siparis['toplam_tutar'], 2); ?> TL</td>
                                        <td><?php echo $siparis['kurs_sayisi']; ?></td>
                                        <td>
                                            <?php 
                                            echo $siparis['odeme_yontemi'] == 'kredi_karti' ? 
                                                '<span class="label label-info">Kredi Kartı</span>' : 
                                                '<span class="label label-warning">Havale</span>'; 
                                            ?>
                                        </td>
                                        <td>
                                            <?php 
                                            $durum_class = [
                                                'beklemede' => 'warning',
                                                'onaylandi' => 'success',
                                                'iptal_edildi' => 'danger'
                                            ];
                                            ?>
                                            <span class="label label-<?php echo $durum_class[$siparis['odeme_durumu']]; ?>">
                                                <?php echo ucfirst($siparis['odeme_durumu']); ?>
                                            </span>
                                        </td>
                                        <td><?php echo date('d.m.Y H:i', strtotime($siparis['created_at'])); ?></td>
                                        <td>
                                            <a href="siparis-detay.php?fatura_id=<?php echo $siparis['fatura_id']; ?>" 
                                               class="btn btn-info btn-sm">
                                                <i class="fa fa-eye"></i> Detay
                                            </a>
                                            <?php if($siparis['odeme_durumu'] == 'beklemede') { ?>
                                                <button type="button" 
                                                        class="btn btn-success btn-sm"
                                                        onclick="siparisOnayla(<?php echo $siparis['fatura_id']; ?>)">
                                                    <i class="fa fa-check"></i> Onayla
                                                </button>
                                                <button type="button" 
                                                        class="btn btn-danger btn-sm"
                                                        onclick="siparisIptal(<?php echo $siparis['fatura_id']; ?>)">
                                                    <i class="fa fa-times"></i> İptal
                                                </button>
                                            <?php } ?>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
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
            // Preserve existing filters in the redirect
            let currentUrl = new URL(window.location.href);
            let filterParams = '';
            if(currentUrl.searchParams.get('durum')) {
                filterParams += '&durum=' + currentUrl.searchParams.get('durum');
            }
            if(currentUrl.searchParams.get('odeme_tipi')) {
                filterParams += '&odeme_tipi=' + currentUrl.searchParams.get('odeme_tipi');
            }
            window.location.href = `../netting/islem.php?siparis_onay=${faturaId}${filterParams}`;
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
            // Preserve existing filters in the redirect
            let currentUrl = new URL(window.location.href);
            let filterParams = '';
            if(currentUrl.searchParams.get('durum')) {
                filterParams += '&durum=' + currentUrl.searchParams.get('durum');
            }
            if(currentUrl.searchParams.get('odeme_tipi')) {
                filterParams += '&odeme_tipi=' + currentUrl.searchParams.get('odeme_tipi');
            }
            window.location.href = `../netting/islem.php?siparis_iptal=${faturaId}${filterParams}`;
        }
    });
}

$(document).ready(function() {
    $('#datatable').DataTable({
        "order": [[ 7, "desc" ]], // Sort by column 7 (Tarih/Date) in descending order
        "columnDefs": [
            {
                "targets": 7, // Target the date column (index 7)
                "type": "date-eu", // Use European date format (day first)
                "render": function(data, type, row) {
                    // For sorting and filtering, convert to YYYY-MM-DD format
                    if (type === 'sort' || type === 'filter') {
                        // Parse the DD.MM.YYYY HH:MM format
                        var parts = data.split(' ')[0].split('.');
                        var time = data.split(' ')[1];
                        // Return YYYY-MM-DD HH:MM for proper sorting
                        return parts[2] + '-' + parts[1] + '-' + parts[0] + ' ' + time;
                    }
                    // For display, keep the original format
                    return data;
                }
            }
        ],
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.25/i18n/Turkish.json"
        }
    });
});
</script>

<?php include 'footer.php'; ?> 