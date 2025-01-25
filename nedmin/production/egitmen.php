<?php 

error_reporting(0);

include 'header.php'; 

// Eğitmenleri çek
$egitmensor = $db->prepare("SELECT e.*, k.kullanici_mail, k.kullanici_tel 
                           FROM egitmen e
                           INNER JOIN kullanici k ON e.kullanici_id = k.kullanici_id 
                           ORDER BY e.egitmen_id DESC");
$egitmensor->execute();

?>

<!-- page content -->
<div class="right_col" role="main">
  <div class="">
    <div class="page-title">
      <div class="title_left">
        <h3>Eğitmenler</h3>
      </div>
    </div>

    <div class="clearfix"></div>

    <div class="row">
      <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="x_panel">
          <div class="x_title">
            <h2>Eğitmen Listesi 
              <small>
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
              </small>
            </h2>
            <div class="clearfix"></div>
          </div>
          <div class="x_content">

            <!-- Div İçerik Başlangıç -->

            <table id="datatable-responsive" class="table table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
                  <thead>
                    <tr>
                      <th>S.No</th>
                      <th>Ad Soyad</th>
                      <th>E-mail</th>
                      <th>Telefon</th>
                      <th>Uzmanlık Alanı</th>
                      <th>Resim</th>
                      <th>İşlemler</th>
                    </tr>
                  </thead>

                  <tbody>

                    <?php 
                    // Kursları döngü içinde listeleme
                    $say = 0;
                    while($egitmencek = $egitmensor->fetch(PDO::FETCH_ASSOC)) { 
                        $say++;
                    ?>
                    <tr>
                        <td><?php echo $say ?></td>
                        <td><?php echo $egitmencek['egitmen_adsoyad'] ?></td>
                        <td><?php echo $egitmencek['kullanici_mail'] ?></td>
                        <td><?php echo $egitmencek['kullanici_tel'] ?></td>
                        <td><?php echo $egitmencek['egitmen_rol'] ?></td>
                        <td>
                            <?php if(!empty($egitmencek['egitmen_resimyol'])) { ?>
                                <img src="../../<?php echo $egitmencek['egitmen_resimyol'] ?>" width="100">
                            <?php } else { ?>
                                <img src="../../dimg/egitmen-resim-yok.jpg" width="100">
                            <?php } ?>
                        </td>
                        <td>
                            <center>
                                <button onclick="egitmenDetay(<?php echo $egitmencek['egitmen_id'] ?>)" class="btn btn-primary btn-sm">
                                    <i class="fa fa-eye"></i> Detay
                                </button>
                                <a href="egitmen-duzenle.php?egitmen_id=<?php echo $egitmencek['egitmen_id'] ?>" class="btn btn-info btn-sm">
                                    <i class="fa fa-pencil"></i> Düzenle
                                </a>
                                <button onclick="egitmenSil(<?php echo $egitmencek['egitmen_id'] ?>)" class="btn btn-danger btn-sm">
                                    <i class="fa fa-trash"></i> Sil
                                </button>
                            </center>
                        </td>
                    </tr>
                    <?php } ?>

                  </tbody>
                </table>

<!-- SweetAlert için JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
  function egitmenDetay(egitmen_id) {
    $.ajax({
        url: '../netting/islem.php',
        type: 'POST',
        dataType: 'json',
        data: {
            egitmen_id: egitmen_id,
            egitmen_detay: 'ok'
        },
        success: function(response) {
            if(response.status === 'success') {
                const data = response.data;
                Swal.fire({
                    title: '<strong>Eğitmen Detayları</strong>',
                    html: `
                        <div class="egitmen-detay">
                            <div class="detay-grup">
                                <label>Ad Soyad:</label>
                                <p>${data.egitmen_adsoyad}</p>
                            </div>
                            <div class="detay-grup">
                                <label>Uzmanlık Alanı:</label>
                                <p>${data.egitmen_rol}</p>
                            </div>
                            <div class="detay-grup">
                                <label>Hakkında:</label>
                                <p>${data.egitmen_hakkinda}</p>
                            </div>
                        </div>
                    `,
                    width: '700px',
                    showCloseButton: true,
                    showConfirmButton: false
                });
            }
        }
    });
  }

  function egitmenSil(egitmen_id) {
    Swal.fire({
        title: 'Emin misiniz?',
        text: "Bu eğitmeni silmek istediğinize emin misiniz?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Evet, Sil',
        cancelButtonText: 'İptal'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = "../netting/islem.php?egitmen_id=" + egitmen_id + "&egitmen_sil=ok";
        }
    });
  }
</script>

<!-- Div İçerik Bitişi -->

          </div>
        </div>
      </div>
    </div>

  </div>
</div>
<!-- /page content -->

<?php include 'footer.php'; ?>  
