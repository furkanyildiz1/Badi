<?php
error_reporting(0); // hata gösterimini kapatmak icin kullanılır
include 'header.php'; 



$alt_kategorisor=$db->prepare("SELECT * FROM alt_kategoriler WHERE alt_kategori_id=:alt_kategori_id");
$alt_kategorisor->execute([
  'alt_kategori_id' => $_GET['alt_kategori_id'] 
]);
$alt_kategoricek=$alt_kategorisor->fetch(PDO::FETCH_ASSOC);

?>
        <!-- page content -->
        <div class="right_col" role="main">
          <div class="">
            
            <div class="clearfix"></div>
            <div class="row">
              <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel">
                  <div class="x_title">
                    <h2><strong>Alt Kategori</strong> Düzenleme İşlemleri <small>
                      
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
                    <ul class="nav navbar-right panel_toolbox">
                      <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                      </li>
                      <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><i class="fa fa-wrench"></i></a>
                      </li>
                      <li><a class="close-link"><i class="fa fa-close"></i></a>
                      </li>
                    </ul>
                    <div class="clearfix"></div>
                  </div>
                  <div class="x_content">
                    <br />

                    <!-- / => en kök dizine çık -->
                    <form method="POST" action="../netting/islem.php" id="demo-form2" data-parsley-validate class="form-horizontal form-label-left">

                        <!-- id alma islemi -->  <input type="hidden" id="first-name" required="required" name="alt_kategori_id" value="<?php echo $alt_kategoricek['alt_kategori_id']; ?>">

                        <div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12" for="kategori_id">Hangi Kategoriye Ait <span class="required">*</span></label>
                            <div class="col-md-6 col-sm-6 col-xs-12">
                                <select name="kategori_id" class="form-control" required>
                                    <?php 
                                    // Ana kategorileri çekmek için sorgu
                                    $kategorisor = $db->prepare("SELECT * FROM kategoriler");
                                    $kategorisor->execute();

                                    // Kategorileri döngüyle listeleme
                                    while ($kategoricek = $kategorisor->fetch(PDO::FETCH_ASSOC)) { 
                                        // Alt kategorinin mevcut bağlı olduğu kategori ile eşleşmeyi kontrol et
                                        $selected = ($alt_kategoricek['kategori_id'] == $kategoricek['kategori_id']) ? "selected" : "";
                                    ?>
                                        <option value="<?php echo $kategoricek['kategori_id']; ?>" <?php echo $selected; ?>>
                                            <?php echo $kategoricek['ad']; ?>
                                        </option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>


                      <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="first-name">Alt Kategori Ad <span class="required">*</span>
                        </label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                          <input type="text" id="first-name" required="required" name="alt_kategori_ad" value="<?php echo $alt_kategoricek['ad']; ?>" class="form-control col-md-7 col-xs-12">
                        </div>
                      </div>

              <!-- Ck Editör Başlangıç -->

                    <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="first-name">Alt Kategori Detay <span class="required">*</span></label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <textarea id="editor1" name="alt_kategori_aciklama"><?php echo $alt_kategoricek['aciklama']; ?></textarea>
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
                          <button type="submit" class="btn btn-success" name="alt_kategori_duzenle">Güncelle</button>
                        </div>
                      </div>
                    </form>

                    <div class="form-group">
                      <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3" align="right">
                        <a href="alt-kategori.php"><button type="submit" class="btn btn-primary">Geri dön</button></a>
                      </div>
                    </div>

                  </div>
                </div>
              </div>
            </div>        
          </div>
        </div>
        <!-- /page content -->

       <?php include 'footer.php'; ?>