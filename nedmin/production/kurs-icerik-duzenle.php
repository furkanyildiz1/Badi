<?php 
include 'header.php';

// Get kurs_id from URL
$kurs_id = $_GET['kurs_id'];

// Get modules for this course
$modulsor = $db->prepare("SELECT * FROM kurs_modulleri WHERE kurs_id = ? ORDER BY modul_sira");
$modulsor->execute([$kurs_id]);

// Get course info
$kurssor = $db->prepare("SELECT * FROM kurslar WHERE kurs_id = ?");
$kurssor->execute([$kurs_id]);
$kurscek = $kurssor->fetch(PDO::FETCH_ASSOC);
?>

<!-- page content -->
<div class="right_col" role="main">
  <div class="">
    <div class="clearfix"></div>
    <div class="row">
      <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="x_panel">
          <div class="x_title">
            <h2><?php echo $kurscek['baslik']; ?> - <small>İçerik Düzenleme</small></h2>
            <div class="clearfix"></div>
            <div align="right">
              <button class="btn btn-success btn-sm" data-toggle="modal" data-target="#addModulModal">
                <i class="fa fa-plus"></i> Yeni Modül Ekle
              </button>
            </div>
          </div>

          <div class="x_content">
            <div class="accordion" id="moduleAccordion">
              <?php 
              while($modulcek = $modulsor->fetch(PDO::FETCH_ASSOC)) {
                // Get sections for this module
                $bolumsor = $db->prepare("SELECT * FROM kurs_bolumleri WHERE modul_id = ? ORDER BY bolum_sira");
                $bolumsor->execute([$modulcek['modul_id']]);
              ?>
                <div class="card">
                  <div class="card-header" id="heading<?php echo $modulcek['modul_id']; ?>">
                    <div class="row">
                      <div class="col-md-8">
                        <h2 class="mb-0">
                          <button class="btn btn-link" type="button" data-toggle="collapse" 
                            data-target="#collapse<?php echo $modulcek['modul_id']; ?>">
                            <?php echo $modulcek['modul_ad']; ?>
                          </button>
                        </h2>
                      </div>
                      <div class="col-md-4 text-right">
                        <button class="btn btn-primary btn-sm" onclick="editModul(<?php echo $modulcek['modul_id']; ?>)">
                          <i class="fa fa-edit"></i> Düzenle
                        </button>
                        <button class="btn btn-danger btn-sm" onclick="deleteModul(<?php echo $modulcek['modul_id']; ?>)">
                          <i class="fa fa-trash"></i> Sil
                        </button>
                        <button class="btn btn-success btn-sm" onclick="addSection(<?php echo $modulcek['modul_id']; ?>)">
                          <i class="fa fa-plus"></i> Ders Ekle
                        </button>
                      </div>
                    </div>
                  </div>

                  <div id="collapse<?php echo $modulcek['modul_id']; ?>" class="collapse" 
                    data-parent="#moduleAccordion">
                    <div class="card-body">
                      <table class="table table-striped">
                        <thead>
                          <tr>
                            <th>Sıra</th>
                            <th>Ders Adı</th>
                            <th>Süre</th>
                            <th>İşlemler</th>
                          </tr>
                        </thead>
                        <tbody>
                          <?php while($bolumcek = $bolumsor->fetch(PDO::FETCH_ASSOC)) { ?>
                            <tr>
                              <td><?php echo $bolumcek['bolum_sira']; ?></td>
                              <td><?php echo $bolumcek['bolum_ad']; ?></td>
                              <td><?php echo $bolumcek['bolum_sure_saat']; ?> saat <?php echo $bolumcek['bolum_sure_dakika']; ?> dakika</td>
                              <td>
                                <button class="btn btn-primary btn-xs" 
                                  onclick="editSection(<?php echo $bolumcek['bolum_id']; ?>)">
                                  <i class="fa fa-edit"></i>
                                </button>
                                <button class="btn btn-danger btn-xs" 
                                  onclick="deleteSection(<?php echo $bolumcek['bolum_id']; ?>)">
                                  <i class="fa fa-trash"></i>
                                </button>
                              </td>
                            </tr>
                          <?php } ?>
                        </tbody>
                      </table>
                    </div>
                  </div>
                </div>
              <?php } ?>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Add Module Modal -->
<div class="modal fade" id="addModulModal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Yeni Modül Ekle</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form action="../netting/islem.php" method="POST">
        <div class="modal-body">
          <input type="hidden" name="kurs_id" value="<?php echo $kurs_id; ?>">
          <div class="form-group">
            <label>Modül Adı</label>
            <input type="text" class="form-control" name="modul_ad" required>
          </div>
          <div class="form-group">
            <label>Sıra</label>
            <input type="number" class="form-control" name="modul_sira" required>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Kapat</button>
          <button type="submit" name="modul_ekle" class="btn btn-primary">Kaydet</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Edit Module Modal -->
<div class="modal fade" id="editModulModal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Modül Düzenle</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form action="../netting/islem.php" method="POST">
        <div class="modal-body">
          <input type="hidden" name="modul_id" id="edit_modul_id">
          <div class="form-group">
            <label>Modül Adı</label>
            <input type="text" class="form-control" name="modul_ad" id="edit_modul_ad" required>
          </div>
          <div class="form-group">
            <label>Sıra</label>
            <input type="number" class="form-control" name="modul_sira" id="edit_modul_sira" required>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Kapat</button>
          <button type="submit" name="modul_duzenle" class="btn btn-primary">Güncelle</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Add Section Modal -->
<div class="modal fade" id="addSectionModal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Yeni Ders Ekle</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form action="../netting/islem.php" method="POST">
        <div class="modal-body">
          <input type="hidden" name="modul_id" id="add_section_modul_id">
          <input type="hidden" name="kurs_id" value="<?php echo $kurs_id; ?>">
          <input type="hidden" id="bolum_id" name="bolum_id" value="">
          <div class="form-group">
            <label>Ders Adı</label>
            <input type="text" class="form-control" name="bolum_ad" required>
          </div>
          <div class="form-group">
            <label>Süre</label>
            <div class="row">
              <div class="col-md-6">
                <div class="input-group">
                  <input type="number" class="form-control" name="bolum_sure_saat" min="0" max="99" placeholder="0" value="0">
                  <div class="input-group-append">
                    <span class="input-group-text">saat</span>
                  </div>
                </div>
              </div>
              <div class="col-md-6">
                <div class="input-group">
                  <input type="number" class="form-control" name="bolum_sure_dakika" min="0" max="59" placeholder="0" value="0">
                  <div class="input-group-append">
                    <span class="input-group-text">dakika</span>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="form-group">
            <label>Sıra</label>
            <input type="number" class="form-control" name="bolum_sira" required>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Kapat</button>
          <button type="submit" name="bolum_ekle" class="btn btn-primary">Kaydet</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Edit Section Modal -->
