<?php 
error_reporting(0); // hata gösterimini kapatmak icin kullanılır
include 'header.php';

$anahakkimizdasor=$db->prepare("SELECT * FROM anahakkimizda WHERE anahakkimizda_id=:anahakkimizda_id");
$anahakkimizdasor->execute([
  'anahakkimizda_id' => $_GET['anahakkimizda_id']
]);
$anahakkimizdacek=$anahakkimizdasor->fetch(PDO::FETCH_ASSOC);

?>


        <!-- page content -->
        <div class="right_col" role="main">
          <div class="">
            
            <div class="clearfix"></div>
            <div class="row">
              <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel">
                  <div class="x_title">
                    <h2><strong>Ana Sayfa</strong> Hakkımızda Ekleme Ekranı<small>
                      
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
                    <ul class="nav navbar-right panel_toolbox">
                      <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                      </li>
                    </ul>
                    <div class="clearfix"></div>
                  </div>
                  <div class="x_content">
                    <br />

                    <!-- / => en kök dizine çık -->
                    <form method="POST" action="../netting/islem.php" enctype="multipart/form-data" id="demo-form2" data-parsley-validate class="form-horizontal form-label-left">
                      <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="first-name">Başlık <span class="required">*</span>
                        </label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                          <input type="text" id="first-name" required="required" name="anahakkimizda_title" value="<?php echo $anahakkimizdacek['anahakkimizda_title']; ?>" placeholder="Lütfen ana sayfa hakkımızda kısmı için başlık giriniz. " class="form-control col-md-7 col-xs-12">
                        </div>
                      </div>

                      <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="first-name">Header <span class="required">*</span>
                        </label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                          <input type="text" id="first-name" required="required" name="anahakkimizda_header" value="<?php echo $anahakkimizdacek['anahakkimizda_title']; ?>" placeholder="Lütfen ana sayfa hakkımızda kısmı için header giriniz. " class="form-control col-md-7 col-xs-12">
                        </div>
                      </div>

                      <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="first-name">Color <span class="required">*</span>
                        </label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                          <input type="text" id="first-name" required="required" name="anahakkimizda_color" value="<?php echo $anahakkimizdacek['anahakkimizda_title']; ?>" placeholder="Lütfen ana sayfa hakkımızda kısmı color için bilgi giriniz. " class="form-control col-md-7 col-xs-12">
                        </div>
                      </div>

                      <input type="hidden" name="anahakkimizda_id" value="<?php echo $anahakkimizdacek['anahakkimizda_id']; ?>">
                      <input type="hidden" name="anahakkimizda_eskiresimyol" value="<?php echo $anahakkimizdacek['anahakkimizda_resimyol']; ?>">

                      <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="first-name">Yüklü Resim<br><span class="required">*</span>
                        </label>
                        <div class="col-md-6 col-sm-6 col-xs-12">

                          <?php 
                          if (strlen($anahakkimizdacek['anahakkimizda_resimyol'])>0) {?>

                          <img width="200"  src="../../<?php echo $anahakkimizdacek['anahakkimizda_resimyol']; ?>">

                          <?php } else {?>


                          <img width="200"  src="../../dimg/logo-yok.png">


                          <?php } ?>

                          
                        </div>
                      </div>

                      <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="first-name">Resim <span class="required">*</span>
                        </label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                          <input type="file" id="first-name" name="anahakkimizda_resimyol" class="form-control col-md-7 col-xs-12">
                        </div>
                      </div>

                      <!-- Ck Editör Başlangıç -->

                                  <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="first-name">İçerik <span class="required">*</span></label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <textarea id="editor1" placeholder="Lütfen ana sayfa hakkımızda kısmı için başlık giriniz." name="anahakkimizda_text"><?php echo $anahakkimizdacek['anahakkimizda_text']; ?></textarea>
                        </div>
                    </div>

                    <script src="https://cdn.ckeditor.com/ckeditor5/38.0.1/classic/ckeditor.js"></script>
                    <script type="text/javascript">
                        ClassicEditor
                            .create(document.querySelector('#editor1'), {
                                ckfinder: {
                                    uploadUrl: '/path/to/your/upload/handler'
                                }
                            })
                            .catch(error => {
                                console.error(error);
                            });
                    </script>

                      <!-- Ck Editör Bitiş -->                  
                                   
                      <div class="form-group">
                        <div align="right" class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
                          <button type="submit" class="btn btn-success" name="anahakkimizdaguncelle">Güncelle</button>
                        </div>
                      </div>
                    </form>

                  </div>
                </div>
              </div>
            </div>           
          </div>
        </div>
        <!-- /page content -->

       <?php include 'footer.php'; ?>