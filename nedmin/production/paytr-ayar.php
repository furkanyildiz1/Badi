<?php 
include 'header.php';

$paytr_ayar = $db->query("SELECT * FROM paytr_ayar WHERE id = 1")->fetch(PDO::FETCH_ASSOC);
?>

<div class="right_col" role="main">
    <div class="">
        <div class="page-title">
            <div class="title_left">
                <h3>PayTR Ayarları</h3>
            </div>
        </div>

        <div class="clearfix"></div>

        <div class="row">
            <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel">
                    <div class="x_title">
                        <h2>PayTR Entegrasyon Bilgileri</h2>
                        <div class="clearfix"></div>
                    </div>
                    <div class="x_content">
                        <form action="../netting/islem.php" method="POST" class="form-horizontal form-label-left">
                            
                            <div class="form-group">
                                <label class="control-label col-md-3 col-sm-3 col-xs-12">Merchant ID <span class="required">*</span></label>
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <input type="text" name="merchant_id" required class="form-control col-md-7 col-xs-12" 
                                           value="<?php echo $paytr_ayar['merchant_id']; ?>">
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label col-md-3 col-sm-3 col-xs-12">Merchant Key <span class="required">*</span></label>
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <input type="text" name="merchant_key" required class="form-control col-md-7 col-xs-12" 
                                           value="<?php echo $paytr_ayar['merchant_key']; ?>">
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label col-md-3 col-sm-3 col-xs-12">Merchant Salt <span class="required">*</span></label>
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <input type="text" name="merchant_salt" required class="form-control col-md-7 col-xs-12" 
                                           value="<?php echo $paytr_ayar['merchant_salt']; ?>">
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label col-md-3 col-sm-3 col-xs-12">Test Modu</label>
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <select name="test_mode" class="form-control">
                                        <option value="0" <?php echo $paytr_ayar['test_mode'] == 0 ? 'selected' : ''; ?>>Kapalı</option>
                                        <option value="1" <?php echo $paytr_ayar['test_mode'] == 1 ? 'selected' : ''; ?>>Açık</option>
                                    </select>
                                    <small class="text-muted">
                                        Test modu açıkken kullanılabilecek test kartı:<br>
                                        Kart No: 9792030394440796<br>
                                        Son Kullanma: 12/26<br>
                                        CVV: 000
                                    </small>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label col-md-3 col-sm-3 col-xs-12">Taksit Seçeneği</label>
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <select name="installment_mode" class="form-control">
                                        <option value="0" <?php echo $paytr_ayar['installment_mode'] == 0 ? 'selected' : ''; ?>>Taksit Yok</option>
                                        <option value="1" <?php echo $paytr_ayar['installment_mode'] == 1 ? 'selected' : ''; ?>>Taksit Var</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label col-md-3 col-sm-3 col-xs-12">Maksimum Taksit</label>
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <input type="number" name="max_installment" class="form-control col-md-7 col-xs-12" 
                                           value="<?php echo $paytr_ayar['max_installment']; ?>" min="0" max="12">
                                    <small class="text-muted">0-12 arası bir değer girin. 0: Taksit yok</small>
                                </div>
                            </div>

                            <div class="ln_solid"></div>

                            <div class="form-group">
                                <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
                                    <button type="submit" name="paytr_ayar_kaydet" class="btn btn-success">Güncelle</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?> 