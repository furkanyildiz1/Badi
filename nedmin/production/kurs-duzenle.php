<?php 
include 'header.php';

$kurs_id = $_GET['kurs_id'];
$kurssor=$db->prepare("SELECT * FROM kurslar WHERE kurs_id=:id");
$kurssor->execute(['id' => $kurs_id]);
$kurscek=$kurssor->fetch(PDO::FETCH_ASSOC);
?>

<script src="https://cdn.ckeditor.com/ckeditor5/38.0.1/classic/ckeditor.js"></script>

<div class="right_col" role="main">
    <div class="">
        <div class="clearfix"></div>
        <div class="row">
            <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel">
                    <div class="x_title">
                        <h2>Kurs Düzenle <small>
                            <?php 
                            if($_GET['durum']=="ok") { ?>
                                <b style="color:green;">İşlem Başarılı...</b>
                            <?php } elseif($_GET['durum']=="no") { ?>
                                <b style="color:red;">İşlem Başarısız...</b>
                            <?php } ?>
                        </small></h2>
                        <div class="clearfix"></div>
                    </div>
                    <div class="x_content">
                        <form action="../netting/islem.php" method="POST" enctype="multipart/form-data" class="form-horizontal form-label-left">

                            <input type="hidden" name="kurs_id" value="<?php echo $kurscek['kurs_id']; ?>">
                            <input type="hidden" name="eski_resim" value="<?php echo $kurscek['resim_yol']; ?>">

                            <div class="form-group">
                                <label class="control-label col-md-3 col-sm-3 col-xs-12">Mevcut Resim</label>
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <img width="200" src="../../<?php echo $kurscek['resim_yol']; ?>" alt="">
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label col-md-3 col-sm-3 col-xs-12">Resim Seç</label>
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <input type="file" name="resim_yol" class="form-control">
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label col-md-3 col-sm-3 col-xs-12">Önizleme Videosu</label>
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <?php if(isset($kurscek['video_yol']) && $kurscek['video_yol'] != '') { ?>
                                        <div class="current-video mb-2">
                                            <label>Mevcut Video:</label>
                                            <video width="200" controls>
                                                <source src="../../<?php echo $kurscek['video_yol']; ?>" 
                                                        type="<?php echo mime_content_type("../../" . $kurscek['video_yol']); ?>">
                                                Tarayıcınız video elementini desteklemiyor.
                                            </video>
                                        </div>
                                    <?php } ?>
                                    <input type="hidden" name="eski_video" value="<?php echo $kurscek['video_yol']; ?>">
                                    <input type="file" name="onizleme_video" class="form-control" 
                                           accept="video/mp4,video/webm,video/ogg,video/quicktime,video/x-msvideo,video/x-ms-wmv">
                                    <small class="text-muted">Desteklenen formatlar: MP4, WebM, OGG, MOV, AVI, WMV</small>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label col-md-3 col-sm-3 col-xs-12">Kurs Başlık</label>
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <input type="text" name="baslik" value="<?php echo $kurscek['baslik']; ?>" 
                                           class="form-control" required>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label col-md-3 col-sm-3 col-xs-12">Kurs Açıklama</label>
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <textarea name="aciklama" id="editor1"><?php echo $kurscek['aciklama']; ?></textarea>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label col-md-3 col-sm-3 col-xs-12">Kurs Seviyesi</label>
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <select name="kurs_seviye_id" class="form-control" required>
                                        <option value="">Seviye Seçiniz</option>
                                        <?php 
                                        $seviyesor=$db->prepare("SELECT * FROM kurs_seviye ORDER BY seviye_ad ASC");
                                        $seviyesor->execute();
                                        while($seviyecek=$seviyesor->fetch(PDO::FETCH_ASSOC)) { 
                                            $selected = ($kurscek['kurs_seviye_id'] == $seviyecek['kurs_seviye_id']) ? 'selected' : '';
                                        ?>
                                            <option value="<?php echo $seviyecek['kurs_seviye_id']; ?>" <?php echo $selected; ?>>
                                                <?php echo $seviyecek['seviye_ad']; ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label col-md-3 col-sm-3 col-xs-12">Vitrin Durumu</label>
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <select name="vitrin_durum" class="form-control">
                                        <option value="1" <?php echo $kurscek['vitrin_durum'] == '1' ? 'selected' : ''; ?>>Aktif</option>
                                        <option value="0" <?php echo $kurscek['vitrin_durum'] == '0' ? 'selected' : ''; ?>>Pasif</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label col-md-3 col-sm-3 col-xs-12">Kurs Fiyatı</label>
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <input type="text" name="kurs_fiyat" value="<?php echo $kurscek['kurs_fiyat']; ?>" 
                                           class="form-control">
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label col-md-3 col-sm-3 col-xs-12">Kurs Türü</label>
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <select name="kurs_tur" class="form-control">
                                        <option value="canli" <?php echo $kurscek['kurs_tur'] == 'canli' ? 'selected' : ''; ?>>Canlı</option>
                                        <option value="online" <?php echo $kurscek['kurs_tur'] == 'online' ? 'selected' : ''; ?>>Online</option>
                                        <option value="yuzyuze" <?php echo $kurscek['kurs_tur'] == 'yuzyuze' ? 'selected' : ''; ?>>Yüzyüze</option>
                                    </select>
                                </div>
                            </div>

                            <div class="x_title">
                                <h2><strong>Sertifika Fiyatlandırması</strong></h2>
                                <div class="clearfix"></div>
                            </div>

                            <div class="form-group">
                                <label class="control-label col-md-3 col-sm-3 col-xs-12">Kurum Onaylı Sertifika Fiyatı</label>
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <input type="text" name="kurum_onayli_sertifika_fiyat" value="<?php echo $kurscek['kurum_onayli_sertifika_fiyat']; ?>" 
                                           class="form-control">
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label col-md-3 col-sm-3 col-xs-12">Üniversite Onaylı Sertifika Fiyatı</label>
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <input type="text" name="uni_onayli_sertifika_fiyat" value="<?php echo $kurscek['uni_onayli_sertifika_fiyat']; ?>" 
                                           class="form-control">
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label col-md-3 col-sm-3 col-xs-12">2 Sertifika Fiyatı</label>
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <input type="text" name="sertifikalar_birlikte_fiyat" value="<?php echo $kurscek['sertifikalar_birlikte_fiyat']; ?>" 
                                           class="form-control">
                                </div>
                            </div>

                            <div class="ln_solid"></div>
                            <div class="form-group">
                                <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
                                    <button type="submit" name="kurs_duzenle" class="btn btn-success">Güncelle</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

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

<?php include 'footer.php'; ?> 