<div class="modal fade" id="editSectionModal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Ders Düzenle</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form action="../netting/islem.php" method="POST">
        <div class="modal-body">
          <input type="hidden" name="bolum_id" id="edit_bolum_id">
          <input type="hidden" name="kurs_id" value="<?php echo $kurs_id; ?>">
          <div class="form-group">
            <label>Ders Adı</label>
            <input type="text" class="form-control" name="bolum_ad" id="edit_bolum_ad" required>
          </div>
          <div class="form-group">
            <label>Süre</label>
            <div class="row">
              <div class="col-md-6">
                <div class="input-group">
                  <input type="number" class="form-control" name="bolum_sure_saat" id="edit_bolum_sure_saat" min="0" max="99" placeholder="0">
                  <div class="input-group-append">
                    <span class="input-group-text">saat</span>
                  </div>
                </div>
              </div>
              <div class="col-md-6">
                <div class="input-group">
                  <input type="number" class="form-control" name="bolum_sure_dakika" id="edit_bolum_sure_dakika" min="0" max="59" placeholder="0">
                  <div class="input-group-append">
                    <span class="input-group-text">dakika</span>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="form-group">
            <label>Sıra</label>
            <input type="number" class="form-control" name="bolum_sira" id="edit_bolum_sira" required>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Kapat</button>
          <button type="submit" name="bolum_duzenle" class="btn btn-primary">Güncelle</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Video Yükleme Yöntemi Modal -->
<div class="modal fade" id="bulkAddLessonModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Video Yükleme Yöntemi</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="upload-methods">
                    <div class="upload-method" onclick="selectUploadMethod('local')">
                        <i class='fa fa-laptop'></i>
                        <h6>Bilgisayarımdan Yükle</h6>
                        <p>Videolarınızı bilgisayarınızdan seçerek yükleyin</p>
                    </div>
                    <div class="upload-method" onclick="selectUploadMethod('bunny')">
                        <i class='fa fa-cloud'></i>
                        <h6>Sistemden Video Yükle</h6>
                        <p>Sistemde kayıtlı videoları içe aktarın</p>
                    </div>
                    <div class="upload-method" onclick="selectUploadMethod('youtube')">
                        <i class='fa fa-youtube'></i>
                        <h6>YouTube'dan İçe Aktar</h6>
                        <p>YouTube video veya oynatma listesi URL'si ile içe aktarın</p>
                    </div>
                    <div class="upload-method" onclick="selectUploadMethod('vimeo')">
                        <i class='fa fa-vimeo'></i>
                        <h6>Vimeo'dan İçe Aktar</h6>
                        <p>Vimeo video veya koleksiyon URL'si ile içe aktarın</p>
                    </div>
                    <div class="upload-method" onclick="selectUploadMethod('url')">
                        <i class='fa fa-link'></i>
                        <h6>URL ile Ekle</h6>
                        <p>Video veya içerik URL'si ile ekleyin</p>
                    </div>
                    <div class="upload-method" onclick="selectUploadMethod('h5p')">
                        <i class='fa fa-cube'></i>
                        <h6>H5P İçeriği Yükle</h6>
                        <p>Etkileşimli H5P içeriği ekleyin</p>
                    </div>
                    <div class="upload-method" onclick="selectUploadMethod('scorm')">
                        <i class='fa fa-archive'></i>
                        <h6>SCORM Paketi Yükle</h6>
                        <p>SCORM uyumlu paketleri yükleyin</p>
                    </div>
                    <div class="upload-method" onclick="selectUploadMethod('embed')">
                        <i class='fa fa-code'></i>
                        <h6>Embed Kodu ile Ekle</h6>
                        <p>HTML embed kodu ile içerik ekleyin</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bilgisayardan Video Yükleme Modal -->
<div class="modal fade" id="localUploadModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Bilgisayardan Video Yükle</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="../netting/islem.php" method="POST" enctype="multipart/form-data" id="localUploadForm">
                <div class="modal-body">
                    <input type="hidden" name="bulk_lesson_add" value="1">
                    <input type="hidden" name="course_id" value="<?= $kurs_id ?>">
                    <input type="hidden" name="section_id" id="local_section_id" value="">
                    <input type="hidden" name="bolum_id" id="bolum_id">
                    
                    <div class="alert alert-info">
                        <i class="fa fa-info-circle"></i> Video dosyalarını seçin. Her dosya için ayrı başlık girebilirsiniz.
                    </div>
                    
                    <div class="form-group">
                        <label>Video Dosyaları (MP4)</label>
                        <input type="file" class="form-control" name="videos[]" accept="video/mp4" multiple onchange="previewVideos(this)">
                    </div>
                    
                    <div class="video-preview-list" id="videoPreviewList">
                        <!-- Buraya JavaScript ile video önizlemeleri eklenecek -->
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">İptal</button>
                    <button type="submit" class="btn btn-primary">Yükle</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- YouTube Video İçe Aktarma Modal -->
<div class="modal fade" id="youtubeUploadModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">YouTube'dan Video İçe Aktar</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="mb-4">
                    <div class="alert alert-info">
                        <i class="fa fa-info-circle"></i> YouTube video veya oynatma listesi URL'sini yapıştırın.
                    </div>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fa fa-youtube"></i></span>
                        </div>
                        <input type="text" id="youtubeUrl" class="form-control" placeholder="https://www.youtube.com/watch?v=..." onchange="fetchYouTubeVideo()">
                        <div class="input-group-append">
                            <button class="btn btn-outline-secondary" type="button" onclick="fetchYouTubeVideo()">Getir</button>
                        </div>
                    </div>
                </div>
                
                <div id="youtubeVideoList" class="video-list">
                    <!-- Buraya JavaScript ile video detayları eklenecek -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">İptal</button>
                <button type="button" id="importYoutubeBtn" class="btn btn-primary d-none" onclick="importYouTubeVideos()">
                    <i class="fa fa-download"></i> İçe Aktar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Vimeo Video İçe Aktarma Modal -->
<div class="modal fade" id="vimeoUploadModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Vimeo'dan Video İçe Aktar</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="form-group">
          <label>Vimeo Video URL'si</label>
          <div class="input-group">
            <input type="text" class="form-control" id="vimeoUrl" placeholder="https://vimeo.com/123456789">
            <div class="input-group-append">
              <button class="btn btn-primary" type="button" onclick="fetchVimeoVideo()">Bilgileri Getir</button>
            </div>
          </div>
          <small class="form-text text-muted">Örnek: https://vimeo.com/123456789</small>
        </div>
                
        <div id="vimeoVideoList" class="video-list mt-3">
          <!-- Video bilgileri burada gösterilecek -->
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">İptal</button>
        <button type="button" class="btn btn-primary d-none" id="importVimeoBtn" onclick="importVimeoVideos()">İçe Aktar</button>
      </div>
    </div>
  </div>
