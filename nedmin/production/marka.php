<?php 

error_reporting(0);

include 'header.php'; 

//Belirli veriyi seçme işlemi
$markasor=$db->prepare("SELECT * FROM markalar");
$markasor->execute();


?>


<!-- page content -->
<div class="right_col" role="main">
  <div class="">

    <div class="clearfix"></div>
    <div class="row">
      <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="x_panel">
          <div class="x_title">
            <h2><strong>Marka</strong> Listeleme <small>

            <?php 
                if ($_GET['durum'] == "ok") { ?>
                    <script>
                        Swal.fire({
                            title: 'Başarılı!',
                            text: 'İşlem başarıyla tamamlandı.',
                            icon: 'success',
                            confirmButtonText: 'Tamam',
                            confirmButtonColor: '#ffb000',
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
            <a href="marka-ekle.php"><button class="btn btn-success btn-xs">Yeni Ekle</button></a>
              </div>
          </div>
          <div class="x_content">

            <!-- Div İçerik Başlangıç -->

            <table id="datatable-responsive" class="table table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
              <thead>
                <tr>
                  <th>S.No</th>
                  <th>Ad</th>
                  <th>Resim</th>
                  <th></th>
                </tr>
              </thead>

              <tbody>

                <?php 
                $say=0;

                while($markacek=$markasor->fetch(PDO::FETCH_ASSOC)) { $say++?> <!-- while -> sonuc dogru döndükce bu islemi tekrarlar -->


                <tr>
                  <td width="20"><?php echo $say ?></td>
                  <td><?php echo $markacek['marka_ad'] ?></td>
                  <td><img width="100" src="../../<?php echo $markacek['marka_resimyol'] ?>"></td>

                  <td><a href="javascript:void(0);" onclick="silConfirm('<?php echo $markacek['marka_id']; ?>')">
                    <center><button class="btn btn-danger btn-xs">Sil</button></center>
                  </a></td>


                  <?php echo $markacek['marka_eskiresimyol']; ?>

                </tr>

                <?php  }

                ?>


              </tbody>
            </table>

            <!-- Div İçerik Bitişi -->

            <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
              <script>
                function silConfirm(markaId) {
                  Swal.fire({
                    title: 'Emin misiniz?',
                    text: 'Bu markayı silmek istediğinizden emin misiniz?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Evet, sil!',
                    cancelButtonText: 'Hayır, iptal et!',
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    reverseButtons: true
                  }).then((result) => {
                    if (result.isConfirmed) {
                      window.location.href = "../netting/islem.php?marka_id=" + markaId + "&markasil=ok";
                    }
                  });
                }
              </script>



          </div>
        </div>
      </div>
    </div>




  </div>
</div>
<!-- /page content -->

<?php include 'footer.php'; ?>
