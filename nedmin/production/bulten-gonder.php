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
            <h2><strong>Bülten Gönderme</strong> İşlemi <small>

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

            <div>
              <br>
                <form action="../netting/islem.php" method="POST">
                    <div class="form-group">
                        <label for="subject">Bülten Başlığı</label>
                        <input type="text" id="subject" name="subject" class="form-control" placeholder="Bülten Başlığını Girin" required>
                    </div>
                    <div class="form-group">
                        <label for="content">Bülten İçeriği</label>
                        <textarea id="content" name="content" class="form-control" rows="5" placeholder="Bülten İçeriğini Girin" required></textarea>
                    </div>
                    <button type="submit" name="send_bulletin" class="btn btn-primary">Bülteni Gönder</button>
                </form>
            </div>


            <!-- Div İçerik Bitişi -->


          </div>
        </div>
      </div>
    </div>




  </div>
</div>
<!-- /page content -->

<?php include 'footer.php'; ?>