</div>

<!-- URL İle Video Ekleme Modal -->
<div class="modal fade" id="urlUploadModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">URL ile İçerik Ekle</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form id="urlUploadForm">
          <input type="hidden" id="url_modul_id" name="bolum_id" value="">
          
          <div class="form-group">
            <label for="url_title">İçerik Başlığı <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="url_title" name="title" required placeholder="Öğrencilere gösterilecek başlık">
          </div>
          
          <div class="form-group">
            <label for="url_link">URL/Bağlantı <span class="text-danger">*</span></label>
            <input type="url" class="form-control" id="url_link" name="url" required placeholder="https://...">
            <small class="form-text text-muted">Öğrencilerin erişeceği içeriğin tam bağlantısı</small>
          </div>
          
          <div class="form-group">
            <label for="url_description">Açıklama (Opsiyonel)</label>
            <textarea class="form-control" id="url_description" name="description" rows="3" placeholder="Ders içeriği hakkında kısa açıklama"></textarea>
          </div>
          
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label for="url_duration_hour">Tahmini Süre (Saat)</label>
                <input type="number" class="form-control" id="url_duration_hour" name="duration_hour" min="0" max="24" value="0">
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label for="url_duration_minute">Tahmini Süre (Dakika)</label>
                <input type="number" class="form-control" id="url_duration_minute" name="duration_minute" min="0" max="59" value="0">
              </div>
            </div>
          </div>
          
          <div class="form-group">
            <div class="custom-control custom-checkbox">
              <input type="checkbox" class="custom-control-input" id="url_preview" name="is_preview" value="1">
              <label class="custom-control-label" for="url_preview">Ücretsiz Önizleme İçeriği</label>
              <small class="form-text text-muted">Bu içerik kursu satın almayan kullanıcılara da gösterilecektir.</small>
            </div>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">İptal</button>
        <button type="button" class="btn btn-primary" onclick="uploadUrl()">Kaydet</button>
      </div>
    </div>
  </div>
</div>

<!-- H5P İçerik Ekleme Modal -->
<div class="modal fade" id="h5pUploadModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">H5P İçeriği Yükle</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="h5pUploadForm" action="../netting/islem.php" method="POST" enctype="multipart/form-data">
                <div class="modal-body">
                    <input type="hidden" name="h5p_upload" value="1">
                    <input type="hidden" name="kurs_id" value="<?= $kurs_id ?>">
                    <input type="hidden" name="bolum_id" id="h5p_bolum_id">
                    
                    <div class="alert alert-info">
                        <i class="fa fa-info-circle"></i> H5P (.h5p) dosyası yükleyin.
                    </div>
                    
                    <div class="form-group">
                        <label>H5P Başlığı</label>
                        <input type="text" class="form-control" name="title" required>
                    </div>
                    
                    <div class="form-group">
                        <label>H5P Dosyası (.h5p)</label>
                        <input type="file" class="form-control" name="h5p_file" accept=".h5p" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Açıklama (Opsiyonel)</label>
                        <textarea class="form-control" name="description" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">İptal</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fa fa-cube"></i> Yükle
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- SCORM Paketi Ekleme Modal -->
<div class="modal fade" id="scormUploadModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">SCORM Paketi Yükle</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="scormUploadForm" action="../netting/islem.php" method="POST" enctype="multipart/form-data">
                <div class="modal-body">
                    <input type="hidden" name="scorm_upload" value="1">
                    <input type="hidden" name="kurs_id" value="<?= $kurs_id ?>">
                    <input type="hidden" name="bolum_id" id="scorm_bolum_id">
                    
                    <div class="alert alert-info">
                        <i class="fa fa-info-circle"></i> SCORM (.zip) dosyası yükleyin.
                    </div>
                    
                    <div class="form-group">
                        <label>SCORM Başlığı</label>
                        <input type="text" class="form-control" name="title" required>
                    </div>
                    
                    <div class="form-group">
                        <label>SCORM Paketi (.zip)</label>
                        <input type="file" class="form-control" name="scorm_file" accept=".zip" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Açıklama (Opsiyonel)</label>
                        <textarea class="form-control" name="description" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">İptal</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fa fa-archive"></i> Yükle
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Embed Kodu Ekleme Modal -->
<div class="modal fade" id="embedUploadModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Embed Kodu ile Ekle</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="embedUploadForm">
                    <div class="form-group">
                        <label>İçerik Başlığı</label>
                        <input type="text" class="form-control" name="title" required>
                    </div>
                    <div class="form-group">
                        <label>Embed Kodu</label>
                        <textarea class="form-control" name="embed_code" rows="5" required 
                            placeholder="<iframe> kodunu buraya yapıştırın"></textarea>
                        <small class="form-text text-muted">iframe, video veya diğer embed kodlarını yapıştırın</small>
                    </div>
                    <input type="hidden" name="bolum_id" id="embed_bolum_id">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">İptal</button>
                <button type="button" class="btn btn-primary" onclick="uploadEmbed()">
                    <i class="fa fa-code"></i> Ekle
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Bunny Stream Sistemden Video Ekle Modal -->
<div class="modal fade" id="bunnySystemModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Bunny Stream Video Ekle</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <input type="hidden" id="bunny_system_modul_id" value="">
        
        <!-- Bunny Stream Connection Form -->
        <div id="bunny-connect-form" class="p-3 bg-light rounded mb-4">
          <div class="alert alert-info">
            <i class="fa fa-info-circle"></i> Bunny CDN hesabınızdaki videoları görüntülemek için lütfen hesap bilgilerinizi girin.
          </div>
          
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label for="bunny_api_key">API Anahtarı <span class="text-danger">*</span></label>
                <div class="input-group">
                  <input type="password" id="bunny_api_key" class="form-control" required>
                  <div class="input-group-append">
                    <button class="btn btn-outline-secondary" type="button" onclick="togglePasswordVisibility('bunny_api_key')">
                      <i class="fa fa-eye"></i>
                    </button>
                  </div>
                </div>
                <small class="form-text text-muted">API anahtarınızı Bunny Stream kontrol panelinden alabilirsiniz.</small>
              </div>
            </div>
            
            <div class="col-md-6">
              <div class="form-group">
                <label for="bunny_library_id">Kütüphane ID <span class="text-danger">*</span></label>
                <input type="text" id="bunny_library_id" class="form-control" required>
                <small class="form-text text-muted">Kütüphane ID'nizi video kütüphanesi ayarlarından bulabilirsiniz.</small>
              </div>
            </div>
          </div>
          
          <div class="form-check mb-3">
            <input type="checkbox" class="form-check-input" id="bunny_save_credentials">
            <label class="form-check-label" for="bunny_save_credentials">Bilgilerimi Kaydet</label>
          </div>
          
          <button id="bunny-connect-btn" class="btn btn-primary" onclick="connectToBunnyStream()">
            <i class="fa fa-link"></i> Hesaba Bağlan
          </button>
        </div>
        
        <!-- Video List (Hidden until connected) -->
        <div id="bunny-video-list-container" style="display: none;">
          <div class="d-flex justify-content-between align-items-center mb-3">
            <div class="alert alert-success mb-0 py-2">
              <i class="fa fa-check-circle"></i> Bunny CDN'e başarıyla bağlandınız.
            </div>
            <button class="btn btn-sm btn-outline-secondary" onclick="disconnectBunnyStream()">
              Bağlantıyı Kes
            </button>
          </div>
          
          <div class="input-group mb-3">
            <div class="input-group-prepend">
              <span class="input-group-text"><i class="fa fa-search"></i></span>
            </div>
            <input type="text" id="bunny-video-search" class="form-control" placeholder="Videolarda ara..." onkeyup="filterBunnyVideos()">
          </div>
          
          <div id="bunny-video-list" class="row">
            <!-- Video items will be loaded here -->
            <div class="col-12 text-center py-5">
              <div class="spinner-border text-primary" role="status">
                <span class="sr-only">Yükleniyor...</span>
              </div>
              <p class="mt-2">Videolar yükleniyor...</p>
            </div>
          </div>
        </div>
      </div>
      
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">İptal</button>
        <button id="add-selected-video-btn" type="button" class="btn btn-primary" onclick="addSelectedBunnyVideo()" style="display: none;">
          Seçili Videoyu Ekle
        </button>
      </div>
    </div>
  </div>
