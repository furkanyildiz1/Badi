<?php 
include 'header.php';

// jQuery'yi en başa ekleyelim


// Başvuruları çek (kullanıcı bilgileriyle birlikte)
$basvurusor = $db->prepare("SELECT eb.*, k.kullanici_ad, k.kullanici_soyad, k.kullanici_mail, k.kullanici_tel 
                           FROM egitmen_basvuru eb
                           INNER JOIN kullanici k ON eb.kullanici_id = k.kullanici_id 
                           ORDER BY eb.basvuru_zaman DESC");
$basvurusor->execute();
?>

<div class="right_col" role="main">
    <div class="">
        <div class="page-title">
            <div class="title_left">
                <h3>Eğitmen Başvuruları</h3>
            </div>
        </div>

        <div class="clearfix"></div>

        <div class="row">
            <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel">
                    <div class="x_title">
                        <h2>Başvuru Listesi 
                            <small>
                                <?php 
                                if ($_GET['durum'] == "ok") { ?>
                                    <script>
                                        Swal.fire({
                                            title: 'Başarılı!',
                                            text: 'İşlem başarıyla tamamlandı.',
                                            icon: 'success',
                                            confirmButtonText: 'Tamam',
                                            confirmButtonColor: '#007bff',
                                            background: '#f9f9f9',
                                            customClass: {
                                                popup: 'shadow-lg rounded',
                                                title: 'text-primary',
                                                content: 'text-dark',
                                            },
                                        });
                                    </script>
                                <?php } else if ($_GET['durum'] == "no") { ?>
                                    <script>
                                        Swal.fire({
                                            title: 'Hata!',
                                            text: 'İşlem başarısız oldu. Lütfen tekrar deneyin.',
                                            icon: 'error',
                                            confirmButtonText: 'Tamam',
                                            confirmButtonColor: '#d33',
                                            background: '#f9f9f9',
                                            customClass: {
                                                popup: 'shadow-lg rounded',
                                                title: 'text-danger',
                                                content: 'text-dark',
                                            },
                                        });
                                    </script>
                                <?php } ?>
                            </small>
                        </h2>
                        <div class="clearfix"></div>
                    </div>
                    <div class="x_content">
                        <!-- Tablo içeriği buraya -->
                        <?php /* Debug bilgisi HTML yorum olarak */ 
                        if($basvurusor->rowCount() > 0) {
                            echo "<!-- " . $basvurusor->rowCount() . " başvuru bulundu -->";
                        } else {
                            echo "<!-- Hiç başvuru bulunamadı -->";
                        }
                        ?>
                        
                        <table id="datatable-responsive" class="table table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
                            <thead>
                                <tr>
                                    <th>Başvuru Tarihi</th>
                                    <th>Ad Soyad</th>
                                    <th>E-mail</th>
                                    <th>Telefon</th>
                                    <th>Durum</th>
                                    <th>CV</th>
                                    <th>İşlemler</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while($basvurucek=$basvurusor->fetch(PDO::FETCH_ASSOC)) { ?>
                                    <tr>
                                        <td><?php echo date('d.m.Y H:i', strtotime($basvurucek['basvuru_zaman'])) ?></td>
                                        <td><?php echo $basvurucek['kullanici_ad']." ".$basvurucek['kullanici_soyad'] ?></td>
                                        <td><?php echo $basvurucek['kullanici_mail'] ?></td>
                                        <td><?php echo $basvurucek['kullanici_tel'] ?></td>
                                        <td>
                                            <?php 
                                            $durum = $basvurucek['basvuru_durum'];
                                            if($durum == 'pending') {
                                                echo '<span class="label label-warning">Beklemede</span>';
                                            } else if($durum == 'accept') {
                                                echo '<span class="label label-success">Onaylandı</span>';
                                            } else if($durum == 'refuse') {
                                                echo '<span class="label label-danger">Reddedildi</span>';
                                            }
                                            ?>
                                        </td>
                                        <td>
                                            <?php if(!empty($basvurucek['cv_yol'])) { ?>
                                                <a href="../../<?php echo $basvurucek['cv_yol'] ?>" target="_blank" class="btn btn-info btn-sm">
                                                    <i class="fa fa-file-pdf-o"></i> CV
                                                </a>
                                            <?php } else { ?>
                                                <span class="label label-default">CV Yok</span>
                                            <?php } ?>
                                        </td>
                                        <td>
                                            <?php if($basvurucek['basvuru_durum'] == 'pending') { ?>
                                                <button onclick="basvuruOnayla(<?php echo $basvurucek['basvuru_id'] ?>)" class="btn btn-success btn-sm">
                                                    <i class="fa fa-check"></i> Onayla
                                                </button>
                                                <button onclick="basvuruReddet(<?php echo $basvurucek['basvuru_id'] ?>)" class="btn btn-danger btn-sm">
                                                    <i class="fa fa-times"></i> Reddet
                                                </button>
                                            <?php } ?>
                                            <button onclick="basvuruDetay(<?php echo $basvurucek['basvuru_id'] ?>)" class="btn btn-primary btn-sm">
                                                <i class="fa fa-eye"></i> Detay
                                            </button>
                                            <?php if($basvurucek['basvuru_durum'] == 'refuse' && !empty($basvurucek['red_nedeni'])) { ?>
                                                <button onclick="Swal.fire('Red Nedeni', '<?php echo $basvurucek['red_nedeni'] ?>', 'info')" class="btn btn-warning btn-sm">
                                                    <i class="fa fa-question-circle"></i> Red Nedeni
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
function basvuruOnayla(basvuru_id) {
    Swal.fire({
        title: 'Eğitmen Başvurusunu Onayla',
        text: "Bu başvuruyu onaylamak istediğinize emin misiniz?",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Evet, Onayla',
        cancelButtonText: 'İptal'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '../netting/islem.php',
                type: 'POST',
                data: {
                    basvuru_id: basvuru_id,
                    basvuru_onay: 'ok'
                },
                success: function(response) {
                    Swal.fire({
                        title: 'Başarılı!',
                        text: 'Başvuru onaylandı',
                        icon: 'success'
                    }).then(() => {
                        location.reload();
                    });
                }
            });
        }
    });
}

