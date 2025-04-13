<?php
include 'header.php';

// Pagination
$sayfa = isset($_GET['sayfa']) ? (int)$_GET['sayfa'] : 1;
$limit = 20;
$offset = ($sayfa - 1) * $limit;

// Search filters
$search = isset($_GET['search']) ? $_GET['search'] : '';
$status = isset($_GET['status']) ? $_GET['status'] : '';

// Build query for sales with credentials
$query = "SELECT 
            sc.credential_id,
            sc.satis_id,
            sc.link,
            sc.username,
            sc.password,
            sc.notlar,
            sc.created_at as cred_created_at,
            s.satis_id,
            s.fatura_id,
            s.kurs_id,
            s.has_edevlet_cert,
            s.has_eng_cert,
            s.has_tr_cert,
            s.has_eng_transcript,
            s.has_tr_transcript,
            s.created_at as satis_created_at,
            f.fatura_no,
            f.odeme_durumu,
            f.user_id,
            k.baslik as kurs_adi,
            ku.kullanici_mail,
            ku.kullanici_ad,
            ku.kullanici_soyad
          FROM satilan_kurslar s
          LEFT JOIN satilan_kurslar_credentials sc ON s.satis_id = sc.satis_id
          INNER JOIN faturalar f ON s.fatura_id = f.fatura_id
          INNER JOIN kurslar k ON s.kurs_id = k.kurs_id
          INNER JOIN kullanici ku ON f.user_id = ku.kullanici_id
          WHERE f.odeme_durumu = 'onaylandi'";

// Add search conditions
if (!empty($search)) {
    $query .= " AND (f.fatura_no LIKE :search OR k.baslik LIKE :search OR 
                    ku.kullanici_mail LIKE :search OR ku.kullanici_ad LIKE :search OR 
                    ku.kullanici_soyad LIKE :search)";
}

if ($status == 'with_credentials') {
    $query .= " AND sc.credential_id IS NOT NULL";
} elseif ($status == 'without_credentials') {
    $query .= " AND sc.credential_id IS NULL";
}

$query .= " ORDER BY f.created_at DESC LIMIT :offset, :limit";

$countQuery = "SELECT COUNT(*) as total FROM satilan_kurslar s 
               INNER JOIN faturalar f ON s.fatura_id = f.fatura_id
               LEFT JOIN satilan_kurslar_credentials sc ON s.satis_id = sc.satis_id
               WHERE f.odeme_durumu = 'onaylandi'";

if (!empty($search)) {
    $countQuery .= " AND (f.fatura_no LIKE :search OR ku.kullanici_mail LIKE :search OR 
                         ku.kullanici_ad LIKE :search OR ku.kullanici_soyad LIKE :search)";
}

if ($status == 'with_credentials') {
    $countQuery .= " AND sc.credential_id IS NOT NULL";
} elseif ($status == 'without_credentials') {
    $countQuery .= " AND sc.credential_id IS NULL";
}

// Prepare and execute the count query
$countStmt = $db->prepare($countQuery);
if (!empty($search)) {
    $searchParam = "%$search%";
    $countStmt->bindParam(':search', $searchParam, PDO::PARAM_STR);
}
$countStmt->execute();
$totalCount = $countStmt->fetch(PDO::FETCH_ASSOC)['total'];
$totalPages = ceil($totalCount / $limit);