</div>

<!-- CSS & JS Styles -->
<style>
.upload-methods {
    display: flex;
    flex-wrap: wrap;
    gap: 15px;
    justify-content: center;
}

.upload-method {
    width: 200px;
    height: 180px;
    background: #f8f9fa;
    border-radius: 8px;
    padding: 20px;
    text-align: center;
    cursor: pointer;
    transition: all 0.3s;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
}

.upload-method:hover {
    background: #e9ecef;
    transform: translateY(-5px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.upload-method i {
    font-size: 36px;
    margin-bottom: 10px;
    color: #007bff;
}

.upload-method h6 {
    margin-bottom: 8px;
    font-weight: 600;
}

.upload-method p {
    font-size: 12px;
    color: #6c757d;
    margin: 0;
}

.video-preview-list {
    margin-top: 20px;
}

.video-preview-item {
    border: 1px solid #dee2e6;
    border-radius: 8px;
    padding: 15px;
    margin-bottom: 15px;
    background: #f8f9fa;
}

.video-title-container {
    margin-bottom: 10px;
}

.video-title-label {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 5px;
}

.video-title-input {
    width: 100%;
    padding: 8px;
    border: 1px solid #ced4da;
    border-radius: 4px;
}

.video-title-tip {
    display: flex;
    align-items: center;
    gap: 5px;
    margin-top: 5px;
    font-size: 12px;
    color: #6c757d;
}

.video-info {
    display: flex;
    gap: 15px;
}

.video-thumbnail {
    width: 160px;
    height: 90px;
    object-fit: cover;
    border-radius: 4px;
}

.video-details {
    flex: 1;
}

.video-channel, .video-duration, .video-size {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 5px;
    font-size: 14px;
    color: #495057;
}

.video-list {
    max-height: 400px;
    overflow-y: auto;
}
</style>

<script>
function editModul(modulId) {
  // Get module data via AJAX
  $.ajax({
    url: '../netting/islem.php',
    type: 'GET',
    data: { get_modul: modulId },
    success: function(response) {
      const modul = JSON.parse(response);
      $('#edit_modul_id').val(modul.modul_id);
      $('#edit_modul_ad').val(modul.modul_ad);
      $('#edit_modul_sira').val(modul.modul_sira);
      $('#editModulModal').modal('show');
    }
  });
}

function deleteModul(modulId) {
  Swal.fire({
    title: 'Emin misiniz?',
    text: "Bu modül ve içindeki tüm dersler silinecek!",
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'Evet, Sil!',
    cancelButtonText: 'İptal'
  }).then((result) => {
    if (result.isConfirmed) {
      window.location.href = '../netting/islem.php?modul_id=' + modulId + '&modul_sil=ok';
    }
  });
}

function addSection(modul_id) {
  console.log("addSection called with modul_id:", modul_id);
  
  // Set the modul_id in the global hidden field
  const moduleIdField = document.getElementById('add_section_modul_id');
  moduleIdField.value = modul_id;
  
  console.log("Set add_section_modul_id to:", moduleIdField.value);
  
  // Yeni video yükleme yöntemleri modalını aç
  $('#bulkAddLessonModal').modal('show');
}

function editSection(bolumId) {
  $.ajax({
    url: '../netting/islem.php',
    type: 'GET',
    data: { get_bolum: bolumId },
    success: function(response) {
      const bolum = JSON.parse(response);
      $('#edit_bolum_id').val(bolum.bolum_id);
      $('#edit_bolum_ad').val(bolum.bolum_ad);
      $('#edit_bolum_sure_saat').val(bolum.bolum_sure_saat);
      $('#edit_bolum_sure_dakika').val(bolum.bolum_sure_dakika);
      $('#edit_bolum_sira').val(bolum.bolum_sira);
      $('#editSectionModal').modal('show');
    }
  });
}

function deleteSection(bolumId) {
  Swal.fire({
    title: 'Emin misiniz?',
    text: "Bu ders silinecek!",
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'Evet, Sil!',
    cancelButtonText: 'İptal'
  }).then((result) => {
    if (result.isConfirmed) {
      window.location.href = '../netting/islem.php?bolum_id=' + bolumId + '&bolum_sil=ok';
    }
  });
}

// Ders ekleme modal gösterme fonksiyonu
function showBulkAddLessonModal(bolumId) {
    console.log("Modal açılıyor - Bölüm ID:", bolumId);
    
    // Set the bolum_id in both places
    document.getElementById('bolum_id').value = bolumId;
    
    // Modal'ı göster
    $('#bulkAddLessonModal').modal('show');
}

// Yükleme metodu seçme fonksiyonu
function selectUploadMethod(method) {
    console.log("Seçilen metod:", method);
    const bolumId = document.getElementById('add_section_modul_id').value;
    
    console.log("Using modul_id:", bolumId);
    
    // Ana modalı kapat
    $('#bulkAddLessonModal').modal('hide');
    
    // Seçilen metoda göre ilgili modalı aç
    switch(method) {
        case 'local':
            // Set the section_id before showing the modal
            setTimeout(function() {
                document.getElementById('local_section_id').value = bolumId;
                console.log("Set local_section_id to:", bolumId);
                $('#localUploadModal').modal('show');
            }, 500);
            break;
            
        case 'bunny':
            console.log("Bunny system case triggered");
            setTimeout(function() {
                openBunnySystemModal(bolumId);
            }, 500);
            break;
            
        case 'youtube':
            $('#youtubeUploadModal').modal('show');
            break;
            
        case 'vimeo':
            $('#vimeoUploadModal').modal('show');
            break;
            
        case 'url':
            setTimeout(function() {
                openUrlModal(bolumId);
            }, 500);
            break;
            
        case 'h5p':
            document.getElementById('h5p_bolum_id').value = bolumId;
            $('#h5pUploadModal').modal('show');
            break;
        
        case 'scorm':
            document.getElementById('scorm_bolum_id').value = bolumId;
            $('#scormUploadModal').modal('show');
            break;

        case 'embed':
            document.getElementById('embed_bolum_id').value = bolumId;
            $('#embedUploadModal').modal('show');
            break;
    }
}

// Video önizlemelerini gösterme fonksiyonu
function previewVideos(input) {
    const videoPreviewList = document.getElementById('videoPreviewList');
    videoPreviewList.innerHTML = '';
    
    // Check if the section_id is set - add a warning if not
    const sectionId = document.getElementById('local_section_id').value;
    if (!sectionId) {
        console.error("WARNING: No section_id set for video upload!");
        videoPreviewList.innerHTML = '<div class="alert alert-danger">Modül ID bulunamadı! Lütfen sayfayı yenileyip tekrar deneyin.</div>';
        return;
    }
    
    if (input.files && input.files.length > 0) {
        for (let i = 0; i < input.files.length; i++) {
            const file = input.files[i];
            
            // Video dosyası kontrolü
            if (!file.type.match('video/mp4')) {
                continue;
            }
            
            // Önizleme öğesi oluştur
            const previewItem = document.createElement('div');
            previewItem.className = 'video-preview-item';
            
            // Dosya adından varsayılan başlık oluştur
            const defaultTitle = file.name.replace(/\.[^/.]+$/, "");
            
            previewItem.innerHTML = `
                <div class="video-title-container">
                    <div class="video-title-label">
                        <i class="fa fa-edit"></i>
                        <span>Video Başlığı</span>
                        <span class="text-danger">*</span>
                    </div>
                    <input type="text" class="video-title-input" name="titles[]" value="${defaultTitle}" placeholder="Video başlığını girin">
                    <div class="video-title-tip">
                        <i class="fa fa-info-circle"></i>
                        <span>Bu başlık öğrencilere gösterilecektir</span>
                    </div>
                </div>
                
                <div class="video-info">
                    <img class="video-thumbnail" src="" alt="Video önizleme">
                    <div class="video-details">
                        <div class="video-channel">
                            <i class="fa fa-file-video-o"></i>
                            <span>${file.name}</span>
                        </div>
                        <div class="video-size">
                            <i class="fa fa-hdd-o"></i>
                            <span>${(file.size / (1024 * 1024)).toFixed(2)} MB</span>
                        </div>
                        <div class="form-group mt-2">
                            <label>Açıklama (Opsiyonel)</label>
                            <textarea class="form-control" name="descriptions[]" rows="2"></textarea>
                        </div>
                    </div>
                </div>
            `;
            
            videoPreviewList.appendChild(previewItem);
            
            // Video thumbnail'ini oluştur
            const thumbnail = previewItem.querySelector('.video-thumbnail');
            const videoURL = URL.createObjectURL(file);
            
            // Videoyu yükle ve zamanına atlayıp thumbnail oluştur
            const video = document.createElement('video');
            video.src = videoURL;
            video.currentTime = 1; // 1 saniyeye atla
            video.addEventListener('loadeddata', function() {
                const canvas = document.createElement('canvas');
                canvas.width = 160;
                canvas.height = 90;
                const ctx = canvas.getContext('2d');
                ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
                thumbnail.src = canvas.toDataURL();
                
                // Video süresini hesapla
                const minutes = Math.floor(video.duration / 60);
                const seconds = Math.floor(video.duration % 60);
                const durationText = `${minutes}:${seconds < 10 ? '0' : ''}${seconds}`;
                
                // Süre bilgisini ekle
                const durationDiv = document.createElement('div');
                durationDiv.className = 'video-duration';
                durationDiv.innerHTML = `
                    <i class="fa fa-clock-o"></i>
                    <span>${durationText}</span>
                    <input type="hidden" name="durations[]" value="${Math.floor(video.duration)}">
                `;
                
                previewItem.querySelector('.video-details').appendChild(durationDiv);
            });
        }
    }
}

// YouTube video bilgilerini getirme
function fetchYouTubeVideo() {
    const youtubeUrl = document.getElementById('youtubeUrl').value.trim();
    
    if (!youtubeUrl) {
        alert('Lütfen bir YouTube URL\'si girin');
        return;
    }
    
    // Video listesini temizle
    const videoList = document.getElementById('youtubeVideoList');
    videoList.innerHTML = '<div class="text-center"><i class="fa fa-spinner fa-spin fa-2x"></i><p>Video bilgileri alınıyor...</p></div>';
    
    // Import butonunu gizle
    const importBtn = document.getElementById('importYoutubeBtn');
    importBtn.classList.add('d-none');
    
    // AJAX isteği
    fetch(`../netting/islem.php?action=get_youtube_info&url=${encodeURIComponent(youtubeUrl)}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const video = data.video;
                
                // Video önizlemesini oluştur
                videoList.innerHTML = `
                <div class="video-preview-item">
                    <div class="video-title-container">
                        <div class="video-title-label">Video Başlığı:</div>
                        <input type="text" class="video-title-input" name="youtube_title" value="${escapeHtml(video.title)}" placeholder="Video başlığını girin">
                    </div>
                    
                    <div class="video-info">
                        <img class="video-thumbnail" src="${video.thumbnail}" alt="Video önizleme">
                        <div class="video-details">
                            <div class="video-channel">
                                <i class="fa fa-youtube"></i>
                                <span>${escapeHtml(video.channel_title)}</span>
                            </div>
                            <div class="video-duration">
                                <i class="fa fa-clock-o"></i>
                                <span>${video.duration}</span>
                            </div>
                            <div class="form-group mt-2">
                                <label>Açıklama (Opsiyonel)</label>
                                <textarea class="form-control" name="youtube_description" rows="2">${escapeHtml(video.description.substring(0, 200))}</textarea>
                            </div>
                            <input type="hidden" name="youtube_video_id" value="${video.id}">
                            <input type="hidden" name="youtube_duration" value="${video.total_seconds}">
                        </div>
                    </div>
                </div>
                `;
                
                // İçe aktarma butonunu göster
                importBtn.classList.remove('d-none');
                
            } else {
                videoList.innerHTML = `<div class="alert alert-danger">${data.error}</div>`;
            }
        })
        .catch(error => {
            videoList.innerHTML = `<div class="alert alert-danger">Bir hata oluştu: ${error.message}</div>`;
        });
}

// HTML karakterlerini escape etme
function escapeHtml(text) {
    const map = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    };
    
    return text.replace(/[&<>"']/g, m => map[m]);
}

// YouTube videolarını içe aktarma
function importYouTubeVideos() {
    const modul_id = document.getElementById('add_section_modul_id').value;
    const title = document.querySelector('input[name="youtube_title"]').value;
    const description = document.querySelector('textarea[name="youtube_description"]').value;
    const videoId = document.querySelector('input[name="youtube_video_id"]').value;
    const duration = document.querySelector('input[name="youtube_duration"]').value;
    
    // Form verileri
    const formData = new FormData();
    formData.append('youtube_import', '1');
    formData.append('kurs_id', '<?= $kurs_id ?>');
    formData.append('bolum_id', modul_id);
    formData.append('title', title);
    formData.append('description', description);
    formData.append('video_id', videoId);
    formData.append('duration', duration);
    
    // AJAX isteği
    fetch('../netting/islem.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Video başarıyla eklendi!');
            $('#youtubeUploadModal').modal('hide');
            location.reload();
        } else {
            alert('Hata: ' + data.error);
        }
    })
    .catch(error => {
        alert('Bir hata oluştu: ' + error.message);
    });
}

// Vimeo video bilgilerini getirme
function fetchVimeoVideo() {
    const vimeoUrl = document.getElementById('vimeoUrl').value.trim();
    
    if (!vimeoUrl) {
        alert('Lütfen bir Vimeo URL\'si girin');
        return;
    }
    
    // Video listesini temizle
    const videoList = document.getElementById('vimeoVideoList');
    videoList.innerHTML = '<div class="text-center"><i class="fa fa-spinner fa-spin fa-2x"></i><p>Video bilgileri alınıyor...</p></div>';
    
    // Import butonunu gizle
    const importBtn = document.getElementById('importVimeoBtn');
    importBtn.classList.add('d-none');
    
    // AJAX isteği
    fetch(`../netting/islem.php?action=get_vimeo_info&url=${encodeURIComponent(vimeoUrl)}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const video = data.video;
                
                // Video önizlemesini oluştur
                videoList.innerHTML = `
                <div class="video-preview-item">
                    <div class="video-title-container">
                        <div class="video-title-label">Video Başlığı:</div>
                        <input type="text" class="video-title-input" name="vimeo_title" value="${escapeHtml(video.title)}" placeholder="Video başlığını girin">
                    </div>
                    
                    <div class="video-info">
                        <img class="video-thumbnail" src="${video.thumbnail}" alt="Video önizleme">
                        <div class="video-details">
                            <div class="video-channel">
                                <i class="fa fa-vimeo"></i>
                                <span>${escapeHtml(video.author_name)}</span>
                            </div>
                            <div class="video-duration">
                                <i class="fa fa-clock-o"></i>
                                <span>${video.duration}</span>
                            </div>
                            <div class="form-group mt-2">
                                <label>Açıklama (Opsiyonel)</label>
                                <textarea class="form-control" name="vimeo_description" rows="2">${escapeHtml(video.description.substring(0, 200))}</textarea>
                            </div>
                            <input type="hidden" name="vimeo_video_id" value="${video.id}">
                            <input type="hidden" name="vimeo_duration" value="${video.total_seconds}">
                        </div>
                    </div>
                </div>
                `;
                
                // İçe aktarma butonunu göster
                importBtn.classList.remove('d-none');
                
            } else {
                videoList.innerHTML = `<div class="alert alert-danger">${data.error}</div>`;
            }
        })
        .catch(error => {
            videoList.innerHTML = `<div class="alert alert-danger">Bir hata oluştu: ${error.message}</div>`;
        });
}

