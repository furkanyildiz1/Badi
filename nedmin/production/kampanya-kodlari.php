<?php 
include 'header.php';

// Fetch all campaign codes
$kampanyasor = $db->prepare("SELECT * FROM kampanya_kodlari ORDER BY olusturma_tarihi DESC");
$kampanyasor->execute();
?>

<div class="right_col" role="main">
    <div class="">
        <div class="clearfix"></div>
        <div class="row">
            <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel">
                    <div class="x_title">
                        <h2>Kampanya Kodları <small>
                            <?php 
                            if($_GET['durum']=="ok") { echo "<b style='color:green;'>İşlem Başarılı...</b>"; }
                            elseif($_GET['durum']=="no") { echo "<b style='color:red;'>İşlem Başarısız...</b>"; }
                            ?>
                        </small></h2>
                        <div class="clearfix"></div>
                        <div align="right">
                            <button class="btn btn-success" data-toggle="modal" data-target="#addCouponModal">
                                <i class="fa fa-plus"></i> Yeni Ekle
                            </button>
                        </div>
                    </div>
                    <div class="x_content">
                        <table id="datatable-responsive" class="table table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Kampanya Kodu</th>
                                    <th>İndirim</th>
                                    <th>Alt Limit</th>
                                    <th>Başlangıç Tarihi</th>
                                    <th>Bitiş Tarihi</th>
                                    <th>Durum</th>
                                    <th>İşlemler</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while($kampanyacek=$kampanyasor->fetch(PDO::FETCH_ASSOC)) { ?>
                                    <tr>
                                        <td><?php echo $kampanyacek['kampanya_kodu_id']; ?></td>
                                        <td><?php echo $kampanyacek['kod']; ?></td>
                                        <td>
                                            <?php 
                                            if(isset($kampanyacek['indirim_orani'])) {
                                                echo '%' . $kampanyacek['indirim_orani'];
                                            } else {
                                                echo number_format($kampanyacek['indirim_tutari'], 2, ',', '.') . ' TL';
                                            }
                                            ?>
                                        </td>
                                        <td>
                                            <?php 
                                            echo $kampanyacek['alt_limit'] 
                                                ? number_format($kampanyacek['alt_limit'], 2, ',', '.') . ' TL' 
                                                : '-';
                                            ?>
                                        </td>
                                        <td><?php echo $kampanyacek['gecerli_baslangic']; ?></td>
                                        <td><?php echo $kampanyacek['gecerli_bitis'] ?? 'Süresiz'; ?></td>
                                        <td>
                                            <span class="badge <?php echo $kampanyacek['durum'] == 'aktif' ? 'badge-success' : 'badge-danger'; ?>">
                                                <?php echo $kampanyacek['durum']; ?>
                                            </span>
                                        </td>
                                        <td>
                                            <button class="btn btn-primary btn-xs" onclick="editCoupon(<?php echo $kampanyacek['kampanya_kodu_id']; ?>)">
                                                <i class="fa fa-pencil"></i> Düzenle
                                            </button>
                                            <a href="../netting/islem.php?kampanya_kodu_id=<?php echo $kampanyacek['kampanya_kodu_id']; ?>&kampanya_sil=ok" 
                                               class="btn btn-danger btn-xs" onclick="return confirm('Silmek istediğinize emin misiniz?')">
                                                <i class="fa fa-trash"></i> Sil
                                            </a>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Coupon Modal -->
<div class="modal fade" id="addCouponModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Yeni Kampanya Kodu Ekle</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form action="../netting/islem.php" method="POST" id="addCouponForm" onsubmit="return validateCouponForm(this);">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Kampanya Kodu</label>
                        <input type="text" class="form-control" name="kod" required>
                    </div>
                    <div class="form-group">
                        <label>İndirim Tipi</label>
                        <select class="form-control" name="indirim_tipi" onchange="toggleDiscountFields(this)">
                            <option value="yuzde">Yüzdelik İndirim</option>
                            <option value="tutar">Sabit Tutar İndirim</option>
                        </select>
                    </div>
                    <div class="form-group" id="yuzdeIndirimGroup">
                        <label>İndirim Oranı (%)</label>
                        <input type="number" class="form-control" name="indirim_yuzdesi" min="0" max="100" step="0.01">
                    </div>
                    <div class="form-group" id="tutarIndirimGroup" style="display:none;">
                        <label>İndirim Tutarı (TL)</label>
                        <input type="number" class="form-control" name="indirim_tutari" min="0" step="0.01">
                    </div>
                    <div class="form-group">
                        <label>Alt Limit (TL) (Opsiyonel)</label>
                        <input type="number" class="form-control" name="alt_limit" min="0" step="0.01">
                        <small class="text-muted">Boş bırakılırsa alt limit uygulanmaz</small>
                    </div>
                    <div class="form-group">
                        <label>Başlangıç Tarihi</label>
                        <input type="datetime-local" class="form-control" name="gecerli_baslangic" required>
                    </div>
                    <div class="form-group">
                        <label>Bitiş Tarihi (Boş bırakılırsa süresiz olur)</label>
                        <input type="datetime-local" class="form-control" name="gecerli_bitis">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Kapat</button>
                    <button type="submit" name="kampanya_ekle" class="btn btn-primary">Kaydet</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Coupon Modal -->
<div class="modal fade" id="editCouponModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Kampanya Kodu Düzenle</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form action="../netting/islem.php" method="POST" id="editCouponForm" onsubmit="return validateCouponForm(this);">
                <input type="hidden" name="kampanya_kodu_id" id="edit_kampanya_id">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Kampanya Kodu</label>
                        <input type="text" class="form-control" name="kod" id="edit_kod" required>
                    </div>
                    <div class="form-group">
                        <label>İndirim Tipi</label>
                        <select class="form-control" name="indirim_tipi" id="edit_indirim_tipi" onchange="toggleDiscountFields(this, true)">
                            <option value="yuzde">Yüzdelik İndirim</option>
                            <option value="tutar">Sabit Tutar İndirim</option>
                        </select>
                    </div>
                    <div class="form-group" id="edit_yuzdeIndirimGroup">
                        <label>İndirim Oranı (%)</label>
                        <input type="number" class="form-control" name="indirim_yuzdesi" id="edit_indirim_yuzdesi" min="0" max="100" step="0.01">
                    </div>
                    <div class="form-group" id="edit_tutarIndirimGroup">
                        <label>İndirim Tutarı (TL)</label>
                        <input type="number" class="form-control" name="indirim_tutari" id="edit_indirim_tutari" min="0" step="0.01">
                    </div>
                    <div class="form-group">
                        <label>Alt Limit (TL) (Opsiyonel)</label>
                        <input type="number" class="form-control" name="alt_limit" id="edit_alt_limit" min="0" step="0.01">
                        <small class="text-muted">Boş bırakılırsa alt limit uygulanmaz</small>
                    </div>
                    <div class="form-group">
                        <label>Başlangıç Tarihi</label>
                        <input type="datetime-local" class="form-control" name="gecerli_baslangic" 
                               id="edit_gecerli_baslangic" required step="60">
                    </div>
                    <div class="form-group">
                        <label>Bitiş Tarihi</label>
                        <input type="datetime-local" class="form-control" name="gecerli_bitis" 
                               id="edit_gecerli_bitis" step="60">
                    </div>
                    <div class="form-group">
                        <label>Durum</label>
                        <select class="form-control" name="durum" id="edit_durum">
                            <option value="aktif">Aktif</option>
                            <option value="süresi dolmuş">Süresi Dolmuş</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Kapat</button>
                    <button type="submit" name="kampanya_duzenle" class="btn btn-primary">Güncelle</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function toggleDiscountFields(select, isEdit = false) {
    const prefix = isEdit ? 'edit_' : '';
    const yuzdeGroup = document.getElementById(prefix + 'yuzdeIndirimGroup');
    const tutarGroup = document.getElementById(prefix + 'tutarIndirimGroup');
    
    if (select.value === 'yuzde') {
        yuzdeGroup.style.display = 'block';
        tutarGroup.style.display = 'none';
        tutarGroup.querySelector('input').value = '';
    } else {
        yuzdeGroup.style.display = 'none';
        tutarGroup.style.display = 'block';
        yuzdeGroup.querySelector('input').value = '';
    }
}

function validateCouponForm(form) {
    const indirimTipi = form.querySelector('[name="indirim_tipi"]').value;
    const yuzdeInput = form.querySelector('[name="indirim_yuzdesi"]');
    const tutarInput = form.querySelector('[name="indirim_tutari"]');
    
    if (indirimTipi === 'yuzde' && (!yuzdeInput.value || yuzdeInput.value <= 0)) {
        alert('Lütfen geçerli bir indirim oranı giriniz.');
        return false;
    }
    
    if (indirimTipi === 'tutar' && (!tutarInput.value || tutarInput.value <= 0)) {
        alert('Lütfen geçerli bir indirim tutarı giriniz.');
        return false;
    }
    
    return true;
}

function editCoupon(id) {
    $.ajax({
        url: '../netting/islem.php',
        type: 'GET',
        data: { get_kampanya: id },
        success: function(response) {
            const kampanya = JSON.parse(response);
            $('#edit_kampanya_id').val(kampanya.kampanya_kodu_id);
            $('#edit_kod').val(kampanya.kod);
            
            // Set discount type and toggle fields
            const indirimTipi = kampanya.indirim_yuzdesi ? 'yuzde' : 'tutar';
            $('#edit_indirim_tipi').val(indirimTipi);
            toggleDiscountFields($('#edit_indirim_tipi')[0], true);
            
            $('#edit_indirim_yuzdesi').val(kampanya.indirim_yuzdesi);
            $('#edit_indirim_tutari').val(kampanya.indirim_tutari);
            $('#edit_alt_limit').val(kampanya.alt_limit);
            
            // Format datetime to remove seconds
            if(kampanya.gecerli_baslangic) {
                let baslangic = kampanya.gecerli_baslangic.replace(' ', 'T');
                baslangic = baslangic.substring(0, 16); // Remove seconds
                $('#edit_gecerli_baslangic').val(baslangic);
            }
            
            if(kampanya.gecerli_bitis) {
                let bitis = kampanya.gecerli_bitis.replace(' ', 'T');
                bitis = bitis.substring(0, 16); // Remove seconds
                $('#edit_gecerli_bitis').val(bitis);
            }
            
            $('#edit_durum').val(kampanya.durum);
            
            $('#editCouponModal').modal('show');
        }
    });
}
</script>

<?php include 'footer.php'; ?> 