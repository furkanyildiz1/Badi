<?php 

error_reporting(0);

include 'header.php'; 

$kurssor=$db->prepare("SELECT * FROM kurslar");
$kurssor->execute();


?>


<!-- page content -->
<div class="right_col" role="main">
  <div class="">

    <div class="clearfix"></div>
    <div class="row">
      <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="x_panel">
          <div class="x_title">
            <h2><strong>Kurs</strong> Listeleme <small>

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

                <?php 
                if ($_GET['sil'] == "ok") { ?>
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
                <?php } else if ($_GET['sil'] == "no") { ?>
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


            </small></h2>
            <div class="clearfix"></div>
              <div align="right">
            <a href="kurs-ekle.php"><button class="btn btn-success btn-xs">Yeni Ekle</button></a>
              </div>
          </div>
          <div class="x_content">

            <!-- Div İçerik Başlangıç -->

            <table id="datatable-responsive" class="table table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
  <thead>
    <tr>
      <th>S.No</th>
      <th>Kurs Ad</th>
      <th>Kurs Fiyat</th>
      <th>Kurs Oluşturulma Tarihi</th>
      <th>Kurs Detay Bilgileri</th>
      <th>İşlem 1</th>
      <th>İşlem 2</th>
      <th>İşlem 3</th>
    </tr>
  </thead>

  <tbody>

    <?php 

// Kursleri döngü içinde listeleme
$say = 0;
while($kurscek=$kurssor->fetch(PDO::FETCH_ASSOC)) { 

    $say++;
?>
    <tr>
        <td width="20px;"><?php echo $say ?></td>
        <td><?php echo $kurscek['baslik'] ?></td>
        <td><?php 
            // Find minimum certificate price (excluding transcripts)
            $prices = [];
            if (!empty($kurscek['edevlet_cert_price']) && $kurscek['edevlet_cert_price'] > 0) 
                $prices[] = $kurscek['edevlet_cert_price'];
            if (!empty($kurscek['eng_cert_price']) && $kurscek['eng_cert_price'] > 0) 
                $prices[] = $kurscek['eng_cert_price'];
            if (!empty($kurscek['tr_cert_price']) && $kurscek['tr_cert_price'] > 0) 
                $prices[] = $kurscek['tr_cert_price'];
            
            $min_price = !empty($prices) ? min($prices) : 0;
            echo $min_price > 0 ? $min_price . ' TL' : 'Ücretsiz';
        ?></td>
        <td><?php
            $timestamp = strtotime($kurscek['olusturma_tarihi']);
            $day = date('d', $timestamp);
            $month_num = date('n', $timestamp); // Get month as a number (1-12)
            $time = date('H:i', $timestamp);
            
            // Turkish month names
            $turkish_months = [
                1 => 'Ocak',
                2 => 'Şubat',
                3 => 'Mart',
                4 => 'Nisan',
                5 => 'Mayıs',
                6 => 'Haziran',
                7 => 'Temmuz',
                8 => 'Ağustos',
                9 => 'Eylül',
                10 => 'Ekim',
                11 => 'Kasım',
                12 => 'Aralık'
            ];
            
            echo $day . ' ' . $turkish_months[$month_num] . ' ' . $time;
        ?></td>
        <td><a href="kurs-detay.php?kurs_id=<?php echo $kurscek['kurs_id']; ?>"><button class="btn btn-info btn-sm">Kurs Detay Bilgileri İçin Tıklayınız</button></a></td>
        <td><center><a href="kurs-duzenle.php?kurs_id=<?php echo $kurscek['kurs_id']; ?>">
            <button class="btn btn-primary btn-xs">Düzenle</button>
        </a></center></td>
        <td><center><a href="kurs-icerik-duzenle.php?kurs_id=<?php echo $kurscek['kurs_id']; ?>">
            <button class="btn btn-info btn-xs">İçerik Düzenle</button>
        </a></center></td>
        <td>
            <center>
                <a href="javascript:void(0);" onclick="confirmDelete('<?php echo $kurscek['kurs_id']; ?>')">
                    <button class="btn btn-danger btn-xs">Sil</button>
                </a>
            </center>
        </td>
    </tr>
<?php } ?>

  </tbody>
</table>

<!-- SweetAlert için JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
  function confirmDelete(kategoriId) {
    Swal.fire({
      title: 'Emin misiniz?',
                text: "Bu işlem geri alınamaz!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Evet, Sil!',
                cancelButtonText: 'Hayır, İptal Et',
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                background: '#f9f9f9',
                customClass: {
                    popup: 'shadow-lg rounded',
                    title: 'text-danger',
                    content: 'text-dark',
                },
    }).then((result) => {
      if (result.isConfirmed) {
        // Eğer onay verilirse, silme işlemini gerçekleştiren sayfaya yönlendirme
        window.location.href = '../netting/islem.php?kurs_id=' + kategoriId + '&kurs_sil=ok';
      }
    });
  }
</script>


            <!-- Div İçerik Bitişi -->


          </div>
        </div>
      </div>
    </div>




  </div>
</div>
<!-- /page content -->

<?php include 'footer.php'; ?>