// Vimeo videolarını içe aktarma
function importVimeoVideos() {
    const modul_id = document.getElementById('add_section_modul_id').value;
    const title = document.querySelector('input[name="vimeo_title"]').value;
    const description = document.querySelector('textarea[name="vimeo_description"]').value;
    const videoId = document.querySelector('input[name="vimeo_video_id"]').value;
    const duration = document.querySelector('input[name="vimeo_duration"]').value;
    
    console.log("Vimeo duration:", duration); // Debug log
    
    // Form verileri
    const formData = new FormData();
    formData.append('vimeo_import', '1');
    formData.append('kurs_id', '<?= $kurs_id ?>');
    formData.append('bolum_id', modul_id);
    formData.append('title', title);
    formData.append('description', description);
    formData.append('video_id', videoId);
    formData.append('duration', duration);
    
    // AJAX isteği
    fetch('../netting/islem.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Video başarıyla eklendi!');
            $('#vimeoUploadModal').modal('hide');
            location.reload();
        } else {
            alert('Hata: ' + data.error);
        }
    })
    .catch(error => {
        alert('Bir hata oluştu: ' + error.message);
    });
}

// COMPLETELY REVISED URL modal opening function
function openUrlModal(modul_id) {
    console.log("openUrlModal called with modul_id:", modul_id);
    
    if (!modul_id) {
        // If no module ID provided, try to get it from the hidden field
        modul_id = document.getElementById('add_section_modul_id').value;
        console.log("Retrieved modul_id from add_section_modul_id:", modul_id);
    }
    
    // Final check - if still no module ID, show error
    if (!modul_id) {
        alert('Lütfen önce bir modül seçin!');
        $('#bulkAddLessonModal').modal('hide');
        return;
    }
    
    // Close the selection modal if it's open
    $('#bulkAddLessonModal').modal('hide');
    
    // Wait for the first modal to close completely
    setTimeout(function() {
        // Set the modul ID in the form - using proper ID
        const urlModulIdField = document.getElementById('url_modul_id');
        urlModulIdField.value = modul_id;
        
        console.log("Set url_modul_id to:", modul_id);
        console.log("Element value is now:", urlModulIdField.value);
        
        // Clear previous form inputs
        document.getElementById('url_title').value = '';
        document.getElementById('url_link').value = '';
        if (document.getElementById('url_description')) {
            document.getElementById('url_description').value = '';
        }
        document.getElementById('url_duration_hour').value = '0';
        document.getElementById('url_duration_minute').value = '0';
        
        // Now open the URL modal
        $('#urlUploadModal').modal('show');
    }, 500);
}