// Prepare and execute the main query
$stmt = $db->prepare($query);
if (!empty($search)) {
    $searchParam = "%$search%";
    $stmt->bindParam(':search', $searchParam, PDO::PARAM_STR);
}
$stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
$stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
$stmt->execute();
$sales = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!-- page content -->
<div class="right_col" role="main">
  <div class="">
    <div class="page-title">
      <div class="title_left">
        <h3>Kurs Erişim Bilgileri</h3>
      </div>

      <div class="title_right">
        <div class="col-md-5 col-sm-5 col-xs-12 form-group pull-right top_search">
          <form action="" method="GET">
            <div class="input-group">
              <input type="text" name="search" class="form-control" placeholder="Arama..." value="<?php echo htmlspecialchars($search); ?>">
              <span class="input-group-btn">
                <button class="btn btn-default" type="submit">Ara</button>
              </span>
            </div>
          </form>
        </div>
      </div>
    </div>

    <div class="clearfix"></div>

    <div class="row">
      <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="x_panel">
          <div class="x_title">
            <h2>Kurs Erişim Yönetimi</h2>
            <div class="clearfix"></div>
          </div>
          
          <div class="x_content">
            <div class="row">
              <div class="col-md-6">
                <form action="" method="GET" class="form-inline">
                  <div class="form-group">
                    <select name="status" class="form-control" onchange="this.form.submit()">
                      <option value="" <?php echo $status == '' ? 'selected' : ''; ?>>Tüm Kurslar</option>
                      <option value="with_credentials" <?php echo $status == 'with_credentials' ? 'selected' : ''; ?>>Erişim Bilgisi Olanlar</option>
                      <option value="without_credentials" <?php echo $status == 'without_credentials' ? 'selected' : ''; ?>>Erişim Bilgisi Olmayanlar</option>
                    </select>
                  </div>
                  <?php if (!empty($search)): ?>
                    <input type="hidden" name="search" value="<?php echo htmlspecialchars($search); ?>">
                  <?php endif; ?>
                </form>
              </div>
            </div>
            
            <div class="table-responsive">
              <table class="table table-striped jambo_table">
                <thead>
                  <tr>
                    <th>Fatura No</th>
                    <th>Müşteri</th>
                    <th>Kurs</th>
                    <th>Satış Tarihi</th>
                    <th>Erişim Durumu</th>
                    <th>İşlemler</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($sales as $sale): ?>
                    <tr>
                      <td><?php echo $sale['fatura_no']; ?></td>
                      <td><?php echo $sale['kullanici_ad'] . ' ' . $sale['kullanici_soyad']; ?><br>
                          <small><?php echo $sale['kullanici_mail']; ?></small>
                      </td>
                      <td><?php echo $sale['kurs_adi']; ?></td>
                      <td><?php echo date('d.m.Y H:i', strtotime($sale['satis_created_at'])); ?></td>
                      <td>
                        <?php if (isset($sale['credential_id'])): ?>
                          <span class="label label-success">Erişim Bilgisi Mevcut</span>
                        <?php else: ?>
                          <span class="label label-warning">Erişim Bilgisi Yok</span>
                        <?php endif; ?>
                        
                        <?php if ($sale['has_edevlet_cert'] || $sale['has_eng_cert'] || $sale['has_tr_cert'] || 
                                  $sale['has_eng_transcript'] || $sale['has_tr_transcript']): ?>
                            <div class="cert-info mt-2">
                                <small>
                                    <?php if ($sale['has_edevlet_cert']): ?>
                                        <span class="label label-info">E-Devlet Sertifika</span>
                                    <?php endif; ?>
                                    
                                    <?php if ($sale['has_eng_cert']): ?>
                                        <span class="label label-info">İngilizce Sertifika</span>
                                    <?php endif; ?>
                                    
                                    <?php if ($sale['has_tr_cert']): ?>
                                        <span class="label label-info">Türkçe Sertifika</span>
                                    <?php endif; ?>
                                    
                                    <?php if ($sale['has_eng_transcript']): ?>
                                        <span class="label label-info">İngilizce Transkript</span>
                                    <?php endif; ?>
                                    
                                    <?php if ($sale['has_tr_transcript']): ?>
                                        <span class="label label-info">Türkçe Transkript</span>
                                    <?php endif; ?>
                                </small>
                            </div>
                        <?php endif; ?>
                      </td>
                      <td>
                        <?php if (isset($sale['credential_id'])): ?>
                          <button type="button" class="btn btn-primary btn-xs edit-credential" 
                                  data-credential-id="<?php echo $sale['credential_id']; ?>"
                                  data-satis-id="<?php echo $sale['satis_id']; ?>"
                                  data-link="<?php echo htmlspecialchars($sale['link']); ?>"
                                  data-username="<?php echo htmlspecialchars($sale['username']); ?>"
                                  data-password="<?php echo htmlspecialchars($sale['password']); ?>"
                                  data-notlar="<?php echo htmlspecialchars($sale['notlar'] ?? ''); ?>">
                            Düzenle
                          </button>
                        <?php else: ?>
                          <button type="button" class="btn btn-success btn-xs add-credential" 
                                  data-satis-id="<?php echo $sale['satis_id']; ?>"
                                  data-kurs-adi="<?php echo htmlspecialchars($sale['kurs_adi']); ?>"
                                  data-kullanici="<?php echo htmlspecialchars($sale['kullanici_ad'] . ' ' . $sale['kullanici_soyad']); ?>">
                            Bilgi Ekle
                          </button>
                        <?php endif; ?>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                  
                  <?php if (count($sales) == 0): ?>
                    <tr>
                      <td colspan="6" class="text-center">Kayıt bulunamadı</td>
                    </tr>
                  <?php endif; ?>
                </tbody>
              </table>
            </div>
            
            <!-- Pagination -->
            <?php if ($totalPages > 1): ?>
            <div class="row">
              <div class="col-md-12">
                <ul class="pagination">
                  <?php if ($sayfa > 1): ?>
                    <li><a href="?sayfa=1<?php echo (!empty($search) ? '&search='.urlencode($search) : '').(!empty($status) ? '&status='.urlencode($status) : ''); ?>">&laquo;</a></li>
                  <?php endif; ?>
                  
                  <?php
                  $startPage = max(1, $sayfa - 2);
                  $endPage = min($totalPages, $sayfa + 2);
                  
                  for ($i = $startPage; $i <= $endPage; $i++): ?>
                    <li<?php echo ($i == $sayfa) ? ' class="active"' : ''; ?>>
                      <a href="?sayfa=<?php echo $i; ?><?php echo (!empty($search) ? '&search='.urlencode($search) : '').(!empty($status) ? '&status='.urlencode($status) : ''); ?>">
                        <?php echo $i; ?>
                      </a>
                    </li>
                  <?php endfor; ?>
                  
                  <?php if ($sayfa < $totalPages): ?>
                    <li><a href="?sayfa=<?php echo $totalPages; ?><?php echo (!empty($search) ? '&search='.urlencode($search) : '').(!empty($status) ? '&status='.urlencode($status) : ''); ?>">&raquo;</a></li>
                  <?php endif; ?>
                </ul>
              </div>
            </div>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<!-- /page content -->

