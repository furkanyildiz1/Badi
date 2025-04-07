<?php 
error_reporting(0); // hata gösterimini kapatmak icin kullanılır
include 'header.php'; 

// KVKK ve Gizlilik verilerini çek
$kvkksor = $db->prepare("SELECT * FROM kvkk_gizlilik WHERE id=:id");
$kvkksor->execute([
    'id' => 1
]);
$kvkkcek = $kvkksor->fetch(PDO::FETCH_ASSOC);
?>

<!-- Replace CKEditor 4 with CKEditor 5 like in hakkimizda.php -->
<script src="https://cdn.ckeditor.com/ckeditor5/38.0.1/classic/ckeditor.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

<style>
.nav-tabs > li.active > a, .nav-tabs > li.active > a:focus, .nav-tabs > li.active > a:hover {
    background-color: #26B99A;
    color: white;
    border: 1px solid #26B99A;
}

.nav-tabs > li > a {
    color: #73879C;
    font-weight: 600;
    padding: 10px 20px;
}

.tab-content {
    padding: 20px;
    border: 1px solid #ddd;
    border-top: none;
    background-color: white;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}

.form-section {
    margin-bottom: 25px;
    padding: 15px;
    background-color: #f9f9f9;
    border-radius: 5px;
    border-left: 3px solid #26B99A;
}

.form-section h4 {
    color: #2A3F54;
    font-weight: 600;
    margin-bottom: 15px;
}

.btn-success {
    padding: 8px 20px;
    font-weight: 600;
}

.editor-container {
    margin-bottom: 25px;
}

.editor-label {
    font-weight: 600;
    margin-bottom: 10px;
    color: #2A3F54;
    display: block;
}

.required {
    color: #E74C3C;
}

/* Updated styles for CKEditor 5 */
.ck-editor__editable {
    min-height: 300px;
}
</style>

<!-- page content -->
<div class="right_col" role="main">
  <div class="">
    <div class="page-title">
      <div class="title_left">
        <h3><i class="fa fa-shield-alt"></i> KVKK ve Gizlilik Politikası Yönetimi</h3>
      </div>
    </div>
    
    <div class="clearfix"></div>
    
    <div class="row">
      <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="x_panel">
          <div class="x_title">
            <h2>Yasal Metin Yönetimi <small>
              
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
                      <li><a class="close-link"><i class="fa fa-close"></i></a>
                      </li>
                    </ul>
                    <div class="clearfix"></div>
                  </div>
                  <div class="x_content">
                    <br />

                    <!-- / => en kök dizine çık -->
                    <form method="POST" action="../netting/islem.php" id="demo-form2" data-parsley-validate class="form-horizontal form-label-left">
                      <ul class="nav nav-tabs">
                        <li class="active"><a data-toggle="tab" href="#kvkk"><i class="fa fa-shield-alt"></i> KVKK Metni</a></li>
                        <li><a data-toggle="tab" href="#gizlilik"><i class="fa fa-user-shield"></i> Gizlilik Politikası</a></li>
                      </ul>
                      
                      <div class="tab-content">
                        <!-- KVKK Tab -->
                        <div role="tabpanel" class="tab-pane active" id="kvkk">
                          <div class="form-section">
                            <h4><i class="fa fa-shield-alt"></i> KVKK Metni</h4>
                            <p class="text-muted">6698 sayılı Kişisel Verilerin Korunması Kanunu kapsamında site ziyaretçilerine sunulacak KVKK bilgilendirme metni.</p>
                            
                            <div class="editor-container">
                              <label class="editor-label">KVKK Metni İçeriği <span class="required">*</span></label>
                              <textarea id="kvkk_metin" name="kvkk_metin"><?php echo $kvkkcek['kvkk_metin']; ?></textarea>
                            </div>
                          </div>
                        </div>
                        
                        <!-- Gizlilik Politikası Tab -->
                        <div role="tabpanel" class="tab-pane" id="gizlilik">
                          <div class="form-section">
                            <h4><i class="fa fa-user-shield"></i> Gizlilik Politikası</h4>
                            <p class="text-muted">Kullanıcı verilerinin nasıl işlendiği ve korunduğu hakkında bilgilendirme metni.</p>
                            
                            <div class="editor-container">
                              <label class="editor-label">Gizlilik Politikası İçeriği <span class="required">*</span></label>
                              <textarea id="gizlilik_politikasi" name="gizlilik_politikasi"><?php echo $kvkkcek['gizlilik_politikasi']; ?></textarea>
                            </div>
                          </div>
                        </div>
                      </div>
                      
                      <div class="ln_solid"></div>
                      <div class="form-group">
                        <div class="col-md-12 text-right">
                          <button type="submit" class="btn btn-success" name="kvkkgizlilikayarkaydet"><i class="fa fa-save"></i> Değişiklikleri Kaydet</button>
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

<!-- Initialize CKEditor 5 using the same approach as hakkimizda.php -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize CKEditor 5 for each textarea
    const editorPromises = [
        ClassicEditor.create(document.querySelector('#kvkk_metin'), {
            ckfinder: {
                uploadUrl: '/path/to/your/upload/handler'
            }
        }),
        ClassicEditor.create(document.querySelector('#gizlilik_politikasi'), {
            ckfinder: {
                uploadUrl: '/path/to/your/upload/handler'
            }
        }),
    ];
    
    // Handle any initialization errors
    Promise.all(editorPromises)
        .then(editors => {
            console.log('All CKEditor 5 instances initialized successfully');
        })
        .catch(error => {
            console.error('Error initializing CKEditor 5:', error);
        });
});
</script>

<?php include 'footer.php'; ?>