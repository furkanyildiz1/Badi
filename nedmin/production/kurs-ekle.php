<?php
error_reporting(0); // hata gösterimini kapatmak icin kullanılır
include 'header.php'; 
?>
        <!-- page content -->
        <div class="right_col" role="main">
          <div class="">
            
            <div class="clearfix"></div>
            <div class="row">
              <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel">
                  <div class="x_title">
                    <h2><strong>Kurs</strong> Ekleme İşlemleri <small></small></h2>
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

                    <!-- / => en kök dizine çık -->

                    <form method="POST" action="../netting/islem.php" enctype="multipart/form-data" id="demo-form2" data-parsley-validate class="form-horizontal form-label-left">

                      <input type="hidden" name="eskikurs_resimyol" value="<?php echo $eskikurscek['resimyol']; ?>">
                      <input type="hidden" name="eskikurs_videoyol" value="<?php echo $eskikurscek['videoyol']; ?>">

                      <div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12" for="kategori_id">Hangi Üst Kategoriye Ait <span class="required">*</span></label>
                            <div class="col-md-6 col-sm-6 col-xs-12">
                                <select name="kategori_id" class="form-control" required>
                                        <?php 
                                            while ($kategoricek = $kategorisor->fetch(PDO::FETCH_ASSOC)) { 
                                        ?>
                                            <option value="<?php echo $kategoricek['kategori_id']; ?>">
                                                <?php echo $kategoricek['ad']; ?>
                                            </option>
                                        <?php } ?>
                                </select>
                            </div>
                      </div>

                      <div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12" for="kategori_id">Hangi Alt Kategoriye Ait <span class="required">*</span></label>
                            <div class="col-md-6 col-sm-6 col-xs-12">
                                <select name="alt_kategori_id" class="form-control" required>
                                        <?php 
                                            while ($altkategoricek = $altkategorisor->fetch(PDO::FETCH_ASSOC)) { 
                                        ?>
                                            <option value="<?php echo $altkategoricek['alt_kategori_id']; ?>">
                                                <?php echo $altkategoricek['ad']; ?>
                                            </option>
                                        <?php } ?>
                                </select>
                            </div>
                      </div>

                      <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="first-name">Kurs Başlık <span class="required">*</span>
                        </label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                          <input type="text" id="first-name" required="required" name="kurs_baslik"  class="form-control col-md-7 col-xs-12" placeholder="Lütfen Kurs Adını Giriniz">
                        </div>
                      </div>

                     <!-- Ck Editör Başlangıç -->

                                  <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="first-name">Kurs Açıklama <span class="required">*</span></label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <textarea id="editor1" name="kurs_aciklama"placeholder="Lütfen Kategori Açıklamasını Giriniz"></textarea>
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
                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="first-name">Kurs Süresini Giriniz <span class="required">*</span>
                        </label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                          <input type="text" id="first-name" required="required" name="kurs_sure"  class="form-control col-md-7 col-xs-12" placeholder="Örn: 300 Saat">
                        </div>
                      </div>  

                      <div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12" for="kategori_id">Lütfen Eğitmen Seçiniz <span class="required">*</span></label>
                            <div class="col-md-6 col-sm-6 col-xs-12">
                                <select name="egitmen_id" class="form-control" required>
                                        <?php 
                                            while ($egitmencek = $egitmensor->fetch(PDO::FETCH_ASSOC)) { 
                                        ?>
                                            <option value="<?php echo $egitmencek['egitmen_id']; ?>">
                                                <?php echo $egitmencek['egitmen_adsoyad']; ?>
                                            </option>
                                        <?php } ?>
                                </select>
                            </div>
                      </div>

                      <div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12" for="kategori_id">Hangi Seviyeye Ait<span class="required">*</span></label>
                            <div class="col-md-6 col-sm-6 col-xs-12">
                                <select name="kurs_seviye_id" class="form-control" required>
                                        <?php 
                                            while ($seviyecek = $seviyesor->fetch(PDO::FETCH_ASSOC)) { 
                                        ?>
                                            <option value="<?php echo $seviyecek['kurs_seviye_id']; ?>">
                                                <?php echo $seviyecek['seviye_ad']; ?>
                                            </option>
                                        <?php } ?>
                                </select>
                            </div>
                      </div>

                      <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="first-name">Kurs Resmi (1 Adet) <span class="required">*</span>
                        </label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                          <input type="file" id="first-name" required="required" name="kurs_resimyol"  class="form-control col-md-7 col-xs-12">
                        </div>
                      </div>

                      <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="first-name">Kurs Ön İzleme Videosu <span class="required">*</span>
                        </label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                          <input type="file" id="first-name" required="required" name="kurs_videoyol"  class="form-control col-md-7 col-xs-12">
                        </div>
                      </div>

                      <!-- Update the certificate pricing section -->
                      <div class="x_title">
                        <h2><strong>Sertifika ve Transkript</strong> Fiyatlandırması <small>(En düşük sertifika fiyatı, kursun temel fiyatı olarak gösterilecektir)</small></h2>
                        <div class="clearfix"></div>
                      </div>

                      <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="edevlet_cert_price">E-Devlet Sertifika Fiyatı
                        </label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                          <input type="text" id="edevlet_cert_price" name="edevlet_cert_price" class="form-control col-md-7 col-xs-12" placeholder="E-Devlet Sertifika Fiyatını Giriniz">
                          <small class="text-muted">Bu fiyat kursun başlangıç fiyatı olarak kullanılabilir.</small>
                        </div>
                      </div>

                      <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="eng_cert_price">İngilizce Sertifika Fiyatı
                        </label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                          <input type="text" id="eng_cert_price" name="eng_cert_price" class="form-control col-md-7 col-xs-12" placeholder="İngilizce Sertifika Fiyatını Giriniz">
                          <small class="text-muted">Bu fiyat kursun başlangıç fiyatı olarak kullanılabilir.</small>
                        </div>
                      </div>

                      <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="tr_cert_price">Türkçe Sertifika Fiyatı
                        </label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                          <input type="text" id="tr_cert_price" name="tr_cert_price" class="form-control col-md-7 col-xs-12" placeholder="Türkçe Sertifika Fiyatını Giriniz">
                          <small class="text-muted">Bu fiyat kursun başlangıç fiyatı olarak kullanılabilir.</small>
                        </div>
                      </div>

                      <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="eng_transcript_price">İngilizce Transkript Fiyatı
                        </label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                          <input type="text" id="eng_transcript_price" name="eng_transcript_price" class="form-control col-md-7 col-xs-12" placeholder="İngilizce Transkript Fiyatını Giriniz">
                        </div>
                      </div>

                      <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="tr_transcript_price">Türkçe Transkript Fiyatı
                        </label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                          <input type="text" id="tr_transcript_price" name="tr_transcript_price" class="form-control col-md-7 col-xs-12" placeholder="Türkçe Transkript Fiyatını Giriniz">
                        </div>
                      </div>

                      <div class="form-group">
                        <div align="right" class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
                          <button type="submit" class="btn btn-success" name="kurskaydet">Kaydet</button>
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