<?php 

error_reporting(0);

include 'header.php'; 

//Belirli veriyi seçme işlemi
$blogsor=$db->prepare("SELECT * FROM blog");
$blogsor->execute();


?>


<!-- page content -->
<div class="right_col" role="main">
  <div class="">

    <div class="clearfix"></div>
    <div class="row">
      <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="x_panel">
          <div class="x_title">
            <h2><strong>Blog</strong> Listeleme <small>,

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
            <a href="blog-ekle.php"><button class="btn btn-success btn-xs">Yeni Ekle</button></a>
              </div>
          </div>
          <div class="x_content">

            <!-- Div İçerik Başlangıç -->

            <table id="datatable-responsive" class="table table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%" style="color: black;">
              <thead>
                <tr>
                  <th>S.No</th>
                  <th>Blog Ad</th>
                  <th>Yazar Ad</th>
                  <th>Blog Açıklama</th>
                  <th>Blog Tarih</th>
                  <th>Blog Resim</th>
                  <th>İşlem</th>
                </tr>
              </thead>

              <tbody>

                <?php 
                $say=0;

                while($blogcek=$blogsor->fetch(PDO::FETCH_ASSOC)) { $say++?> <!-- while -> sonuc dogru döndükce bu islemi tekrarlar -->


                <tr>
                  <td width="20px;"><?php echo $say ?></td>
                  <td><?php echo $blogcek['blog_ad'] ?></td>
                  <td><?php echo $blogcek['yazar_ad'] ?></td>
                  <td><?php echo $blogcek['blog_aciklama'] ?></td>
                  <td><?php echo $blogcek['blog_tarih'] ?></td>
                  <td><img width="50px" height="50px" src="../../<?php echo $blogcek['blog_resimyol'] ?>"></td>
                  
                  <td><a href="javascript:void(0);" onclick="silConfirm('<?php echo $blogcek['blog_id']; ?>')">
                    <center><button class="btn btn-danger btn-xs">Sil</button></center>
                  </a></td>

                </tr>



                <?php  }

                ?>


              </tbody>
            </table>

            <!-- Div İçerik Bitişi -->

            <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
              <script>
                function silConfirm(blogId) {
                  Swal.fire({
                    title: 'Emin misiniz?',
                    text: 'Bu Mutluluklarımızyü silmek istediğinizden emin misiniz?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Evet, sil!',
                    cancelButtonText: 'Hayır, iptal et!',
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    reverseButtons: true
                  }).then((result) => {
                    if (result.isConfirmed) {
                      window.location.href = "../netting/islem.php?blog_id=" + blogId + "&blogsil=ok";
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
