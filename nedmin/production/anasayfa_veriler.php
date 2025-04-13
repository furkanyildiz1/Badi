<?php 
error_reporting(0); // hata gösterimini kapatmak icin kullanılır
include 'header.php'; 

// Verileri çekme
$anasayfasor = $db->prepare("SELECT * FROM anasayfa_veri WHERE id=:id");
$anasayfasor->execute([
    'id' => 1
]);
$anasayfacek = $anasayfasor->fetch(PDO::FETCH_ASSOC);
?>
        <!-- page content -->
        <div class="right_col" role="main">
          <div class="">
            
            <div class="clearfix"></div>
            <div class="row">
              <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel">
                  <div class="x_title">
                    <h2>Ana Sayfa Verileri <small>
                      
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
                  </div>
                  <div class="x_content">
                    <br />

                    <!-- / => en kök dizine çık -->
                    <form method="POST" action="../netting/islem.php" id="demo-form2" data-parsley-validate class="form-horizontal form-label-left">
                    
                      <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="toplam_ogrenci">Toplam Öğrenci Sayısı <span class="required">*</span>
                        </label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                          <input type="number" id="toplam_ogrenci" required="required" name="toplam_ogrenci" value="<?php echo $anasayfacek['toplam_ogrenci']; ?>" class="form-control col-md-7 col-xs-12">
                        </div>
                      </div>

                      <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="toplam_kurs">Toplam Kurs Sayısı <span class="required">*</span>
                        </label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                          <input type="number" id="toplam_kurs" required="required" name="toplam_kurs" value="<?php echo $anasayfacek['toplam_kurs']; ?>" class="form-control col-md-7 col-xs-12">
                        </div>
                      </div>

                      <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="mutlu_ogrenci">Mutlu Öğrenci Sayısı <span class="required">*</span>
                        </label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                          <input type="number" id="mutlu_ogrenci" required="required" name="mutlu_ogrenci" value="<?php echo $anasayfacek['mutlu_ogrenci']; ?>" class="form-control col-md-7 col-xs-12">
                        </div>
                      </div>

                      <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="deneyim">Deneyim (Yıl) <span class="required">*</span>
                        </label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                          <input type="number" id="deneyim" required="required" name="deneyim" value="<?php echo $anasayfacek['deneyim']; ?>" class="form-control col-md-7 col-xs-12">
                        </div>
                      </div>

                      <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="olumlu_yorum">Olumlu Yorum Sayısı <span class="required">*</span>
                        </label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                          <input type="number" id="olumlu_yorum" required="required" name="olumlu_yorum" value="<?php echo $anasayfacek['olumlu_yorum']; ?>" class="form-control col-md-7 col-xs-12">
                        </div>
                      </div>

                      <hr>
                      <h4 class="text-center">Yüzdelik Oranlar</h4>
                      <hr>

                      <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="yuzdelik1_isim">1. Yüzdelik İsmi <span class="required">*</span>
                        </label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                          <input type="text" id="yuzdelik1_isim" required="required" name="yuzdelik1_isim" value="<?php echo $anasayfacek['yuzdelik1_isim']; ?>" class="form-control col-md-7 col-xs-12">
                        </div>
                      </div>

                      <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="yuzdelik1_yuzde">1. Yüzdelik Oranı <span class="required">*</span>
                        </label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                          <input type="number" id="yuzdelik1_yuzde" required="required" min="0" max="100" name="yuzdelik1_yuzde" value="<?php echo $anasayfacek['yuzdelik1_yuzde']; ?>" class="form-control col-md-7 col-xs-12">
                        </div>
                      </div>

                      <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="yuzdelik2_isim">2. Yüzdelik İsmi <span class="required">*</span>
                        </label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                          <input type="text" id="yuzdelik2_isim" required="required" name="yuzdelik2_isim" value="<?php echo $anasayfacek['yuzdelik2_isim']; ?>" class="form-control col-md-7 col-xs-12">
                        </div>
                      </div>

                      <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="yuzdelik2_yuzde">2. Yüzdelik Oranı <span class="required">*</span>
                        </label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                          <input type="number" id="yuzdelik2_yuzde" required="required" min="0" max="100" name="yuzdelik2_yuzde" value="<?php echo $anasayfacek['yuzdelik2_yuzde']; ?>" class="form-control col-md-7 col-xs-12">
                        </div>
                      </div>

                      <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="yuzdelik3_isim">3. Yüzdelik İsmi <span class="required">*</span>
                        </label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                          <input type="text" id="yuzdelik3_isim" required="required" name="yuzdelik3_isim" value="<?php echo $anasayfacek['yuzdelik3_isim']; ?>" class="form-control col-md-7 col-xs-12">
                        </div>
                      </div>

                      <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="yuzdelik3_yuzde">3. Yüzdelik Oranı <span class="required">*</span>
                        </label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                          <input type="number" id="yuzdelik3_yuzde" required="required" min="0" max="100" name="yuzdelik3_yuzde" value="<?php echo $anasayfacek['yuzdelik3_yuzde']; ?>" class="form-control col-md-7 col-xs-12">
                        </div>
                      </div>
                                   
                      <div class="form-group">
                        <div align="right" class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
                          <button type="submit" class="btn btn-success" name="anasayfaveriayarkaydet">Güncelle</button>
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