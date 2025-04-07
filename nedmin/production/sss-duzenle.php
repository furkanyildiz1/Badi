<?php 
include 'header.php';

// Check if sss_id is provided
if(!isset($_GET['sss_id'])) {
    header("Location:sss.php?durum=no");
    exit;
}

// Get SSS details
$ssssor = $db->prepare("SELECT * FROM sss WHERE sss_id=:id");
$ssssor->execute(['id' => $_GET['sss_id']]);
$ssscek = $ssssor->fetch(PDO::FETCH_ASSOC);

// Check if record exists
if(!$ssscek) {
    header("Location:sss.php?durum=no");
    exit;
}
?>

<!-- page content -->
<div class="right_col" role="main">
  <div class="">
    <div class="clearfix"></div>
    <div class="row">
      <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="x_panel">
          <div class="x_title">
            <h2>Sıkça Sorulan Soru Düzenleme <small>
              <?php 
              if($_GET['durum']=="ok") { ?>
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
              <?php } else if($_GET['durum']=="no") { ?>
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
            <form action="../netting/islem.php" method="POST" id="demo-form2" data-parsley-validate class="form-horizontal form-label-left">

              <input type="hidden" name="sss_id" value="<?php echo $ssscek['sss_id']; ?>">
              
              <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="sss_soru">Soru <span class="required">*</span>
                </label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                  <input type="text" id="sss_soru" name="sss_soru" value="<?php echo $ssscek['sss_soru']; ?>" required="required" class="form-control col-md-7 col-xs-12">
                </div>
              </div>

              <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="sss_aciklama">Açıklama <span class="required">*</span>
                </label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                  <textarea id="sss_aciklama" name="sss_aciklama" required="required" class="form-control col-md-7 col-xs-12" rows="6"><?php echo $ssscek['sss_aciklama']; ?></textarea>
                </div>
              </div>

              <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="sss_sira">Sıra <span class="required">*</span>
                </label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                  <input type="number" id="sss_sira" name="sss_sira" value="<?php echo $ssscek['sss_sira']; ?>" required="required" class="form-control col-md-7 col-xs-12">
                </div>
              </div>

              <div class="ln_solid"></div>
              <div class="form-group">
                <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
                  <a href="sss.php" class="btn btn-default">İptal</a>
                  <button type="submit" name="sssduzenle" class="btn btn-primary">Güncelle</button>
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