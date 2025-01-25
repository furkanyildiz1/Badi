<?php 

error_reporting(0);

include 'header.php'; 

//Belirli veriyi seçme işlemi
$bultensor=$db->prepare("SELECT * FROM bulten");
$bultensor->execute();


?>


<!-- page content -->
<div class="right_col" role="main">
  <div class="">

    <div class="clearfix"></div>
    <div class="row">
      <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="x_panel">
          <div class="x_title">
            <div align="right">
              <a href="bulten-gonder.php"><button class="btn-primary btn-xs">Toplu Gönder</button></a>
            </div>
            <h2><strong>İletişim Form</strong> Başvuruları Listeleme <small>

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
          </div>
          <div class="x_content">

            <!-- Div İçerik Başlangıç -->

            <table id="datatable-responsive" class="table table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
              <thead>
                <tr>
                  <th width="10">S.No</th>
                  <th>Ad Soyad</th>
                  <th>Durum</th>
                  <th></th>
                </tr>
              </thead>

              <tbody>

                <?php 
                $say=0;

                while($bultencek=$bultensor->fetch(PDO::FETCH_ASSOC)) { $say++?> <!-- while -> sonuc dogru döndükce bu islemi tekrarlar -->


                <tr>
                  <td width="20px;"><?php echo $say ?></td>
                  <td><?php echo $bultencek['bulten_mail'] ?></td>
                  <td> <center>
                    <?php 
                    if($bultencek['bulten_durum'] == "1")
                    { ?>
                      <button class="btn btn-success btn-xs">Bülten Sağlandı</button>
                    <?php }  
                    else if ($bultencek['bulten_durum'] == "0")
                    {  ?>
                      <button class="btn btn-danger btn-xs">Bülten Sağlanmadı</button>
                    <?php } ?>
                  </td></center>

                  <td><a href="../netting/islem.php?bulten_id=<?php echo $bultencek['bulten_id']; ?>&bultensil=ok"><center><button class="btn btn-danger btn-xs">Sil</button></center></a></td>
                </tr>



                <?php  }

                ?>


              </tbody>
            </table>

            <!-- Div İçerik Bitişi -->


          </div>
        </div>
      </div>
    </div>




  </div>
</div>
<!-- /page content -->

<?php include 'footer.php'; ?>