<!-- Add Credential Modal -->
<div class="modal fade" id="addCredentialModal" tabindex="-1" role="dialog" aria-labelledby="addCredentialModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="addCredentialModalLabel">Erişim Bilgisi Ekle</h4>
      </div>
      <form action="../netting/islem.php" method="POST">
        <div class="modal-body">
          <input type="hidden" name="satis_id" id="add_satis_id">
          
          <div class="form-group">
            <label>Kurs</label>
            <p id="add_kurs_adi" class="form-control-static"></p>
          </div>
          
          <div class="form-group">
            <label>Müşteri</label>
            <p id="add_kullanici" class="form-control-static"></p>
          </div>
          
          <div class="form-group">
            <label for="add_link">Kurs Linki</label>
            <input type="url" class="form-control" id="add_link" name="link" required>
          </div>
          
          <div class="form-group">
            <label for="add_username">Kullanıcı Adı/E-mail</label>
            <input type="text" class="form-control" id="add_username" name="username" required>
          </div>
          
          <div class="form-group">
            <label for="add_password">Şifre</label>
            <input type="text" class="form-control" id="add_password" name="password" required>
          </div>
          
          <div class="form-group">
            <label for="add_notlar">Notlar</label>
            <textarea class="form-control" id="add_notlar" name="notlar" rows="3"></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">İptal</button>
          <button type="submit" name="kurs_erisim_ekle" class="btn btn-success">Kaydet</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Edit Credential Modal -->
<div class="modal fade" id="editCredentialModal" tabindex="-1" role="dialog" aria-labelledby="editCredentialModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="editCredentialModalLabel">Erişim Bilgisini Düzenle</h4>
      </div>
      <form action="../netting/islem.php" method="POST">
        <div class="modal-body">
          <input type="hidden" name="credential_id" id="edit_credential_id">
          <input type="hidden" name="satis_id" id="edit_satis_id">
          
          <div class="form-group">
            <label for="edit_link">Kurs Linki</label>
            <input type="url" class="form-control" id="edit_link" name="link" required>
          </div>
          
          <div class="form-group">
            <label for="edit_username">Kullanıcı Adı/E-mail</label>
            <input type="text" class="form-control" id="edit_username" name="username" required>
          </div>
          
          <div class="form-group">
            <label for="edit_password">Şifre</label>
            <input type="text" class="form-control" id="edit_password" name="password" required>
          </div>
          
          <div class="form-group">
            <label for="edit_notlar">Notlar</label>
            <textarea class="form-control" id="edit_notlar" name="notlar" rows="3"></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">İptal</button>
          <button type="submit" name="kurs_erisim_duzenle" class="btn btn-primary">Güncelle</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
$(document).ready(function() {
  // Add credential modal
  $('.add-credential').click(function() {
    var satis_id = $(this).data('satis-id');
    var kurs_adi = $(this).data('kurs-adi');
    var kullanici = $(this).data('kullanici');
    
    $('#add_satis_id').val(satis_id);
    $('#add_kurs_adi').text(kurs_adi);
    $('#add_kullanici').text(kullanici);
    
    $('#addCredentialModal').modal('show');
  });
  
  // Edit credential modal
  $('.edit-credential').click(function() {
    var credential_id = $(this).data('credential-id');
    var satis_id = $(this).data('satis-id');
    var link = $(this).data('link');
    var username = $(this).data('username');
    var password = $(this).data('password');
    var notlar = $(this).data('notlar');
    
    $('#edit_credential_id').val(credential_id);
    $('#edit_satis_id').val(satis_id);
    $('#edit_link').val(link);
    $('#edit_username').val(username);
    $('#edit_password').val(password);
    $('#edit_notlar').val(notlar);
    
    $('#editCredentialModal').modal('show');
  });
});
</script>

<?php include 'footer.php'; ?> 