// COMPLETELY REVISED URL upload function
function uploadUrl() {
    console.log("uploadUrl function called");
    
    const form = document.getElementById('urlUploadForm');
    
    // First, explicitly check for module ID
    const modul_id = document.getElementById('url_modul_id').value;
    console.log("Module ID before upload:", modul_id);
    
    if (!modul_id) {
        alert('Modül seçilmedi! Lütfen tekrar deneyin.');
        return;
    }
    
    // Form validasyonu
    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }
    
    const formData = new FormData(form);
    
    // Add the module ID again to be sure
    formData.append('bolum_id', modul_id);
    formData.append('url_upload', '1');
    formData.append('kurs_id', '<?= $kurs_id ?>');
    
    // Log the FormData entries to verify
    for (let [key, value] of formData.entries()) {
        console.log(`${key}: ${value}`);
    }
    
    // Yükleme başladığında butonları devre dışı bırak
    const submitButton = document.querySelector('#urlUploadModal .btn-primary');
    submitButton.disabled = true;
    submitButton.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Yükleniyor...';
    
    fetch('../netting/islem.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('İçerik başarıyla eklendi!');
            $('#urlUploadModal').modal('hide');
            location.reload();
        } else {
            alert('Hata: ' + data.error);
            // Hata durumunda butonları etkinleştir
            submitButton.disabled = false;
            submitButton.innerHTML = 'Kaydet';
        }
    })
    .catch(error => {
        alert('Bir hata oluştu: ' + error.message);
        // Hata durumunda butonları etkinleştir
        submitButton.disabled = false;
        submitButton.innerHTML = 'Kaydet';
    });
}

