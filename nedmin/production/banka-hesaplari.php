<?php 
include 'header.php';
?>

<div class="right_col" role="main">
    <div class="">
        <div class="page-title">
            <div class="title_left">
                <h3>Banka Hesapları</h3>
            </div>
        </div>

        <div class="clearfix"></div>

        <div class="row">
            <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel">
                    <div class="x_title">
                        <h2>Banka Hesapları Listesi</h2>
                        <button type="button" class="btn btn-success pull-right" data-toggle="modal" data-target="#bankaEkleModal">
                            <i class="fa fa-plus"></i> Yeni Hesap Ekle
                        </button>
                        <div class="clearfix"></div>
                    </div>
                    <div class="x_content">
                        <table id="datatable-responsive" class="table table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
                            <thead>
                                <tr>
                                    <th>Banka Adı</th>
                                    <th>Hesap Sahibi</th>
                                    <th>Şube</th>
                                    <th>IBAN</th>
                                    <th>Durum</th>
                                    <th>İşlemler</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $stmt = $db->query("SELECT * FROM banka_hesaplari ORDER BY id DESC");
                                while($hesap = $stmt->fetch(PDO::FETCH_ASSOC)) { ?>
                                    <tr>
                                        <td><?php echo $hesap['banka_adi']; ?></td>
                                        <td><?php echo $hesap['hesap_sahibi']; ?></td>
                                        <td><?php echo $hesap['sube_adi']; ?> (<?php echo $hesap['sube_kodu']; ?>)</td>
                                        <td><?php echo $hesap['iban']; ?></td>
                                        <td><?php echo $hesap['durum'] == 1 ? 'Aktif' : 'Pasif'; ?></td>
                                        <td>
                                            <button type="button" class="btn btn-info btn-xs" 
                                                    onclick="hesapDuzenle(<?php echo htmlspecialchars(json_encode($hesap)); ?>)">
                                                <i class="fa fa-pencil"></i> Düzenle
                                            </button>
                                            <button type="button" class="btn btn-danger btn-xs" 
                                                    onclick="hesapSil(<?php echo $hesap['id']; ?>)">
                                                <i class="fa fa-trash"></i> Sil
                                            </button>
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

<!-- Ekle Modal -->
<div class="modal fade" id="bankaEkleModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Yeni Banka Hesabı Ekle</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="bankaEkleForm">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Banka Adı</label>
                        <input type="text" name="banka_adi" required class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Hesap Sahibi</label>
                        <input type="text" name="hesap_sahibi" required class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Şube Adı</label>
                        <input type="text" name="sube_adi" required class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Şube Kodu</label>
                        <input type="text" name="sube_kodu" required class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Hesap No</label>
                        <input type="text" name="hesap_no" required class="form-control">
                    </div>
                    <div class="form-group">
                        <label>IBAN</label>
                        <input type="text" name="iban" required class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Durum</label>
                        <select name="durum" class="form-control">
                            <option value="1">Aktif</option>
                            <option value="0">Pasif</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">İptal</button>
                    <button type="submit" class="btn btn-primary">Kaydet</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Düzenle Modal -->
<div class="modal fade" id="bankaDuzenleModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Banka Hesabı Düzenle</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="bankaDuzenleForm">
                <input type="hidden" name="id" id="duzenle_id">
                <div class="modal-body">
                    <!-- Same form fields as add modal -->
                    <div class="form-group">
                        <label>Banka Adı</label>
                        <input type="text" name="banka_adi" id="duzenle_banka_adi" required class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Hesap Sahibi</label>
                        <input type="text" name="hesap_sahibi" id="duzenle_hesap_sahibi" required class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Şube Adı</label>
                        <input type="text" name="sube_adi" id="duzenle_sube_adi" required class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Şube Kodu</label>
                        <input type="text" name="sube_kodu" id="duzenle_sube_kodu" required class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Hesap No</label>
                        <input type="text" name="hesap_no" id="duzenle_hesap_no" required class="form-control">
                    </div>
                    <div class="form-group">
                        <label>IBAN</label>
                        <input type="text" name="iban" id="duzenle_iban" required class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Durum</label>
                        <select name="durum" id="duzenle_durum" class="form-control">
                            <option value="1">Aktif</option>
                            <option value="0">Pasif</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">İptal</button>
                    <button type="submit" class="btn btn-primary">Güncelle</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Add form submit
    $('#bankaEkleForm').submit(function(e) {
        e.preventDefault();
        $.ajax({
            type: 'POST',
            url: '../netting/islem.php',
            data: $(this).serialize() + '&islem=banka_ekle',
            success: function(response) {
                console.log('Response:', response); // Debug line
                if(response.trim() == 'ok') {
                    Swal.fire({
                        title: 'Başarılı!',
                        text: 'Banka hesabı eklendi.',
                        icon: 'success'
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        title: 'Hata!',
                        text: 'Bir sorun oluştu.',
                        icon: 'error'
                    });
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', status, error); // Debug line
                Swal.fire({
                    title: 'Hata!',
                    text: 'Bir bağlantı hatası oluştu.',
                    icon: 'error'
                });
            }
        });
    });

    // Edit form submit
    $('#bankaDuzenleForm').submit(function(e) {
        e.preventDefault();
        $.ajax({
            type: 'POST',
            url: '../netting/islem.php',
            data: $(this).serialize() + '&islem=banka_duzenle',
            success: function(response) {
                if(response == 'ok') {
                    Swal.fire('Başarılı!', 'Banka hesabı güncellendi.', 'success')
                    .then(() => location.reload());
                } else {
                    Swal.fire('Hata!', 'Bir sorun oluştu.', 'error');
                }
            }
        });
    });
});

function hesapDuzenle(hesap) {
    $('#duzenle_id').val(hesap.id);
    $('#duzenle_banka_adi').val(hesap.banka_adi);
    $('#duzenle_hesap_sahibi').val(hesap.hesap_sahibi);
    $('#duzenle_sube_adi').val(hesap.sube_adi);
    $('#duzenle_sube_kodu').val(hesap.sube_kodu);
    $('#duzenle_hesap_no').val(hesap.hesap_no);
    $('#duzenle_iban').val(hesap.iban);
    $('#duzenle_durum').val(hesap.durum);
    $('#bankaDuzenleModal').modal('show');
}

function hesapSil(id) {
    Swal.fire({
        title: 'Emin misiniz?',
        text: "Bu hesap silinecek!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Evet, Sil!',
        cancelButtonText: 'İptal'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                type: 'POST',
                url: '../netting/islem.php',
                data: {
                    islem: 'banka_sil',
                    id: id
                },
                success: function(response) {
                    if(response == 'ok') {
                        Swal.fire('Silindi!', 'Banka hesabı silindi.', 'success')
                        .then(() => location.reload());
                    } else {
                        Swal.fire('Hata!', 'Bir sorun oluştu.', 'error');
                    }
                }
            });
        }
    });
}
</script>

<?php include 'footer.php'; ?> 