<?php 
error_reporting(0); // hata gösterimini kapatmak icin kullanılır
include 'header.php';

$blog_kategorisor=$db->prepare("SELECT * FROM blog_kategori");
$blog_kategorisor->execute();
?>


        <!-- page content -->
        <div class="right_col" role="main">
          <div class="">
            
            <div class="clearfix"></div>
            <div class="row">
              <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel">
                  <div class="x_title">
                    <h2><strong>Blog</strong> Ekleme Ekranı<small>
                      
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
                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="first-name">Blog Adı <span class="required">*</span>
                        </label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                          <input type="text" id="first-name" required="required" name="blog_ad" placeholder="Lütfen proje adı giriniz. " class="form-control col-md-7 col-xs-12">
                        </div>
                      </div>

                      <div class="form-group">
                                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="kategori_id">Hangi Kategoriye Ait <span class="required">*</span></label>
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <select name="blog_kategori_id" class="form-control" required>
                                        <?php 
                                            while ($blogkategoricek = $blog_kategorisor->fetch(PDO::FETCH_ASSOC)) { 
                                        ?>
                                            <option value="<?php echo $blogkategoricek['blogkategori_id']; ?>">
                                                <?php echo $blogkategoricek['kategori_ad']; ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>

                      <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="first-name">Yazar Adı <span class="required">*</span>
                        </label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                          <input type="text" id="first-name" required="required" name="yazar_ad" placeholder="Lütfen marka adı giriniz. " class="form-control col-md-7 col-xs-12">
                        </div>
                      </div>

                      <!-- Ck Editör Başlangıç -->

                                  <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="first-name">Blog Açıklama <span class="required">*</span></label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <textarea id="editor1" placeholder="Lütfen açıklama bilgisi giriniz." name="blog_aciklama"></textarea>
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
                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="first-name">Blog Resim: <span class="required">*</span>
                        </label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                          <input type="file" id="first-name" required="required" name="blog_resimyol" class="form-control col-md-7 col-xs-12">
                        </div>
                      </div>   

                      <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="first-name">Blog Tarih <span class="required">*</span>
                        </label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                          <input type="text" id="first-name" required="required" name="blog_tarih" placeholder="Lütfen bilgisini giriniz. " class="form-control col-md-7 col-xs-12">
                        </div>
                      </div>           
                                   
                      <div class="form-group">
                        <div align="right" class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
                          <button type="submit" class="btn btn-success" name="blogkaydet">Kaydet</button>
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