// Embed kodu ile içerik ekleme
function uploadEmbed() {
    const form = document.getElementById('embedUploadForm');
    const formData = new FormData(form);
    formData.append('embed_upload', '1');
    formData.append('kurs_id', '<?= $kurs_id ?>');
    
    fetch('../netting/islem.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('İçerik başarıyla eklendi!');
            $('#embedUploadModal').modal('hide');
            location.reload();
        } else {
            alert('Hata: ' + data.error);
        }
    })
    .catch(error => {
        alert('Bir hata oluştu: ' + error.message);
    });
}

// Update the function that opens the URL modal directly
function openUrlModal(modul_id) {
    if (!modul_id) {
        // If no module ID provided, try to get it from the hidden field
        modul_id = document.getElementById('add_section_modul_id').value;
        
        // If still empty, show error
        if (!modul_id) {
            alert('Lütfen önce bir modül seçin!');
            $('#bulkAddLessonModal').modal('hide');
            return;
        }
    }
    
    // Set the modul ID in the form - using proper ID
    document.getElementById('url_modul_id').value = modul_id;
    
    console.log("Opening URL modal with modul_id:", modul_id); // Debug log
    
    // Clear previous form inputs
    document.getElementById('url_title').value = '';
    document.getElementById('url_link').value = '';
    document.getElementById('url_description').value = '';
    document.getElementById('url_duration_hour').value = '0';
    document.getElementById('url_duration_minute').value = '0';
    
    // Close the selection modal if it's open
    $('#bulkAddLessonModal').modal('hide');
    
    // Wait for animation to complete before opening the new modal
    setTimeout(function() {
        $('#urlUploadModal').modal('show');
    }, 500);
}

// Add debugging to the openBunnySystemModal function
function openBunnySystemModal(modul_id) {
    console.log("Opening Bunny System Modal with modul_id:", modul_id);
    
    // Set the modul ID
    document.getElementById('bunny_system_modul_id').value = modul_id || document.getElementById('add_section_modul_id').value;
    console.log("Bunny system modul_id set to:", document.getElementById('bunny_system_modul_id').value);
    
    // Load saved credentials if any
    loadBunnyCredentials();
    
    // Close the selection modal if it's open
    $('#bulkAddLessonModal').modal('hide');
    
    // Open the Bunny system modal - no delay needed
    $('#bunnySystemModal').modal('show');
    console.log("Modal show triggered");
}

// Toggle password visibility
function togglePasswordVisibility(elementId) {
    const input = document.getElementById(elementId);
    if (input.type === "password") {
        input.type = "text";
    } else {
        input.type = "password";
    }
}

// Load saved Bunny Stream credentials
function loadBunnyCredentials() {
    const savedApiKey = localStorage.getItem('bunny_api_key');
    const savedLibraryId = localStorage.getItem('bunny_library_id');
    const saveCredentials = localStorage.getItem('bunny_save_credentials') === 'true';
    
    if (savedApiKey && savedLibraryId) {
        document.getElementById('bunny_api_key').value = savedApiKey;
        document.getElementById('bunny_library_id').value = savedLibraryId;
        document.getElementById('bunny_save_credentials').checked = saveCredentials;
        
        // If credentials are saved, auto-connect
        if (saveCredentials) {
            connectToBunnyStream();
        }
    }
}

// Connect to Bunny Stream
function connectToBunnyStream() {
    const apiKey = document.getElementById('bunny_api_key').value.trim();
    const libraryId = document.getElementById('bunny_library_id').value.trim();
    const saveCredentials = document.getElementById('bunny_save_credentials').checked;
    
    if (!apiKey || !libraryId) {
        alert('Lütfen API Anahtarı ve Kütüphane ID bilgilerini girin.');
        return;
    }
    
    // Save credentials if requested
    if (saveCredentials) {
        localStorage.setItem('bunny_api_key', apiKey);
        localStorage.setItem('bunny_library_id', libraryId);
        localStorage.setItem('bunny_save_credentials', 'true');
    } else {
        // Clear saved credentials if not checking "save"
        localStorage.removeItem('bunny_api_key');
        localStorage.removeItem('bunny_library_id');
        localStorage.removeItem('bunny_save_credentials');
    }
    
    // Disable button and show loading
    const connectButton = document.getElementById('bunny-connect-btn');
    connectButton.disabled = true;
    connectButton.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Bağlanıyor...';
    
    // Fetch videos from server
    fetchBunnyVideos(apiKey, libraryId);
}