function basvuruReddet(basvuru_id) {
    Swal.fire({
        title: 'Red Nedeni',
        input: 'text',
        inputLabel: 'Lütfen red nedenini yazın',
        showCancelButton: true,
        confirmButtonText: 'Reddet',
        cancelButtonText: 'İptal',
        inputValidator: (value) => {
            if (!value) {
                return 'Red nedeni yazmalısınız!'
            }
        }
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '../netting/islem.php',
                type: 'POST',
                data: {
                    basvuru_id: basvuru_id,
                    basvuru_red: 'ok',
                    red_nedeni: result.value
                },
                success: function(response) {
                    Swal.fire({
                        title: 'Başarılı!',
                        text: 'Başvuru reddedildi',
                        icon: 'success'
                    }).then(() => {
                        location.reload();
                    });
                }
            });
        }
    });
}

function basvuruDetay(basvuru_id) {
    $.ajax({
        url: '../netting/islem.php',
        type: 'POST',
        dataType: 'json',
        data: {
            basvuru_id: basvuru_id,
            basvuru_detay: 'ok'
        },
        success: function(response) {
            if(response.status === 'success') {
                const data = response.data;
                Swal.fire({
                    title: '<strong>Eğitmen Başvuru Detayları</strong>',
                    html: `
                        <div class="basvuru-detay">
                            <div class="detay-grup">
                                <i class="fa fa-user"></i>
                                <div class="detay-icerik">
                                    <label>Ad Soyad:</label>
                                    <p>${data.ad_soyad}</p>
                                </div>
                            </div>
                            
                            <div class="detay-grup">
                                <i class="fa fa-graduation-cap"></i>
                                <div class="detay-icerik">
                                    <label>Uzmanlık Alanı:</label>
                                    <p>${data.uzmanlik_alani}</p>
                                </div>
                            </div>

                            <div class="detay-grup">
                                <i class="fa fa-info-circle"></i>
                                <div class="detay-icerik">
                                    <label>Hakkında:</label>
                                    <div class="hakkinda-metin">${data.hakkinda}</div>
                                </div>
                            </div>

                            <div class="detay-grup">
                                <i class="fa fa-calendar"></i>
                                <div class="detay-icerik">
                                    <label>Başvuru Tarihi:</label>
                                    <p>${data.basvuru_zaman}</p>
                                </div>
                            </div>
                        </div>
                    `,
                    width: '700px',
                    showCloseButton: true,
                    showConfirmButton: false
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Hata!',
                    text: response.message
                });
            }
        },
        error: function() {
            Swal.fire({
                icon: 'error',
                title: 'Hata!',
                text: 'Veriler alınırken bir hata oluştu.'
            });
        }
    });
}
</script>

<style>
/* Başvuru Detay Popup Stilleri */
.basvuru-detay {
    padding: 20px;
    text-align: left;
}

.detay-grup {
    display: flex;
    align-items: flex-start;
    margin-bottom: 20px;
    padding: 15px;
    background: #f8f9fa;
    border-radius: 8px;
    transition: all 0.3s ease;
}

.detay-grup:hover {
    background: #e9ecef;
    transform: translateX(5px);
}

.detay-grup i {
    font-size: 24px;
    color: #3498db;
    margin-right: 15px;
    margin-top: 5px;
}

.detay-icerik {
    flex: 1;
}

.detay-icerik label {
    display: block;
    font-weight: 600;
    color: #2c3e50;
    margin-bottom: 5px;
    font-size: 14px;
}

.detay-icerik p {
    margin: 0;
    color: #34495e;
    font-size: 15px;
}

.hakkinda-metin {
    padding: 10px;
    background: white;
    border-radius: 4px;
    border-left: 4px solid #3498db;
    color: #34495e;
    font-size: 15px;
    line-height: 1.6;
    max-height: 200px;
    overflow-y: auto;
}

/* SweetAlert Özelleştirmeleri */
.swal2-popup {
    border-radius: 15px !important;
}

.swal2-title {
    color: #2c3e50 !important;
    font-size: 24px !important;
    padding: 20px 0 !important;
}

.swal2-close {
    color: #95a5a6 !important;
}

.swal2-close:hover {
    color: #34495e !important;
}

/* Scrollbar Özelleştirmesi */
.hakkinda-metin::-webkit-scrollbar {
    width: 8px;
}

.hakkinda-metin::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 4px;
}

.hakkinda-metin::-webkit-scrollbar-thumb {
    background: #bdc3c7;
    border-radius: 4px;
}

.hakkinda-metin::-webkit-scrollbar-thumb:hover {
    background: #95a5a6;
}
</style>

<?php include 'footer.php'; ?> 