<?php 
error_reporting(0); // hata gösterimini kapatmak icin kullanılır
include 'header.php';

$kurs_id=$_GET['kurs_id'];

$kurssor=$db->prepare("SELECT * FROM kurslar WHERE kurs_id=:kurs_id");
$kurssor->execute([
  'kurs_id' => $kurs_id
]);
$kurscek=$kurssor->fetch(PDO::FETCH_ASSOC);

?>

<!-- page content -->
<div class="right_col" role="main">
  <div class="">
    <div class="clearfix"></div>
    <div class="row">
      <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="x_panel">
          <div class="x_title">
            <h2><strong><?php echo $kurscek['baslik'] ?></strong> -> İçerik Ekleme Ekranı<small>
            </small></h2>
            <ul class="nav navbar-right panel_toolbox">
              <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a></li>
            </ul>
            <div class="clearfix"></div>
          </div>
          <div class="x_content">
            <br />
            <!-- Form Başlangıcı -->
            <form method="POST" action="../netting/islem.php" enctype="multipart/form-data" id="dynamic-form" data-parsley-validate class="form-horizontal form-label-left">

              <input type="hidden" name="kurs_id" value="<?php echo $kurscek['kurs_id']; ?>">

              <div id="content-container">
                <!-- İçerik Grubu -->
                <div class="content-group">

                  <div class="form-group">
                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="icerik_ad">İçerik Adı <span class="required">*</span></label>
                    <div class="col-md-6 col-sm-6 col-xs-12">
                      <input type="text" required="required" name="icerikler[0][icerik_ad]" placeholder="Lütfen İçerik Adını Giriniz." class="form-control col-md-7 col-xs-12">
                    </div>
                  </div>

                   <!-- Ck Editör Başlangıç -->

                                  <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="first-name">İçerik Açıklama <span class="required">*</span></label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <textarea id="editor1" name="icerikler[0][icerik_aciklama]" placeholder="Lütfen İçerik Bilgisini Giriniz"></textarea>
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
                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="icerik_ders_sayi">İçerik Ders Sayısı <span class="required">*</span></label>
                    <div class="col-md-6 col-sm-6 col-xs-12">
                      <input type="text" required="required" name="icerikler[0][icerik_ders_sayi]" placeholder="Lütfen İçerik Ders Sayısı Giriniz." class="form-control col-md-7 col-xs-12">
                    </div>
                  </div>
                </div>
              </div>

              <div class="form-group">
                <div align="right" class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
                  <button type="button" id="add-content" class="btn btn-primary">Ekle</button>
                  <button type="submit" class="btn btn-success" name="kursicerikkaydet">Kaydet</button>
                </div>
              </div>
            </form>
            <!-- Form Bitişi -->
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<!-- /page content -->

<?php include 'footer.php'; ?>

<script>
  let contentCount = 1; // Dinamik içerik sayacı

  document.getElementById('add-content').addEventListener('click', function() {
    const container = document.getElementById('content-container');

    const newGroup = document.createElement('div');
    newGroup.className = 'content-group';

    newGroup.innerHTML = `
      <div class="form-group">
        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="icerik_ad">İçerik Adı <span class="required">*</span></label>
        <div class="col-md-6 col-sm-6 col-xs-12">
          <input type="text" required="required" name="icerikler[${contentCount}][icerik_ad]" placeholder="Lütfen proje adı giriniz." class="form-control col-md-7 col-xs-12">
        </div>
      </div>
      <div class="form-group">
        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="icerik_ders_sayi">İçerik Ders Sayısı <span class="required">*</span></label>
        <div class="col-md-6 col-sm-6 col-xs-12">
          <input type="text" required="required" name="icerikler[${contentCount}][icerik_ders_sayi]" placeholder="Lütfen ders sayısı giriniz." class="form-control col-md-7 col-xs-12">
        </div>
      </div>
    `;

    container.appendChild(newGroup);
    contentCount++; // Sayaç artırılır
  });
</script>