// Disconnect from Bunny Stream
function disconnectBunnyStream() {
    // Hide video list and show connection form
    document.getElementById('bunny-video-list-container').style.display = 'none';
    document.getElementById('bunny-connect-form').style.display = 'block';
    document.getElementById('add-selected-video-btn').style.display = 'none';
    
    // Reset connect button
    document.getElementById('bunny-connect-btn').innerHTML = '<i class="fa fa-link"></i> Hesaba Bağlan';
    document.getElementById('bunny-connect-btn').disabled = false;
    
    // Clear selected video
    window.selectedBunnyVideo = null;
}

// Fetch videos from Bunny Stream
function fetchBunnyVideos(apiKey, libraryId) {
    // Create form data
    const formData = new FormData();
    formData.append('bunny_fetch_videos', '1');
    formData.append('api_key', apiKey);
    formData.append('library_id', libraryId);
    
    console.log("Fetching Bunny videos with:", {libraryId});
    
    // Fetch videos using AJAX
    fetch('../netting/islem.php', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        console.log("Response status:", response.status);
        return response.text(); // First get raw text response
    })
    .then(text => {
        console.log("Raw response:", text);
        try {
            return JSON.parse(text); // Try to parse as JSON
        } catch (e) {
            console.error("JSON parse error:", e);
            throw new Error("Server response is not valid JSON: " + text);
        }
    })
    .then(data => {
        if (data.success) {
            // Show video list and hide connection form
            document.getElementById('bunny-video-list-container').style.display = 'block';
            document.getElementById('bunny-connect-form').style.display = 'none';
            document.getElementById('add-selected-video-btn').style.display = 'none';
            
            // Render videos
            renderBunnyVideos(data.videos);
        } else {
            alert('Hata: ' + data.error);
            // Reset connect button
            document.getElementById('bunny-connect-btn').innerHTML = '<i class="fa fa-link"></i> Hesaba Bağlan';
            document.getElementById('bunny-connect-btn').disabled = false;
        }
    })
    .catch(error => {
        alert('Bir hata oluştu: ' + error.message);
        // Reset connect button
        document.getElementById('bunny-connect-btn').innerHTML = '<i class="fa fa-link"></i> Hesaba Bağlan';
        document.getElementById('bunny-connect-btn').disabled = false;
    });
}

// Render Bunny Stream videos
function renderBunnyVideos(videos) {
    const videoList = document.getElementById('bunny-video-list');
    
    if (videos.length === 0) {
        videoList.innerHTML = '<div class="col-12 text-center py-5"><p>Hiç video bulunamadı.</p></div>';
        return;
    }
    
    let html = '';
    
    videos.forEach(video => {
        // Construct proper Bunny Stream thumbnail URL
        const thumbnailUrl = `https://${document.getElementById('bunny_library_id').value}.b-cdn.net/${video.guid}/thumbnail.jpg`;
        
        // Format duration
        const minutes = Math.floor(video.length / 60);
        const seconds = Math.floor(video.length % 60);
        const formattedDuration = `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
        
        // Format date
        const uploadDate = new Date(video.dateUploaded);
        const formattedDate = uploadDate.toLocaleDateString();
        
        html += `
        <div class="col-md-6 mb-4">
            <div class="card bunny-video-card" data-video-id="${video.guid}" onclick="selectBunnyVideo(this, ${JSON.stringify(video).replace(/"/g, '&quot;')})">
                <div class="position-relative">
                    <img src="${thumbnailUrl}" class="card-img-top" alt="${video.title}" onerror="this.src='https://placehold.co/320x180/e9ecef/6c757d?text=No+Thumbnail'">
                    <div class="position-absolute" style="bottom: 10px; right: 10px; background: rgba(0,0,0,0.7); color: white; padding: 2px 8px; border-radius: 4px;">
                        <i class="fa fa-clock-o"></i> ${formattedDuration}
                    </div>
                </div>
                <div class="card-body">
                    <h6 class="card-title">${video.title}</h6>
                    <div class="d-flex align-items-center mt-2 text-muted small">
                        <i class="fa fa-calendar mr-1"></i> ${formattedDate}
                        <div class="ml-3"><i class="fa fa-eye mr-1"></i> ${video.views || 0}</div>
                    </div>
                </div>
            </div>
        </div>`;
    });
    
    videoList.innerHTML = html;
}

// Select a Bunny Stream video
function selectBunnyVideo(element, videoData) {
    // Remove selection from other cards
    document.querySelectorAll('.bunny-video-card').forEach(card => {
        card.classList.remove('border-primary');
    });
    
    // Add selection to clicked card
    element.classList.add('border-primary');
    
    // Store selected video data
    window.selectedBunnyVideo = {
        guid: videoData.guid,
        title: videoData.title,
        duration: videoData.length || 0
    };
    
    // Show the add button
    document.getElementById('add-selected-video-btn').style.display = 'block';
}

// Filter Bunny videos by search term
function filterBunnyVideos() {
    const searchTerm = document.getElementById('bunny-video-search').value.toLowerCase();
    const cards = document.querySelectorAll('.bunny-video-card');
    
    cards.forEach(card => {
        const title = card.querySelector('.card-title').textContent.toLowerCase();
        const parentCol = card.parentElement;
        
        if (title.includes(searchTerm)) {
            parentCol.style.display = '';
        } else {
            parentCol.style.display = 'none';
        }
    });
}

// Add selected Bunny video to course
function addSelectedBunnyVideo() {
    if (!window.selectedBunnyVideo) {
        alert('Lütfen bir video seçin.');
        return;
    }
    
    const modul_id = document.getElementById('bunny_system_modul_id').value;
    
    if (!modul_id) {
        alert('Modül seçilmedi! Lütfen tekrar deneyin.');
        return;
    }
    
    // Disable button during process
    const addButton = document.getElementById('add-selected-video-btn');
    addButton.disabled = true;
    addButton.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Ekleniyor...';
    
    // Create form data
    const formData = new FormData();
    formData.append('bunny_add_selected_video', '1');
    formData.append('kurs_id', '<?= $kurs_id ?>');
    formData.append('bolum_id', modul_id);
    formData.append('video_guid', window.selectedBunnyVideo.guid);
    formData.append('title', window.selectedBunnyVideo.title);
    formData.append('duration', window.selectedBunnyVideo.duration);
    
    // Submit the form
    fetch('../netting/islem.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Video başarıyla eklendi!');
            $('#bunnySystemModal').modal('hide');
            location.reload();
        } else {
            alert('Hata: ' + data.error);
            // Reset button
            addButton.disabled = false;
            addButton.innerHTML = 'Seçili Videoyu Ekle';
        }
    })
    .catch(error => {
        alert('Bir hata oluştu: ' + error.message);
        // Reset button
        addButton.disabled = false;
        addButton.innerHTML = 'Seçili Videoyu Ekle';
    });
}


</script>

<?php include 'footer.php'; ?> 