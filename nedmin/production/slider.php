<?php 

error_reporting(0);

include 'header.php'; 

//Belirli veriyi seçme işlemi
$slidersor=$db->prepare("SELECT * FROM slider");
$slidersor->execute();


?>

 <style>
  .modal-fullscreen .modal-dialog {
    max-width: 100%;
    height: 100%;
    margin: 0;
    display: flex;
    justify-content: center; /* Yatay ortalama */
    align-items: center; /* Dikey ortalama */
  }

  .modal-fullscreen .modal-content {
    background: transparent; /* Arka planı kaldır */
    border: none; /* Kenarlıkları kaldır */
    border-radius: 0;
    box-shadow: none;
  }

  .modal-fullscreen .modal-body {
    display: flex;
    justify-content: center; /* Resmi yatay ortalar */
    align-items: center; /* Resmi dikey ortalar */
    height: 100%;
    padding: 0;
  }

  .modal-fullscreen img {
    max-width: 100%;
    max-height: 100%;
    object-fit: contain; /* Oranı koruyarak resmi sığdır */
  }
</style>





  

<!-- page content -->
<div class="right_col" role="main">
  <div class="">

    <div class="clearfix"></div>
    <div class="row">
      <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="x_panel">
          <div class="x_title">
            <h2>Slider Listeleme <small>,

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


            </small></h2>
            <div class="clearfix"></div>
              <div align="right">
            <a href="slider-ekle.php"><button class="btn btn-success btn-xs">Yeni Ekle</button></a>
              </div>
          </div>
          <div class="x_content">

            <strong><p style="font-size:15px;">Geliştirme Aşamasındayken Lütfen Slider İsimlerinde Türkçe Karakter Kullanmayınız!</p></strong>

            <!-- Div İçerik Başlangıç -->

            <!-- Modal -->
              <div class="modal fade modal-fullscreen" id="imageModal" tabindex="-1" role="dialog" aria-labelledby="imageModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                  <div class="modal-content">
                    <div class="modal-header">
                      <h5 class="modal-title text-white" id="imageModalLabel">Resim Görüntüle</h5>
                      <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                      </button>
                    </div>
                    <div class="modal-body">
                      <img id="modalImage" src="" alt="Resim">
                    </div>
                  </div>
                </div>
              </div>


              <!-- Slider Tablosu -->
              <table id="datatable-responsive" class="table table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
  <thead>
    <tr>
      <th>S.No</th>
      <th>Resim</th>
      <th>Ad</th>
      <th>Url</th>
      <th>Sıra</th>
      <th>Durum</th>
      <th></th>
      <th></th>
    </tr>
  </thead>
  <tbody>
    <?php 
    $say = 0;
    while ($slidercek = $slidersor->fetch(PDO::FETCH_ASSOC)) { 
      $say++; 
    ?>
    <tr>
      <td width="20"><?php echo $say ?></td>
      <td>
        <!-- Resim için modal açma -->
        <img width="200" src="../../<?php echo $slidercek['slider_resimyol'] ?>" class="img-thumbnail preview-image" alt="Resim" data-image-src="../../<?php echo $slidercek['slider_resimyol'] ?>" style="cursor: pointer;">
      </td>
      <td><?php echo $slidercek['slider_ad'] ?></td>
      <td><?php echo $slidercek['slider_link'] ?></td>
      <td><?php echo $slidercek['slider_sira'] ?></td>
      <td>
        <center>
          <?php 
          if ($slidercek['slider_durum'] == "1") { ?>
            <button class="btn btn-success btn-xs">Aktif</button>
          <?php } else if ($slidercek['slider_durum'] == "0") { ?>
            <button class="btn btn-danger btn-xs">Pasif</button>
          <?php } ?>
        </center>
      </td>
      <td><center><a href="slider-duzenle.php?slider_id=<?php echo $slidercek['slider_id']; ?>"><button class="btn btn-primary btn-xs">Düzenle</button></a></center></td>
      <td>
        <center>
          <!-- Silme Butonu için SweetAlert Onaylama -->
          <a href="javascript:void(0);" onclick="confirmDelete('<?php echo $slidercek['slider_id']; ?>')">
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
  function confirmDelete(sliderId) {
    Swal.fire({
      title: 'Emin misiniz?',
      text: 'Bu işlem geri alınamaz!',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonText: 'Evet, sil!',
      cancelButtonText: 'Hayır, iptal et',
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
        window.location.href = '../netting/islem.php?slider_id=' + sliderId + '&slidersil=ok';
      }
    });
  }
</script>


              <!-- Modal ve Resim Açma Scripti -->
              <script>
                // Tüm resimlere tıklama olayını ekle
                document.querySelectorAll('.preview-image').forEach(img => {
                  img.addEventListener('click', function () {
                    const imageUrl = this.getAttribute('data-image-src'); // Resim URL'sini al
                    const modalImage = document.getElementById('modalImage'); // Modal içerisindeki resim
                    modalImage.src = imageUrl; // Modal resmini değiştir
                    $('#imageModal').modal('show'); // Modal'ı aç
                  });
                });
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
