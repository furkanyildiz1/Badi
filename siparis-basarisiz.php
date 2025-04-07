<?php 
include 'header.php';
?>

<section class="checkout-area ptb-100">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="alert alert-danger">
                    <h4>Ödeme İşlemi Başarısız!</h4>
                    <p>Siparişiniz oluşturulurken bir hata meydana geldi. Bu durum aşağıdaki sebeplerden kaynaklanıyor olabilir:</p>
                    <ul>
                        <li>Kredi kartı bilgileriniz hatalı olabilir</li>
                        <li>Kartınızda yeterli bakiye bulunmuyor olabilir</li>
                        <li>Bankanız işlemi onaylamamış olabilir</li>
                        <li>Sistem kaynaklı geçici bir sorun oluşmuş olabilir</li>
                    </ul>
                    <p>Lütfen daha sonra tekrar deneyiniz. Sorun devam ederse <a href="contact.php" class="alert-link">bizimle iletişime</a> geçebilirsiniz.</p>
                </div>
                <div class="text-center mt-4">
                    <a href="cart.php" class="btn btn-primary">Sepete Geri Dön</a>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'footer.php'; ?> 