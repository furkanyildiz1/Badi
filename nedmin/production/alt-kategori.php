<?php 

error_reporting(0);

include 'header.php'; 

$altkategorisor=$db->prepare("SELECT * FROM alt_kategoriler");
$altkategorisor->execute();


?>


<!-- page content -->
<div class="right_col" role="main">
  <div class="">

    <div class="clearfix"></div>
    <div class="row">
      <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="x_panel">
          <div class="x_title">
            <h2><strong>Alt Kategori</strong> Listeleme <small>

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
            <a href="alt-kategori-ekle.php"><button class="btn btn-success btn-xs">Yeni Ekle</button></a>
              </div>
          </div>
          <div class="x_content">

            <!-- Div İçerik Başlangıç -->

            <table id="datatable-responsive" class="table table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
  <thead>
    <tr>
      <th>S.No</th>
      <th>Alt Kategori Ad</th>
      <th>Bağlı Olduğu Kategori</th>
      <th>Alt Kategori Açıklama</th>
      <th>Alt Kategori Oluşturulma Tarihi</th>
      <th>İşlem 1</th>
      <th>İşlem 2</th>
    </tr>
  </thead>

  <tbody>

    <?php 

// Alt kategorileri döngü içinde listeleme
while($altkategoricek=$altkategorisor->fetch(PDO::FETCH_ASSOC)) { 

    $say++;

    // Bağlı olduğu kategoriyi çekme işlemi
    $kategori_id_sor=$db->prepare("SELECT * FROM kategoriler WHERE kategori_id=:kategori_id");
    $kategori_id_sor->execute([
        'kategori_id' => $altkategoricek['kategori_id']
    ]);

    // Bu satırda doğru nesneyi kullanıyoruz
    $kategoricek=$kategori_id_sor->fetch(PDO::FETCH_ASSOC); 
?>
    <tr>
        <td width="20px;"><?php echo $say ?></td>
        <td><?php echo $altkategoricek['ad'] ?></td>
        <td><?php echo $kategoricek['ad'] ?? 'Kategori Bulunamadı'; ?></td>
        <td><?php echo $altkategoricek['aciklama'] ?></td>
        <td><?php echo date('d F H:i', strtotime($altkategoricek['olusturma_tarihi'])); ?></td>
        <td><center><a href="alt-kategori-duzenle.php?alt_kategori_id=<?php echo $altkategoricek['alt_kategori_id']; ?>"><button class="btn btn-primary btn-xs">Düzenle</button></a></center></td>
        <td>
            <center>
                <!-- Silme Butonu için SweetAlert Onaylama -->
                <a href="javascript:void(0);" onclick="confirmDelete('<?php echo $altkategoricek['alt_kategori_id']; ?>')">
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
        window.location.href = '../netting/islem.php?alt_kategori_id=' + kategoriId + '&alt_kategori_sil=ok';
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
