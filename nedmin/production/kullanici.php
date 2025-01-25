<?php 

error_reporting(0);

include 'header.php'; 

//Belirli veriyi seçme işlemi
$kullanicisor=$db->prepare("SELECT * FROM kullanici");
$kullanicisor->execute();


?>


<!-- page content -->
<div class="right_col" role="main">
  <div class="">

    <div class="clearfix"></div>
    <div class="row">
      <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="x_panel">
          <div class="x_title">
            <h2>Kullanıcı Listeleme <small>,

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
            <ul class="nav navbar-right panel_toolbox">
              <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
              </li>
              <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><i class="fa fa-wrench"></i></a>
                <ul class="dropdown-menu" role="menu">
                  <li><a href="#">Settings 1</a>
                  </li>
                  <li><a href="#">Settings 2</a>
                  </li>
                </ul>
              </li>
              <li><a class="close-link"><i class="fa fa-close"></i></a>
              </li>
            </ul>
            <div class="clearfix"></div>
          </div>
          <div class="x_content">


            <!-- Div İçerik Başlangıç -->

            <table id="datatable-responsive" class="table table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
    <thead>
        <tr>
            <th>Kayıt Tarih</th>
            <th>Ad Soyad</th>
            <th>Mail Adresi</th>
            <th>Telefon</th>
            <th></th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        <?php 
        while ($kullanicicek = $kullanicisor->fetch(PDO::FETCH_ASSOC)) { ?>
            <tr>
                <td><?php echo $kullanicicek['kullanici_zaman']; ?></td>
                <td><?php echo $kullanicicek['kullanici_adsoyad']; ?></td>
                <td><?php echo $kullanicicek['kullanici_mail']; ?></td>
                <td><?php echo $kullanicicek['kullanici_gsm']; ?></td>
                <td>
                    <center>
                        <a href="kullanici-duzenle.php?kullanici_id=<?php echo $kullanicicek['kullanici_id']; ?>">
                            <button class="btn btn-primary btn-xs">Düzenle</button>
                        </a>
                    </center>
                </td>
                <td>
                    <center>
                        <button 
                            class="btn btn-danger btn-xs delete-button" 
                            data-url="../netting/islem.php?kullanici_id=<?php echo $kullanicicek['kullanici_id']; ?>&kullanicisil=ok">
                            Sil
                        </button>
                    </center>
                </td>
            </tr>
        <?php } ?>
    </tbody>
</table>

<!-- SweetAlert JavaScript -->
<script>
    // Tüm silme butonlarını seç
    const deleteButtons = document.querySelectorAll('.delete-button');

    deleteButtons.forEach(button => {
        button.addEventListener('click', function () {
            const deleteUrl = this.getAttribute('data-url'); // URL'yi al

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
                    // Onaylanırsa URL'ye yönlendir
                    window.location.href = deleteUrl;
                }
            });
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
