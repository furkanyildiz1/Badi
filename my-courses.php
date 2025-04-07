<?php 
include 'header.php';

// Check if user is logged in
if(!isset($_SESSION['userkullanici_mail'])) {
    header("Location: login.php");
    exit();
}

// Get user's courses with credential information
$kurslar = $db->prepare("
    SELECT 
        k.*,
        f.created_at as erisim_tarihi,
        f.fatura_no,
        f.odeme_durumu,
        sk.satis_id,
        skc.link,
        skc.username,
        skc.password,
        skc.notlar,
        CASE WHEN skc.credential_id IS NULL THEN 0 ELSE 1 END as has_credentials
    FROM satilan_kurslar sk
    JOIN kurslar k ON sk.kurs_id = k.kurs_id
    JOIN faturalar f ON sk.fatura_id = f.fatura_id
    LEFT JOIN satilan_kurslar_credentials skc ON sk.satis_id = skc.satis_id
    WHERE f.user_id = ?
    ORDER BY f.created_at DESC
");
$kurslar->execute([$_SESSION['userkullanici_id']]);
?>

<style>
.my-courses {
    padding-top: 100px;
    padding-bottom: 50px;
    min-height: 100vh;
    background-color: #f8f9fa;
}

.course-card {
    transition: transform 0.3s ease;
    margin-bottom: 30px;
}

.course-card:hover {
    transform: translateY(-5px);
}

.course-image {
    height: 200px;
    object-fit: cover;
}

.course-status {
    position: absolute;
    top: 10px;
    right: 10px;
    padding: 5px 10px;
    border-radius: 3px;
    font-size: 12px;
}

.course-progress {
    height: 5px;
    margin-top: 10px;
}

.course-details {
    padding: 15px;
}

.no-courses {
    text-align: center;
    padding: 50px 20px;
}

.credentials-section {
    margin-top: 20px;
    padding: 15px;
    background-color: #f9f9f9;
    border-radius: 5px;
    border-left: 3px solid #007bff;
}

.credential-item {
    margin-bottom: 10px;
}

.credential-item label {
    font-weight: bold;
    margin-bottom: 5px;
    display: block;
}

.credential-value {
    padding: 8px 12px;
    background-color: #fff;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-family: monospace;
    word-break: break-all;
}

.pending-credential {
    padding: 15px;
    background-color: #fff3cd;
    border-left: 4px solid #ffc107;
    margin-top: 15px;
    border-radius: 4px;
}
</style>

<div class="my-courses">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <h2 class="mb-4">Kurslarım</h2>
            </div>
        </div>

        <div class="row">
            <?php if($kurslar->rowCount() > 0): ?>
                <?php while($kurs = $kurslar->fetch(PDO::FETCH_ASSOC)): ?>
                    <?php if($kurs['odeme_durumu'] != 'iptal_edildi'): ?>
                        <div class="col-md-4">
                            <div class="card course-card">
                                <!-- Course Status Badge -->
                                <?php if($kurs['odeme_durumu'] == 'beklemede'): ?>
                                    <span class="course-status badge bg-warning">Ödeme Bekliyor</span>
                                <?php elseif($kurs['odeme_durumu'] == 'onaylandi'): ?>
                                    <span class="course-status badge bg-success">Aktif</span>
                                <?php endif; ?>

                                <!-- Course Image -->
                                <img src="<?php echo $kurs['resim_yol']; ?>" 
                                    class="card-img-top course-image" 
                                    alt="<?php echo $kurs['baslik']; ?>">

                                <div class="card-body course-details">
                                    <h5 class="card-title"><?php echo $kurs['baslik']; ?></h5>
                                    
                                    <!-- Progress Bar (You can implement actual progress tracking later) -->
                                    <div class="progress course-progress">
                                        <div class="progress-bar" 
                                            role="progressbar" 
                                            style="width: 0%;" 
                                            aria-valuenow="0" 
                                            aria-valuemin="0" 
                                            aria-valuemax="100">
                                        </div>
                                    </div>

                                    <div class="mt-3">
                                        <?php if($kurs['odeme_durumu'] == 'onaylandi'): ?>
                                            <?php if($kurs['has_credentials']): ?>
                                                <button class="btn btn-primary btn-sm show-credentials" 
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#credentialsModal"
                                                        data-kurs-id="<?php echo $kurs['kurs_id']; ?>"
                                                        data-kurs-title="<?php echo htmlspecialchars($kurs['baslik']); ?>"
                                                        data-link="<?php echo htmlspecialchars($kurs['link']); ?>"
                                                        data-username="<?php echo htmlspecialchars($kurs['username']); ?>"
                                                        data-password="<?php echo htmlspecialchars($kurs['password']); ?>"
                                                        data-notlar="<?php echo htmlspecialchars($kurs['notlar'] ?? ''); ?>">
                                                    Kursa Git
                                                </button>
                                            <?php else: ?>
                                                <button class="btn btn-outline-secondary btn-sm" disabled>
                                                    <i class="fa-solid fa-clock me-1"></i> Erişim Bekliyor
                                                </button>
                                                <div class="pending-credential mt-2">
                                                    <small class="text-muted">
                                                        <i class="fa-solid fa-info-circle me-1"></i>
                                                        Kurs erişim bilgileriniz hazırlanıyor. En kısa sürede burada görünecektir.
                                                    </small>
                                                </div>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <button class="btn btn-warning btn-sm" disabled>
                                                Ödeme Bekleniyor
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="col-12">
                    <div class="card">
                        <div class="card-body no-courses">
                            <i class="fas fa-book-open fa-3x mb-3 text-muted"></i>
                            <h3>Henüz Kursunuz Bulunmuyor</h3>
                            <p class="text-muted">Hemen yeni bir kurs satın alarak öğrenmeye başlayın!</p>
                            <a href="courses.php" class="btn btn-primary">Kursları İncele</a>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Credentials Modal -->
<div class="modal fade" id="credentialsModal" tabindex="-1" aria-labelledby="credentialsModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="credentialsModalLabel">Kurs Erişim Bilgileri</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <h4 id="modal-kurs-title" class="mb-3"></h4>
        
        <div class="credentials-section">
          <div class="credential-item">
            <label>Kurs Linki:</label>
            <div class="credential-value" id="modal-link"></div>
          </div>
          
          <div class="credential-item">
            <label>Kullanıcı Adı / E-posta:</label>
            <div class="credential-value" id="modal-username"></div>
          </div>
          
          <div class="credential-item">
            <label>Şifre:</label>
            <div class="credential-value" id="modal-password"></div>
          </div>
          
          <div class="credential-item" id="notes-section">
            <label>Ek Notlar:</label>
            <div class="credential-value" id="modal-notlar"></div>
          </div>
        </div>
        
        <div class="alert alert-info mt-4">
          <i class="fa-solid fa-circle-info me-2"></i>
          Yukarıdaki bilgileri kullanarak kursa giriş yapabilirsiniz. Erişim bilgilerinizi güvenli bir yerde saklayın.
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Kapat</button>
        <a href="#" class="btn btn-primary" id="direct-access-btn" target="_blank">Kursa Git</a>
      </div>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
  // Handle showing credentials in modal
  const credentialsModal = document.getElementById('credentialsModal');
  if (credentialsModal) {
    credentialsModal.addEventListener('show.bs.modal', function (event) {
      const button = event.relatedTarget;
      
      // Extract info from data attributes
      const kursTitle = button.getAttribute('data-kurs-title');
      const link = button.getAttribute('data-link');
      const username = button.getAttribute('data-username');
      const password = button.getAttribute('data-password');
      const notlar = button.getAttribute('data-notlar');
      
      // Update modal content
      document.getElementById('modal-kurs-title').textContent = kursTitle;
      document.getElementById('modal-link').textContent = link;
      document.getElementById('modal-username').textContent = username;
      document.getElementById('modal-password').textContent = password;
      
      // Update direct access button
      document.getElementById('direct-access-btn').href = link;
      
      // Handle notes (show/hide based on content)
      const notesSection = document.getElementById('notes-section');
      const notesContent = document.getElementById('modal-notlar');
      
      if (notlar && notlar.trim() !== '') {
        notesContent.textContent = notlar;
        notesSection.style.display = 'block';
      } else {
        notesSection.style.display = 'none';
      }
    });
  }
});
</script>

<?php include 'footer.php'; ?> 