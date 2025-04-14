<?php

ob_start();
error_reporting(0);
include 'baglan.php';
include '../production/fonksiyon.php';
require '../../vendor/autoload.php';
use SendGrid\Mail\Mail;


// ADMIN GİRİS İSLEMİ BASLANGIC
if(isset($_POST['admingiris']))
{
	$kullanici_mail = $_POST['kullanici_mail'];
	$kullanici_password = md5($_POST['kullanici_password']);// md5 şifreleme

	$kullanicisor=$db->prepare("SELECT * FROM kullanici where kullanici_mail=:kullanici_mail AND kullanici_password=:kullanici_password AND kullanici_tip=:kullanici_yetki");
	$kullanicisor->execute([
		'kullanici_mail' => $kullanici_mail,
		'kullanici_password' => $kullanici_password,
		'kullanici_yetki' => "admin"
	]);

	echo $say=$kullanicisor->rowCount();

	if($say==1)
	{
		$_SESSION['kullanici_mail']=$kullanici_mail;
		header("Location: ../production/index.php");

		//session -> kullanici tarayıcıyı kapatana ya da cıkıs yapana kadar kullanici orada mı degil mi bilgilerini tutar! bunun icin de obstart ve session start komutlarının yazılmıs olması gerekir!

	}
	else
	{
		header("Location: ../production/login.php?durum=no");
		exit;
	}

}
// ADMIN GİRİS İSLEMİ BITIS


// GENEL AYAR GÜNCELLEME İSLEMİ BASLANGIC
if (isset($_POST['genelayarkaydet']))
{

	$ayarkaydet=$db->prepare("UPDATE ayar SET 
		ayar_title=:ayar_title,
		ayar_description=:ayar_description,
		ayar_keywords=:ayar_keywords,
		ayar_author=:ayar_author
		WHERE ayar_id=0");

	$update=$ayarkaydet->execute([
		'ayar_title' => $_POST['ayar_title'],
		'ayar_description' => $_POST['ayar_description'],
		'ayar_keywords' => $_POST['ayar_keywords'],
		'ayar_author' => $_POST['ayar_author']
	]);

	if($update)
	{
		header("Location: ../production/genel-ayar.php?durum=ok");
	}
	else
	{
		header("Location: ../production/genel-ayar.php?durum=no");
	}

}
// GENEL AYAR GÜNCELLEME İSLEMİ BİTİS


// İLETİSİM AYAR GÜNCELLEME İSLEMİ BASLANGIC
	if(isset($_POST['iletisimayarkaydet']))
	{
		$ayarkaydet=$db->prepare("UPDATE ayar SET
			ayar_tel=:ayar_tel,
			ayar_gsm=:ayar_gsm,
			ayar_faks=:ayar_faks,
			ayar_mail=:ayar_mail,
			ayar_ilce=:ayar_ilce,
			ayar_il=:ayar_il,
			ayar_adres=:ayar_adres,
			ayar_mesai=:ayar_mesai
			WHERE ayar_id=0");

		$update=$ayarkaydet->execute([
			'ayar_tel' => $_POST['ayar_tel'],
			'ayar_gsm' => $_POST['ayar_gsm'],
			'ayar_faks' => $_POST['ayar_faks'],
			'ayar_mail' => $_POST['ayar_mail'],
			'ayar_ilce' => $_POST['ayar_ilce'],
			'ayar_il' => $_POST['ayar_il'],
			'ayar_adres' => $_POST['ayar_adres'],
			'ayar_mesai' => $_POST['ayar_mesai']
	]);
		if($update)
		{
			header("Location: ../production/iletisim-ayar.php?durum=ok");
		}
		else
		{
			header("Location: ../production/iletisim-ayar.php?durum=no");
		}

	}
// İLETİSİM AYAR GÜNCELLEME İSLEMİ BİTİS


// API AYAR GÜNCELLEME İSLEMİ BASLANGIC
	if(isset($_POST['apiayarkaydet']))
	{
		$ayarkaydet=$db->prepare("UPDATE ayar SET
			ayar_maps=:ayar_maps,
			ayar_analystic=:ayar_analystic,
			ayar_zopim=:ayar_zopim
			WHERE ayar_id=0
			");

		$update=$ayarkaydet->execute([
			'ayar_maps' => $_POST['ayar_maps'],
			'ayar_analystic' => $_POST['ayar_analystic'],
			'ayar_zopim' => $_POST['ayar_zopim']
		]);		

		if($update)
		{
			header("Location: ../production/api-ayar.php?durum=ok");
		}
		else
		{
			header("Location: ../production/api-ayar.php?durum=no");
		}
	}
// API AYAR GÜNCELLEME İSLEMİ BİTİS


// SOSYAL AYAR GÜNCELLEME BASLANGIC
	if(isset($_POST['sosyalayarkaydet']))
	{
		$ayarkaydet=$db->prepare("UPDATE ayar SET
			ayar_facebook=:ayar_facebook,
			ayar_twitter=:ayar_twitter,
			ayar_google=:ayar_google,
			ayar_youtube=:ayar_youtube
			WHERE ayar_id=0
			");

		$update=$ayarkaydet->execute([
			'ayar_facebook' => $_POST['ayar_facebook'],
			'ayar_twitter' => $_POST['ayar_twitter'],
			'ayar_google' => $_POST['ayar_google'],
			'ayar_youtube' => $_POST['ayar_youtube']
		]);

		if($update)
		{
			header("Location: ../production/sosyal-ayar.php?durum=ok");
		}
		else
		{
			header("Location: ../production/sosyal-ayar.php?durum=no");
		}
	}
// SOSYAL AYAR GÜNCELLEME BİTİS


// MAIL AYAR GÜNCELLEME BASLANGIC
	if(isset($_POST['smtpayarkaydet']))
	{
		$ayarkaydet=$db->prepare("UPDATE ayar SET
			ayar_smtphost=:ayar_smtphost,
			ayar_smtpuser=:ayar_smtpuser,
			ayar_smtppassword=:ayar_smtppassword,
			ayar_smtpport=:ayar_smtpport
			WHERE ayar_id=0
			");

		$update=$ayarkaydet->execute([
			'ayar_smtphost' => $_POST['ayar_smtphost'],
			'ayar_smtpuser' => $_POST['ayar_smtpuser'],
			'ayar_smtppassword' => $_POST['ayar_smtppassword'],
			'ayar_smtpport' => $_POST['ayar_smtpport']
		]);

		if($update)
		{
			header("Location: ../production/mail-ayar.php?durum=ok");
		}
		else
		{
			header("Location: ../production/mail-ayar.php?durum=no");
		}
	}
// MAIL AYAR GÜNCELLEME BASLANGIC


// HAKKIMIZDA AYAR GÜNCELLEME BASLANGIC
	if(isset($_POST['hakkimizdakaydet']))
	{
		$hakkimizdakaydet=$db->prepare("UPDATE hakkimizda SET
			hakkimizda_baslik=:hakkimizda_baslik,
			hakkimizda_icerik=:hakkimizda_icerik,
			hakkimizda_vizyon=:hakkimizda_vizyon,
			hakkimizda_misyon=:hakkimizda_misyon
			WHERE hakkimizda_id=0
		");

		$update=$hakkimizdakaydet->execute([
			'hakkimizda_baslik' => $_POST['hakkimizda_baslik'],
			'hakkimizda_icerik' => $_POST['hakkimizda_icerik'],
			'hakkimizda_vizyon' => $_POST['hakkimizda_vizyon'],
			'hakkimizda_misyon' => $_POST['hakkimizda_misyon']
		]);

		if($update)
		{
			header("Location: ../production/hakkimizda.php?durum=ok");
		}
		else
		{
			header("Location: ../production/hakkimizda.php?durum=no");
		}
	}
// HAKKIMIZDA AYAR GÜNCELLEME BİTİS


// KULLANICI DÜZENLEME İSLEMLERİ BASLANGIC
	if(isset($_POST['kullanici_duzenle']))
	{
		$kullanici_id = $_POST['kullanici_id'];

		$kullaniciduzenle=$db->prepare("UPDATE kullanici SET
			kullanici_ad=:kullanici_ad,
            kullanici_soyad=:kullanici_soyad,
			kullanici_tel=:kullanici_gsm,
			kullanici_durum=:kullanici_durum
			WHERE kullanici_id={$_POST['kullanici_id']}
			"); // where satırında degisken kullanılacağı icin süslü parantez icine yazdı
		// AYRICA SÜTUNLARI BİRBİRİNE ESİTLERKEN DEMEK İSTEDİGİMİ ANLADIN ARALARA ASLA BOSLUK BIRAKMA

		$update=$kullaniciduzenle->execute([
			'kullanici_ad' => $_POST['kullanici_ad'],
            'kullanici_soyad' => $_POST['kullanici_soyad'],
			'kullanici_gsm' => $_POST['kullanici_gsm'],
			'kullanici_durum' => $_POST['kullanici_durum']
		]);

		if($update)
		{
			header("Location: ../production/kullanici-duzenle.php?kullanici_id=$kullanici_id&durum=ok");
		}
		else
		{
			header("Location: ../production/kullanici-duzenle.php?kullanici_id=$kullanici_id&durum=no");
		}
	}
// KULLANICI DÜZENLEME İSLEMLERİ BASLANGIC


// KULLANICI "SİL"ME İSLEMLERİ BASLANGIC
if($_GET['kullanicisil'] == "ok")  // get kullanırken isset kullanmayız
{
	$kullanicisil=$db->prepare("DELETE FROM kullanici WHERE kullanici_id=:kullanici_id");
	$kontrol=$kullanicisil->execute([
		'kullanici_id' => $_GET['kullanici_id']
	]); 

	if($kontrol)
	{
		header("Location: ../production/kullanici.php?sil=ok");
	}
	else
	{
		header("Location: ../production/kullanici.php?sil=no");
	}
}
// KULLANICI SİLME İSLEMLERİ BİTİS


// MENU GUNCELLEME İSLEMİ BASLANGIC
	if(isset($_POST['menu_duzenle']))
	{
		$menu_id = $_POST['menu_id'];
		$menu_seourl = seo($_POST['menu_ad']);

		$menuguncelle=$db->prepare("UPDATE menu SET
			menu_ad=:menu_ad,
			menu_detay=:menu_detay,
			menu_url=:menu_url,
			menu_sira=:menu_sira,
			menu_seourl=:menu_seourl,
			menu_durum=:menu_durum
			WHERE menu_id = {$_POST['menu_id']}
		");

		$update=$menuguncelle->execute([
			'menu_ad' => $_POST['menu_ad'],
			'menu_detay' => $_POST['menu_detay'],
			'menu_url' => $_POST['menu_url'],
			'menu_sira' => $_POST['menu_sira'],
			'menu_seourl' => $menu_seourl,
			'menu_durum' => $_POST['menu_durum']
		]);

		if($update)
		{
			header("Location: ../production/menu-duzenle.php?menu_id=$menu_id&durum=ok");
		}
		else
		{
			header("Location: ../production/menu-duzenle.php?menu_id=$menu_id&durum=no");
		}
	}
// MENU GUNCELLEME İSLEMİ BİTİS


// MENU SİLME İSLEMİ BASLANGIC
	if($_GET['menusil'] == "ok")
	{
		$menusil=$db->prepare("DELETE FROM menu WHERE menu_id=:menu_id");
		$kontrol=$menusil->execute([
			'menu_id' => $_GET['menu_id']
		]);

		if($kontrol)
		{
			header("Location: ../production/menu.php?sil=ok");
		}
		else
		{
			header("Location: ../production/menu.php?sil=no");
		}
	}
// MENU SİLME İSLEMİ BİTİS


// MENU EKLEME İSLEMİ BASLANGIC
	if(isset($_POST['menukaydet']))
	{
		$menu_seourl = seo($_POST['menu_ad']);

		$menuekle=$db->prepare("INSERT INTO menu SET
			menu_ad=:menu_ad,
			menu_detay=:menu_detay,
			menu_url=:menu_url,
			menu_sira=:menu_sira,
			menu_seourl=:menu_seourl,
			menu_durum=:menu_durum
		");

		$kaydet=$menuekle->execute([
			'menu_ad' => $_POST['menu_ad'],
			'menu_detay' => $_POST['menu_detay'],
			'menu_url' => $_POST['menu_url'],
			'menu_sira' => $_POST['menu_sira'],
			'menu_seourl' => $menu_seourl,
			'menu_durum' => $_POST['menu_durum']
		]);

		if($kaydet)
		{
			header("Location: ../production/menu.php?durum=ok");
		}
		else
		{
			header("Location: ../production/menu.php?durum=no");
		}
	}
// MENU EKLEME İSLEMİ BITIS


// LOGO KAYDETME İSLEMİ BASLANGIC
	if(isset($_POST['logoduzenle']))
	{
		$uploads_dir = '../../dimg';

		$tmp_name = $_FILES['ayar_logo']["tmp_name"];
		$name = $_FILES['ayar_logo']["name"];

		$benzersizsayi4=rand(20000,32000);
		$refimgyol=substr($uploads_dir, 6) ."/". $benzersizsayi4.$name;

		move_uploaded_file($tmp_name, "$uploads_dir/$benzersizsayi4$name");

		$logokaydet=$db->prepare("UPDATE ayar SET
			ayar_logo=:ayar_logo
			WHERE ayar_id=0");

		$update=$logokaydet->execute([
			'ayar_logo' => $refimgyol
		]);

		if($update)
		{
			$resimsilunlink= $_POST['eski_yol']; //unlink eski resimi silmek icin kullanılır
			unlink("../../$resimsilunlink");

			header("Location: ../production/genel-ayar.php?durum=ok");
		}
		else
		{
			header("Location: ../production/genel-ayar.php?durum=no");
		}
	}
// LOGO KAYDETME İSLEMİ BITIS


// FAVICON KAYDETME İSLEMİ BASLANGIC
	if(isset($_POST['faviconduzenle']))
	{
		$uploads_dir = '../../dimg/favicon';

		$tmp_name = $_FILES['ayar_favicon']["tmp_name"];
		$name = $_FILES['ayar_favicon']["name"];

		$benzersizsayi4=rand(20000,32000);
		$refimgyol=substr($uploads_dir, 6) ."/". $benzersizsayi4.$name;

		move_uploaded_file($tmp_name, "$uploads_dir/$benzersizsayi4$name");

		$logokaydet=$db->prepare("UPDATE ayar SET
			ayar_favicon=:ayar_favicon
			WHERE ayar_id=0");

		$update=$logokaydet->execute([
			'ayar_favicon' => $refimgyol
		]);

		if($update)
		{
			$resimsilunlink= $_POST['eski_faviconyol']; //unlink eski resimi silmek icin kullanılır
			unlink("../../$resimsilunlink");

			header("Location: ../production/genel-ayar.php?durum=ok");
		}
		else
		{
			header("Location: ../production/genel-ayar.php?durum=no");
		}
	}
// FAVICON KAYDETME İSLEMİ BITIS


// SLIDER KAYDETME İSLEMİ BASLANGIC
	if(isset($_POST['sliderkaydet']))
	{
		$uploads_dir = '../../dimg/slider';

		$tmp_name = $_FILES['slider_resimyol']["tmp_name"];
		$name = $_FILES['slider_resimyol']["name"];

		$benzersizsayi1=rand(20000,32000);
		$benzersizsayi2=rand(20000,32000);
		$benzersizsayi3=rand(20000,32000);
		$benzersizsayi4=rand(20000,32000);

		$benzersizad=$benzersizsayi1.$benzersizsayi2.$benzersizsayi3.$benzersizsayi4;
		$refimgyol=substr($uploads_dir, 6)."/".$benzersizad.$name;

		move_uploaded_file($tmp_name, "$uploads_dir/$benzersizad$name");


		$sliderkaydet=$db->prepare("INSERT INTO slider SET
			slider_ad=:slider_ad,
			slider_sira=:slider_sira,
			slider_link=:slider_link,
			slider_resimyol=:slider_resimyol,
			slider_durum=:slider_durum
		");

		$update=$sliderkaydet->execute([
			'slider_ad' => $_POST['slider_ad'],
			'slider_sira' => $_POST['slider_sira'],
			'slider_link' => $_POST['slider_link'],
			'slider_resimyol' => $refimgyol,
			'slider_durum' => $_POST['slider_durum']
		]);

		if($update)
		{
			header("Location: ../production/slider.php?durum=ok");
		}
		else
		{
			header("Location: ../production/slider.php?durum=no");
		}
	}
// SLIDER KAYDETME İSLEMİ BITIS


// SLIDER RESIM DUZENLEME İSLEMİ BASLANGIC
	if(isset($_POST['slider_resim_duzenle']))
	{
		$slider_id = $_POST['slider_id'];

		$uploads_dir = '../../dimg/slider';

		$tmp_name = $_FILES['slider_resimyol']["tmp_name"];
		$name = $_FILES['slider_resimyol']["name"];

		$benzersizsayi1=rand(20000, 32000);
		$benzersizsayi2=rand(20000, 32000);
		$benzersizsayi3=rand(20000, 32000);
		$benzersizsayi4=rand(20000, 32000);

		$benzersizad = $benzersizsayi1.$benzersizsayi2.$benzersizsayi3.$benzersizsayi4;
		$refimgyol = substr($uploads_dir, 6)."/".$benzersizad.$name;

		move_uploaded_file($tmp_name, "$uploads_dir/$benzersizad$name");

		$sliderresim = $db->prepare("UPDATE slider SET
			slider_resimyol=:slider_resimyol
			WHERE slider_id={$_POST['slider_id']} 
		");

		$update=$sliderresim->execute([
			'slider_resimyol' => $refimgyol
		]);	

		if($update)
		{
			$sliderresimsilunlink= $_POST['slidereskiresim_yol']; 
			unlink("../../$sliderresimsilunlink");

			header("Location: ../production/slider-duzenle.php?slider_id=$slider_id&durum=ok");
		}
		else
		{
			header("Location: ../production/slider-duzenle.php?slider_id=$slider_id&durum=no");
		}
	}	
// SLIDER RESİM DUZENLEME İSLEMİ BİTİS


// SLIDER DUZENLEME İSLEMİ BASLANGIC
	if(isset($_POST['slider_duzenle']))
	{
		$slider_id = $_POST['slider_id'];

		$sliderduzenle=$db->prepare("UPDATE slider SET
			slider_ad=:slider_ad,
			slider_sira=:slider_sira,
			slider_link=:slider_link,
			slider_durum=:slider_durum
			WHERE slider_id={$_POST['slider_id']}
		");

		$update=$sliderduzenle->execute([
			'slider_ad' => $_POST['slider_ad'],
			'slider_sira' => $_POST['slider_sira'],
			'slider_link' => $_POST['slider_link'],
			'slider_durum' => $_POST['slider_durum']
		]);

		if($update)
		{
			header("Location: ../production/slider-duzenle.php?slider_id=$slider_id&durum=ok");
		}
		else
		{
			header("Location: ../production/slider-duzenle.php?slider_id=$slider_id&durum=no");
		}
	}
// SLIDER DUZENLEME İSLEMİ BITIS


// SLIDER SİLME İSLEMİ BASLANGIC
	if($_GET['slidersil']=="ok")
	{
		$slidersil=$db->prepare("DELETE FROM slider WHERE slider_id=:slider_id");
		$delete=$slidersil->execute([
			'slider_id' => $_GET['slider_id']
		]);

		if($delete)
		{
			header("Location: ../production/slider.php?durum=ok");
		}
		else
		{
			header("Location: ../production/slider.php?durum=no");
		}
	}
// SLIDER SİLME İSLEMİ BASLANGIC


// KULLANICI GİRİŞ İŞLEMİ BAŞLANGIÇ
if(isset($_POST['kullanicigiris'])) {
    
    $kullanici_mail = htmlspecialchars($_POST['email']);
    $kullanici_password = md5($_POST['password']);

    $kullanicisor=$db->prepare("SELECT * FROM kullanici WHERE 
        kullanici_mail=:mail AND 
        kullanici_password=:password");
    
    $kullanicisor->execute([
        'mail' => $kullanici_mail,
        'password' => $kullanici_password
    ]);

    $say=$kullanicisor->rowCount();
    $kullanici=$kullanicisor->fetch(PDO::FETCH_ASSOC);

    if($say==1) {
        // Kullanıcı bulundu, durum kontrolü yapalım
        if($kullanici['kullanici_durum'] == 'pending') {
            // Eğitmen onayı bekleyen kullanıcı
            header("Location:../../login.php?durum=onaybekliyor");
            exit;
        } else if($kullanici['kullanici_durum'] == 'passive') {
            // Pasif kullanıcı
            header("Location:../../login.php?durum=pasifhesap");
            exit;
        } else {
            // Aktif kullanıcı, giriş yaptıralım
            $_SESSION['userkullanici_mail'] = $kullanici_mail;
            $_SESSION['userkullanici_id'] = $kullanici['kullanici_id'];
            $_SESSION['userkullanici_tip'] = $kullanici['kullanici_tip'];
			$_SESSION['userkullanici_adsoyad'] = $kullanici['kullanici_ad'] . " " . $kullanici['kullanici_soyad'];
            
            // Kullanıcı tipine göre yönlendirme
            if($kullanici['kullanici_tip'] == 'egitmen') {
                header("Location:../../index.php");
            } else {
                header("Location:../../index.php");
            }
        }
    } else {
        header("Location:../../login.php?durum=basarisizgiris");
    }
}
// KULLANICI GİRİŞ İŞLEMİ BİTİŞ


// KULLANICI KAYIT İŞLEMİ BAŞLANGIÇ
if(isset($_POST['kullanicikaydet'])) {
    
    // Gelen verileri güvenli hale getirme
    $kullanici_ad = htmlspecialchars($_POST['firstname']);
    $kullanici_soyad = htmlspecialchars($_POST['lastname']);
    $kullanici_mail = htmlspecialchars($_POST['email']);
    $kullanici_tel = htmlspecialchars($_POST['phone']);
    $kullanici_password = md5($_POST['password']); // Şifreyi md5 ile şifreleme
    $kullanici_tip = 'ogrenci'; // Öğrenci formu için sabit değer

    // Önce mail adresi kontrolü yapalım
    $mailsor=$db->prepare("SELECT * FROM kullanici WHERE kullanici_mail=:mail");
    $mailsor->execute([
        'mail' => $kullanici_mail
    ]);

    $say=$mailsor->rowCount();

    if($say==0) {
        // Mail adresi kullanılmamışsa kayıt işlemini gerçekleştir
        $kullanicikaydet=$db->prepare("INSERT INTO kullanici SET
            kullanici_ad=:kullanici_ad,
            kullanici_soyad=:kullanici_soyad,
            kullanici_mail=:kullanici_mail,
            kullanici_tel=:kullanici_tel,
            kullanici_password=:kullanici_password,
            kullanici_tip=:kullanici_tip,
            kullanici_durum=:kullanici_durum
        ");

        $insert=$kullanicikaydet->execute([
            'kullanici_ad' => $kullanici_ad,
            'kullanici_soyad' => $kullanici_soyad,
            'kullanici_mail' => $kullanici_mail,
            'kullanici_tel' => $kullanici_tel,
            'kullanici_password' => $kullanici_password,
            'kullanici_tip' => $kullanici_tip,
            'kullanici_durum' => "active"
        ]);

        if($insert) {
            header("Location:../../login.php?durum=kayitbasarili");
        } else {
            header("Location:../../register.php?durum=basarisiz");
        }

    } else {
        // Mail adresi kullanılmışsa hata ver
        header("Location:../../register.php?durum=mukerrerkayit");
    }
}
// KULLANICI KAYIT İŞLEMİ BİTİŞ


// EĞİTMEN KAYIT İŞLEMİ BAŞLANGIÇ
if(isset($_POST['egitmenrekaydet'])) {
    
    // Gelen verileri güvenli hale getirme
    $kullanici_ad = htmlspecialchars($_POST['firstname']);
    $kullanici_soyad = htmlspecialchars($_POST['lastname']);
    $kullanici_mail = htmlspecialchars($_POST['email']);
    $kullanici_tel = htmlspecialchars($_POST['phone']);
    $kullanici_password = md5($_POST['password']);
    $kullanici_tip = 'egitmen';
    
    // Mail kontrolü
    $mailsor=$db->prepare("SELECT * FROM kullanici WHERE kullanici_mail=:mail");
    $mailsor->execute([
        'mail' => $kullanici_mail
    ]);

    $say=$mailsor->rowCount();

    if($say==0) {
        // 1. AŞAMA: Kullanıcı kaydı
        $kullanicikaydet=$db->prepare("INSERT INTO kullanici SET
            kullanici_ad=:kullanici_ad,
            kullanici_soyad=:kullanici_soyad,
            kullanici_mail=:kullanici_mail,
            kullanici_tel=:kullanici_tel,
            kullanici_password=:kullanici_password,
            kullanici_tip=:kullanici_tip,
            kullanici_durum=:kullanici_durum
        ");

        $insert=$kullanicikaydet->execute([
            'kullanici_ad' => $kullanici_ad,
            'kullanici_soyad' => $kullanici_soyad,
            'kullanici_mail' => $kullanici_mail,
            'kullanici_tel' => $kullanici_tel,
            'kullanici_password' => $kullanici_password,
            'kullanici_tip' => $kullanici_tip,
            'kullanici_durum' => 'pending'
        ]);

        if($insert) {
            // Yeni eklenen kullanıcının ID'sini al
            $son_id = $db->lastInsertId();

            // CV dosyası işlemleri
            $uploads_dir = '../../dimg/cv';
            if (!file_exists($uploads_dir)) {
                mkdir($uploads_dir, 0777, true);
            }

            $tmp_name = $_FILES['cv']["tmp_name"];
            $name = $_FILES['cv']["name"];
            $benzersizsayi = rand(20000, 32000);
            $refimgyol = substr($uploads_dir, 6) . "/" . $benzersizsayi . $name;

            // Dosyayı yükle
            if(move_uploaded_file($tmp_name, "$uploads_dir/$benzersizsayi$name")) {
                
                // 2. AŞAMA: Eğitmen başvuru bilgilerini kaydet
                $basvurukaydet=$db->prepare("INSERT INTO egitmen_basvuru SET
                    kullanici_id=:kullanici_id,
                    uzmanlik_alani=:uzmanlik_alani,
                    hakkinda=:hakkinda,
                    cv_yol=:cv_yol,
                    basvuru_durum=:basvuru_durum
                ");

                $basvuru=$basvurukaydet->execute([
                    'kullanici_id' => $son_id,
                    'uzmanlik_alani' => htmlspecialchars($_POST['expertise']),
                    'hakkinda' => htmlspecialchars($_POST['experience']),
                    'cv_yol' => $refimgyol,
                    'basvuru_durum' => 'pending'
                ]);

                if($basvuru) {
                    header("Location:../../login.php?durum=egitmenonaybekliyor");
                } else {
                    // Başvuru kaydedilemezse kullanıcı kaydını da sil
                    $kullanicisil=$db->prepare("DELETE FROM kullanici WHERE kullanici_id=:id");
                    $kullanicisil->execute(['id' => $son_id]);
                    header("Location:../../register.php?durum=basarisiz");
                }
            } else {
                // Dosya yüklenemezse kullanıcı kaydını sil
                $kullanicisil=$db->prepare("DELETE FROM kullanici WHERE kullanici_id=:id");
                $kullanicisil->execute(['id' => $son_id]);
                header("Location:../../register.php?durum=dosyayuklenemedi");
            }
        } else {
            header("Location:../../register.php?durum=basarisiz");
        }
    } else {
        header("Location:../../register.php?durum=mukerrerkayit");
    }
}
// EĞİTMEN KAYIT İŞLEMİ BİTİŞ


// KATEGORİ KAYDETME İSLEMİ BASLANGIC
	if(isset($_POST['kategorikaydet']))
	{
		$kaydet=$db->prepare("INSERT INTO kategoriler SET
			ad=:ad,
			aciklama=:aciklama
		");

		$insert=$kaydet->execute([
			'ad' => $_POST['kategori_ad'],
			'aciklama' => $_POST['kategori_aciklama']
		]);

		if($insert)
		{
			header("Location: ../production/kategori.php?durum=ok");
		}
		else
		{
			header("Location: ../production/kategori.php?durum=no");
		}
	}
// KATEGORİ KAYDETME İSLEMİ BİTİS


// KATEGORİ SİLME İSLEMİ BASLANGIC
if($_GET['kategorisil'] == "ok")  // get kullanırken isset kullanmayız
{
	$kategorisil=$db->prepare("DELETE FROM kategoriler WHERE kategori_id=:kategori_id");
	$kontrol=$kategorisil->execute([
		'kategori_id' => $_GET['kategori_id']
	]); 

	if($kontrol)
	{
		header("Location: ../production/kategori.php?sil=ok");
	}
	else
	{
		header("Location: ../production/kategori.php?sil=no");
	}
}
// KATEGORİ SİLME İSLEMİ BİTİS


// KATEGORİ DÜZENLEME İSLEMİ BASLANGIC
if (isset($_POST['kategori_duzenle']))
{
	$kategori_id=$_POST['kategori_id'];

	$kategorikaydet=$db->prepare("UPDATE kategoriler SET 
		ad=:ad,
		aciklama=:aciklama
		WHERE kategori_id={$_POST['kategori_id']}");

	$update=$kategorikaydet->execute([
		'ad' => $_POST['kategori_ad'],
		'aciklama' => $_POST['kategori_aciklama']
	]);

	if($update)
	{
		header("Location: ../production/kategori-duzenle.php?kategori_id=$kategori_id&durum=ok");
	}
	else
	{
		header("Location: ../production/kategori-duzenle.php?kategori_id=$kategori_id&durum=no");
	}
}
// KATEGORİ DÜZENLEME İSLEMİ BİTİS

// ALT KATEGORİ KAYDETME İSLEMİ BASLANGIC
	if(isset($_POST['altkategorikaydet']))
	{
		$uploads_dir = '../../dimg/altkategori';

		$tmp_name = $_FILES['altkategori_resimyol']["tmp_name"];
		$name = $_FILES['altkategori_resimyol']["name"];

		$benzersizsayi1=rand(20000, 32000);
		$benzersizsayi2=rand(20000, 32000);
		$benzersizsayi3=rand(20000, 32000);
		$benzersizsayi4=rand(20000, 32000);

		$benzersizad = $benzersizsayi1.$benzersizsayi2.$benzersizsayi3.$benzersizsayi4;
		$refimgyol = substr($uploads_dir, 6)."/".$benzersizad.$name;

		move_uploaded_file($tmp_name, "$uploads_dir/$benzersizad$name");

		$kaydet=$db->prepare("INSERT INTO alt_kategoriler SET
			ad=:ad,
			kategori_id=:kategori_id,
			aciklama=:aciklama,
			fav_icon_resimyol=:fav_icon_resimyol
		");

		$insert=$kaydet->execute([
			'ad' => $_POST['altkategori_ad'],
			'kategori_id' => $_POST['kategori_id'],
			'aciklama' => $_POST['altkategori_aciklama'],
			'fav_icon_resimyol' => $refimgyol
		]);

		if($insert)
		{
			header("Location: ../production/alt-kategori.php?durum=ok");
		}
		else
		{
			header("Location: ../production/alt-kategori.php?durum=no");
		}
	}
// ALT KATEGORİ KAYDETME İSLEMİ BİTİS

// ALT KATEGORİ DÜZENLEME İSLEMİ BASLANGIC
if (isset($_POST['alt_kategori_duzenle']))
{
	$alt_kategori_id=$_POST['alt_kategori_id'];

	$alt_kategorikaydet=$db->prepare("UPDATE alt_kategoriler SET 
		ad=:ad,
		kategori_id=:kategori_id,
		aciklama=:aciklama
		WHERE alt_kategori_id={$_POST['alt_kategori_id']}");

	$update=$alt_kategorikaydet->execute([
		'ad' => $_POST['alt_kategori_ad'],
		'kategori_id' => $_POST['kategori_id'],
		'aciklama' => $_POST['alt_kategori_aciklama']
	]);

	if($update)
	{
		header("Location: ../production/alt-kategori-duzenle.php?alt_kategori_id=$alt_kategori_id&durum=ok");
	}
	else
	{
		header("Location: ../production/alt-kategori-duzenle.php?alt_kategori_id=$alt_kategori_id&durum=no");
	}
}
// ALT KATEGORİ DÜZENLEME İSLEMİ BİTİS

// ALT KATEGORİ SİLME İSLEMİ BASLANGIC
if($_GET['alt_kategori_sil'] == "ok")  // get kullanırken isset kullanmayız
{
	$alt_kategorisil=$db->prepare("DELETE FROM alt_kategoriler WHERE alt_kategori_id=:alt_kategori_id");
	$kontrol=$alt_kategorisil->execute([
		'alt_kategori_id' => $_GET['alt_kategori_id']
	]); 

	if($kontrol)
	{
		header("Location: ../production/alt-kategori.php?sil=ok");
	}
	else
	{
		header("Location: ../production/alt-kategori.php?sil=no");
	}
}
// ALT KATEGORİ SİLME İSLEMİ BİTİS


// KURS EKLEME ISLEMI BASLANGIC

if(isset($_POST['kurskaydet'])) 
{
		$uploads_dirimage = '../../dimg/kurs_image';
		$uploads_dirvideo = '../../dimg/kurs_video';

		$refimgyol = $_POST['eskikurs_resimyol'];
		$refvideoyol = $_POST['eskikurs_videoyol'];

		if ($_FILES['kurs_resimyol']['size'] > 0) {
			$tmp_name = $_FILES['kurs_resimyol']["tmp_name"];
			$name = $_FILES['kurs_resimyol']["name"];

			$benzersizsayi4 = rand(20000, 32000);

			$refimgyol = substr($uploads_dirimage, 6) . "/" . $benzersizsayi4 . $name;

			move_uploaded_file($tmp_name, "$uploads_dirimage/$benzersizsayi4$name");

			$resimsilunlink = $_POST['eskikurs_resimyol'];
			unlink("../../$resimsilunlink");
		}


			if ($_FILES['kurs_videoyol']['size'] > 0) {
			$tmp_name = $_FILES['kurs_videoyol']["tmp_name"];
			$name = $_FILES['kurs_videoyol']["name"];

			$benzersizsayi4 = rand(20000, 32000);

			$refvideoyol = substr($uploads_dirvideo, 6) . "/" . $benzersizsayi4 . $name;

			move_uploaded_file($tmp_name, "$uploads_dirvideo/$benzersizsayi4$name");

			// Eski resmi sil
			$videosilunlink = $_POST['eskikurs_videoyol'];
			unlink("../../$videosilunlink");
		}


	
	$kurskaydet=$db->prepare("INSERT INTO kurslar SET
		baslik=:baslik,
		kategori_id=:kategori_id,
		alt_kategori_id=:alt_kategori_id,
		kurs_seviye_id=:kurs_seviye_id,
		egitmen_id=:egitmen_id,
		aciklama=:aciklama,
		sure=:sure,
		kurs_fiyat=:kurs_fiyat,
		video_yol=:video_yol,
		resim_yol=:resim_yol,
        kurs_tur=:kurs_tur,
		kurum_onayli_sertifika_fiyat=:kurum_onayli_sertifika_fiyat,
		uni_onayli_sertifika_fiyat=:uni_onayli_sertifika_fiyat,
		sertifikalar_birlikte_fiyat=:sertifikalar_birlikte_fiyat
	");

	$kaydet=$kurskaydet->execute([

		'baslik' => $_POST['kurs_baslik'],
		'kategori_id' => $_POST['kategori_id'],
		'alt_kategori_id' => $_POST['alt_kategori_id'],
		'kurs_seviye_id' => $_POST['kurs_seviye_id'],
		'egitmen_id' => $_POST['egitmen_id'],
		'aciklama' => $_POST['kurs_aciklama'],
		'sure' => $_POST['kurs_sure'],
		'kurs_fiyat' => $_POST['kurs_fiyat'],
		'video_yol' => $refvideoyol,
		'resim_yol' => $refimgyol,
		'kurs_tur' => $_POST['kurs_tur'],
		'kurum_onayli_sertifika_fiyat' => $_POST['kurum_onayli_sertifika_fiyat'] ?: 0,
		'uni_onayli_sertifika_fiyat' => $_POST['uni_onayli_sertifika_fiyat'] ?: 0,
		'sertifikalar_birlikte_fiyat' => $_POST['sertifikalar_birlikte_fiyat'] ?: 0
	]);

	if($kaydet)
	{
		header("Location: ../production/kurs.php?sil=ok");
	}
	else
	{
		header("Location: ../production/kurs.php?sil=no");
	}
}
// KURS EKLEME ISLEMI BITIS


//KURS DUZENLEME ISLEMI YAPILACAK NOT!!

//KURS SİLME ISLEMI YAPILACAK NOT!!


// KURS VITRIN DURUM DEGİSTİRME İSLEMİ BASLANGIC
	if (isset($_POST['vitrin_durum_degistir'])) {

    $kurs_id = $_POST['kurs_id'];

    $durumSor = $db->prepare("SELECT vitrin_durum FROM kurslar WHERE kurs_id = :kurs_id");
    $durumSor->execute(['kurs_id' => $kurs_id]);
    $kurs = $durumSor->fetch(PDO::FETCH_ASSOC);

    $yeniDurum = $kurs['vitrin_durum'] == 1 ? 0 : 1;

    $guncelle = $db->prepare("UPDATE kurslar SET vitrin_durum = :yeni_durum WHERE kurs_id = :kurs_id");
    $guncelle->execute([
        'yeni_durum' => $yeniDurum,
        'kurs_id' => $kurs_id
    ]);

    if($guncelle)
    {
    	header("Location: ../production/vitrin.php?durum=ok");
    }
    else
    {
    	header("Location: ../production/vitrin.php?durum=no");
    }
}
// KURS VITRIN DURUM DEGİSTİRME İSLEMİ BİTİS


// EGITMEN EKLEME ISLEMI BASLANGIC
	if(isset($_POST['egitmenkaydet'])) 
{
		$uploads_dirimage = '../../dimg/egitmen';

		$refimgyol = $_POST['egitmen_eskiresimyol'];

		if ($_FILES['egitmen_resimyol']['size'] > 0) {
			$tmp_name = $_FILES['egitmen_resimyol']["tmp_name"];
			$name = $_FILES['egitmen_resimyol']["name"];

			$benzersizsayi4 = rand(20000, 32000);

			$refimgyol = substr($uploads_dirimage, 6) . "/" . $benzersizsayi4 . $name;

			move_uploaded_file($tmp_name, "$uploads_dirimage/$benzersizsayi4$name");

		}

	$egitmenkaydet=$db->prepare("INSERT INTO egitmen SET
		egitmen_adsoyad=:egitmen_adsoyad,
		egitmen_hakkinda=:egitmen_hakkinda,
		egitmen_rol=:egitmen_rol,
		egitmen_resimyol=:egitmen_resimyol,
		egitmen_medyabir=:egitmen_medyabir,
		egitmen_medyaiki=:egitmen_medyaiki,
		egitmen_medyauc=:egitmen_medyauc,
		egitmen_medyadort=:egitmen_medyadort");

	$kaydet=$egitmenkaydet->execute([

		'egitmen_adsoyad' => $_POST['egitmen_adsoyad'],
		'egitmen_hakkinda' => $_POST['egitmen_hakkinda'],
		'egitmen_rol' => $_POST['egitmen_rol'],
		'egitmen_resimyol' => $refimgyol,
		'egitmen_medyabir' => $_POST['egitmen_medyabir'],
		'egitmen_medyaiki' => $_POST['egitmen_medyaiki'],
		'egitmen_medyauc' => $_POST['egitmen_medyauc'],
		'egitmen_medyadort' => $egitmen_medyadort
	]);

	if($kaydet)
	{
		header("Location: ../production/egitmen.php?durum=ok");
	}
	else
	{
		header("Location: ../production/egitmen.php?durum=no");
	}
}
// EGITMEN EKLEME ISLEMI BITIS


// EGITMEN DUZENLEME ISLEMI YAPILACAK NOT!!

// EGITMEN SILME ISLEMI YAPILACAK NOT!!

// ANA HAKKIMIZDA KAYDETME ISLEMI BASLANGIC
	if(isset($_POST['anahakkimizdakaydet']))
	{
		$anahakkimizda_id=$_POST['anahakkimizda_id'];
		$uploads_dirimage = '../../dimg/anahakkimizda';
		$refimgyol = $_POST['anahakkimizda_eskiresimyol'];

		if ($_FILES['anahakkimizda_resimyol']['size'] > 0) {
			$tmp_name = $_FILES['anahakkimizda_resimyol']["tmp_name"];
			$name = $_FILES['anahakkimizda_resimyol']["name"];

			$benzersizsayi4 = rand(20000, 32000);

			$refimgyol = substr($uploads_dirimage, 6) . "/" . $benzersizsayi4 . $name;

			move_uploaded_file($tmp_name, "$uploads_dirimage/$benzersizsayi4$name");

			$resimsilunlink = $_POST['anahakkimizda_eskiresimyol'];
			unlink("../../$resimsilunlink");
		}

		$anahakkimizdaduzenle=$db->prepare("INSERT INTO anahakkimizda SET
			anahakkimizda_title=:anahakkimizda_title,
			anahakkimizda_header=:anahakkimizda_header,
			anahakkimizda_color=:anahakkimizda_color,
			anahakkimizda_resimyol=:anahakkimizda_resimyol,
			anahakkimizda_text=:anahakkimizda_text");		

		$guncelle=$anahakkimizdaduzenle->execute([
			'anahakkimizda_title' => $_POST['anahakkimizda_title'],
			'anahakkimizda_header' =>$_POST['anahakkimizda_header'],
			'anahakkimizda_color' => $_POST['anahakkimizda_color'],
			'anahakkimizda_resimyol' => $refimgyol,
			'anahakkimizda_text' => $_POST['anahakkimizda_text']
		]);

		if($guncelle)
		{
			header("Location: ../production/anahakkimizda.php?durum=ok");
		}
		else
		{
			header("Location: ../production/anahakkimizda.php?durum=no");
		}
	}
// ANA HAKKIMIZDA KAYDETME ISLEMI BITIS


// ANA HAKKIMIZDA DUZENLEME ISLEMI BASLANGIC
	if(isset($_POST['anahakkimizdaguncelle']))
	{
		$anahakkimizda_id=$_POST['anahakkimizda_id'];
		$uploads_dirimage = '../../dimg/anahakkimizda';
		$refimgyol = $_POST['anahakkimizda_eskiresimyol'];

		if ($_FILES['anahakkimizda_resimyol']['size'] > 0) {
			$tmp_name = $_FILES['anahakkimizda_resimyol']["tmp_name"];
			$name = $_FILES['anahakkimizda_resimyol']["name"];

			$benzersizsayi4 = rand(20000, 32000);

			$refimgyol = substr($uploads_dirimage, 6) . "/" . $benzersizsayi4 . $name;

			move_uploaded_file($tmp_name, "$uploads_dirimage/$benzersizsayi4$name");

			$resimsilunlink = $_POST['anahakkimizda_eskiresimyol'];
			unlink("../../$resimsilunlink");
		}

		$anahakkimizdaduzenle=$db->prepare("UPDATE anahakkimizda SET
			anahakkimizda_title=:anahakkimizda_title,
			anahakkimizda_header=:anahakkimizda_header,
			anahakkimizda_color=:anahakkimizda_color,
			anahakkimizda_resimyol=:anahakkimizda_resimyol,
			anahakkimizda_text=:anahakkimizda_text
			WHERE anahakkimizda_id = {$_POST['anahakkimizda_id']}");		

		$guncelle=$anahakkimizdaduzenle->execute([
			'anahakkimizda_title' => $_POST['anahakkimizda_title'],
			'anahakkimizda_header' => $_POST['anahakkimizda_header'],
			'anahakkimizda_color' => $_POST['anahakkimizda_color'],
			'anahakkimizda_resimyol' => $refimgyol,
			'anahakkimizda_text' => $_POST['anahakkimizda_text']
		]);

		if($guncelle)
		{
			header("Location: ../production/anahakkimizda-duzenle.php?anahakkimizda_id=$anahakkimizda_id&durum=ok");
		}
		else
		{
			header("Location: ../production/anahakkimizda-duzenle.php?anahakkimizda_id=$anahakkimizda_id&durum=no");
		}
	}
// ANA HAKKIMIZDA DUZENLEME ISLEMİ BITIS


// ANA HAKKIMIZDA SİLME İSLEMLERİ BASLANGIC
if($_GET['anahakkimizdasil'] == "ok")  // get kullanırken isset kullanmayız
{
	$anahakkimizdasil=$db->prepare("DELETE FROM anahakkimizda WHERE anahakkimizda_id=:anahakkimizda_id");
	$kontrol=$anahakkimizdasil->execute([
		'anahakkimizda_id' => $_GET['anahakkimizda_id']
	]); 

	if($kontrol)
	{
		header("Location: ../production/anahakkimizda.php?sil=ok");
	}
	else
	{
		header("Location: ../production/anahakkimizda.php?sil=no");
	}
}
// ANA HAKKIMIZDA SİLME İSLEMLERİ BİTİS


// MARKA KAYDETME İSLEMİ BASLANGIC
	if(isset($_POST['markakaydet']))
	{
		$uploads_dir = '../../dimg/markalogo';

		$tmp_name = $_FILES['marka_logo']["tmp_name"];
		$name = $_FILES['marka_logo']["name"];

		$benzersizsayi1=rand(20000,32000);
		$benzersizsayi2=rand(20000,32000);
		$benzersizsayi3=rand(20000,32000);
		$benzersizsayi4=rand(20000,32000);

		$benzersizad=$benzersizsayi1.$benzersizsayi2.$benzersizsayi3.$benzersizsayi4;
		$refimgyol=substr($uploads_dir, 6)."/".$benzersizad.$name;

		move_uploaded_file($tmp_name, "$uploads_dir/$benzersizad$name");


		$markakaydet=$db->prepare("INSERT INTO markalar SET
			marka_ad=:marka_ad,
			marka_resimyol=:marka_resimyol
		");

		$update=$markakaydet->execute([
			'marka_ad' => $_POST['marka_ad'],
			'marka_resimyol' => $refimgyol,
		]);

		if($update)
		{
			$resimsilunlink= $_POST['marka_eskiresimyol'];
			unlink("../../$resimsilunlink");
			header("Location: ../production/marka.php?durum=ok");
		}
		else
		{
			header("Location: ../production/marka.php?durum=no");
		}
	}
// MARKA KAYDETME İSLEMİ BITIS


// MARKA SİLME İSLEMLERİ BASLANGIC
if($_GET['markasil'] == "ok")  // get kullanırken isset kullanmayız
{
	$kullanicisil=$db->prepare("DELETE FROM markalar WHERE marka_id=:marka_id");
	$kontrol=$kullanicisil->execute([
		'marka_id' => $_GET['marka_id']
	]); 

	if($kontrol)
	{
		header("Location: ../production/marka.php?sil=ok");
	}
	else
	{
		header("Location: ../production/marka.php?sil=no");
	}
}
// MARKA SİLME İSLEMLERİ BİTİS


// MARKA VITRIN DURUM DEGİSTİRME İSLEMİ BASLANGIC
	if (isset($_POST['marka_vitrin_durum_degistir'])) {

    $marka_id = $_POST['marka_id'];

    $durumSor = $db->prepare("SELECT markavitrin_durum FROM markalar WHERE marka_id = :marka_id");
    $durumSor->execute(['marka_id' => $marka_id]);
    $marka = $durumSor->fetch(PDO::FETCH_ASSOC);

    $yeniDurum = $marka['markavitrin_durum'] == 1 ? 0 : 1;

    $guncelle = $db->prepare("UPDATE markalar SET markavitrin_durum = :yeni_durum WHERE marka_id = :marka_id");
    $guncelle->execute([
        'yeni_durum' => $yeniDurum,
        'marka_id' => $marka_id
    ]);

    if($guncelle)
    {
    	header("Location: ../production/marka_vitrin.php?durum=ok");
    }
    else
    {
    	header("Location: ../production/marka_vitrin.php?durum=no");
    }
}
// MARKA VITRIN DURUM DEGİSTİRME İSLEMİ BİTİS


// EGITMEN VITRIN DURUM DEGİSTİRME İSLEMİ BASLANGIC
	if (isset($_POST['egitmen_vitrin_durum_degistir'])) {

    $egitmen_id = $_POST['egitmen_id'];

    $egitmenSor = $db->prepare("SELECT egitmen_vitrin FROM egitmen WHERE egitmen_id = :egitmen_id");
    $egitmenSor->execute(['egitmen_id' => $egitmen_id]);
    $egitmen = $egitmenSor->fetch(PDO::FETCH_ASSOC);

    $yeniDurum = $egitmen['egitmen_vitrin'] == 1 ? 0 : 1;

    $guncelle = $db->prepare("UPDATE egitmen SET egitmen_vitrin = :yeni_durum WHERE egitmen_id = :egitmen_id");
    $guncelle->execute([
        'yeni_durum' => $yeniDurum,
        'egitmen_id' => $egitmen_id
    ]);

    if($guncelle)
    {
    	header("Location: ../production/egitmen_vitrin.php?durum=ok");
    }
    else
    {
    	header("Location: ../production/egitmen_vitrin.php?durum=no");
    }
}
// EGITMEN VITRIN DURUM DEGİSTİRME İSLEMİ BİTİS

// AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA

// BLOG KAYDETME İSLEMİ BASLANGIC
	if(isset($_POST['blogkaydet']))
	{
		$uploads_dirimage = '../../dimg/blog';

		if ($_FILES['blog_resimyol']['size'] > 0) {
			$tmp_name = $_FILES['blog_resimyol']["tmp_name"];
			$name = $_FILES['blog_resimyol']["name"];

			$benzersizsayi4 = rand(20000, 32000);

			$refimgyol = substr($uploads_dirimage, 6) . "/" . $benzersizsayi4 . $name;

			move_uploaded_file($tmp_name, "$uploads_dirimage/$benzersizsayi4$name");
		}


		$sliderkaydet=$db->prepare("INSERT INTO blog SET
			blog_ad=:blog_ad,
			blog_kategori_id=:blog_kategori_id,
			yazar_ad=:yazar_ad,
			blog_aciklama=:blog_aciklama,
			blog_tarih=:blog_tarih,
			blog_resimyol=:blog_resimyol
		");

		$update=$sliderkaydet->execute([
			'blog_ad' => $_POST['blog_ad'],
			'blog_kategori_id' => $_POST['blog_kategori_id'],
			'yazar_ad' => $_POST['yazar_ad'],
			'blog_aciklama' => $_POST['blog_aciklama'],
			'blog_tarih' => $_POST['blog_tarih'],
			'blog_resimyol' => $refimgyol
		]);

		if($update)
		{
			header("Location: ../production/blog.php?durum=ok");
		}
		else
		{
			header("Location: ../production/blog.php?durum=no");
		}
	}
// BLOG KAYDETME İSLEMİ BITIS


// BLOG SİLME İSLEMİ BASLANGIC
if($_GET['blogsil'] == "ok") 
{
	$blogsil=$db->prepare("DELETE FROM blog WHERE blog_id=:blog_id");
	$kontrol=$blogsil->execute([
		'blog_id' => $_GET['blog_id']
	]); 

	if($kontrol)
	{
		header("Location: ../production/blog.php?sil=ok");
	}
	else
	{
		header("Location: ../production/blog.php?sil=no");
	}
}
// BLOG SILME ISLEMI BİTİS


// BLOG VITRIN DURUM DEGİSTİRME İSLEMİ BASLANGIC
	if (isset($_POST['blog_vitrin_durum_degistir'])) {

    $blog_id = $_POST['blog_id'];

    $blogsor = $db->prepare("SELECT blog_vitrin FROM blog WHERE blog_id = :blog_id");
    $blogsor->execute(['blog_id' => $blog_id]);
    $blog = $blogsor->fetch(PDO::FETCH_ASSOC);

    $yeniDurum = $blog['blog_vitrin'] == 1 ? 0 : 1;

    $guncelle = $db->prepare("UPDATE blog SET blog_vitrin = :yeni_durum WHERE blog_id = :blog_id");
    $guncelle->execute([
        'yeni_durum' => $yeniDurum,
        'blog_id' => $blog_id
    ]);

    if($guncelle)
    {
    	header("Location: ../production/blog_vitrin.php?durum=ok");
    }
    else
    {
    	header("Location: ../production/blog_vitrin.php?durum=no");
    }
}
// BLOG VITRIN DURUM DEGİSTİRME İSLEMİ BİTİS

// BLOG KATEGORİ KAYDETME İSLEMİ BASLANGIC
	if(isset($_POST['blogkategorikaydet']))
	{
		$bkategori=$db->prepare("INSERT INTO blog_kategori SET
			kategori_ad=:kategori_ad
		");
		$kaydet=$bkategori->execute([
			'kategori_ad' => $_POST['blog_kategori_ad']
		]);

		if($kaydet)
    {
    	header("Location: ../production/blog_kategori.php?durum=ok");
    }
    else
    {
    	header("Location: ../production/blog_kategori.php?durum=no");
    }
	}

// BLOG KATEGORİ KAYDETME İSLEMİ BITIS


// SSS EKLEME ISLEMI BASLANGIC
	if(isset($_POST['ssskaydet']))
	{
		$sss_kaydet=$db->prepare("INSERT INTO sss SET
			sss_soru=:sss_soru,
			sss_aciklama=:sss_aciklama,
			sss_sira=:sss_sira
		");
		$insert=$sss_kaydet->execute([
			'sss_soru' => $_POST['sss_soru'],
			'sss_aciklama' => $_POST['sss_aciklama'],
			'sss_sira' => $_POST['sss_aciklama']
		]);

		if($insert)
		{
			header("Location: ../production/sss.php?durum=ok");
		}
		else
		{
			header("Location: ../production/sss.php?durum=no");
		}
	}
// SSS EKLEME ISLEMI BITIS


//ILETISIM FORM GONDERME ISLEMI BASLANGIC
	if(isset($_POST['iletisim_form_gonder']))
	{
		$iletisimformgonder=$db->prepare("INSERT INTO iletisimform SET
			iletisim_adsoyad=:iletisim_adsoyad,
			iletisim_tel=:iletisim_tel,
			iletisim_konu=:iletisim_konu,
			iletisim_mail=:iletisim_mail,
			iletisim_mesaj=:iletisim_mesaj
		");

		$kaydet=$iletisimformgonder->execute([
			'iletisim_adsoyad' => $_POST['iletisim_adsoyad'],
			'iletisim_tel' => $_POST['iletisim_tel'],
			'iletisim_konu' => $_POST['iletisim_konu'],
			'iletisim_mail' => $_POST['iletisim_mail'],
			'iletisim_mesaj' => $_POST['iletisim_mesaj']
		]);

		if($kaydet)
		{
			header("Location: ../../contact.php?durum=ok");
		}
		else
		{
			header("Location: ../../contact.php?durum=no");
		}
	}
//ILETISIM FORM GONDERME ISLEMI BITIS


//BULTEN GONDERME ISLEMI BASLANGIC
	if(isset($_POST['bulten_kaydet']))
	{
		$bultengonder=$db->prepare("INSERT INTO bulten SET
			bulten_mail=:bulten_mail,
			bulten_durum=:bulten_durum
		");

		$kaydet=$bultengonder->execute([
			'bulten_mail' => $_POST['bulten_mail'],
			'bulten_durum' => 0
		]);

		if($kaydet)
		{
			header("Location: ../../index.php?durum=ok");
		}
		else
		{
			header("Location: ../../index.php?durum=no");
		}
	}
//BULTEN GONDERME ISLEMI BITIS


//KURS ICERIK EKLEME ISLEMI BASLANGIC

if (isset($_POST['kursicerikkaydet'])) {
    // Kurs ID'yi al
    $kurs_id = $_POST['kurs_id'];

    // İçerikler dizisini al
    $icerikler = $_POST['icerikler'];

    // İçerikleri döngüyle işleyip veritabanına ekle
    foreach ($icerikler as $icerik) {
        // Gerekli alanları kontrol et
        if (!empty($icerik['icerik_ad']) && !empty($icerik['icerik_aciklama']) && !empty($icerik['icerik_ders_sayi'])) {
            $query = $db->prepare("INSERT INTO kurs_icerik (kurs_id, icerik_ad, icerik_aciklama, icerik_ders_sayi) 
                                   VALUES (:kurs_id, :icerik_ad, :icerik_aciklama, :icerik_ders_sayi)");
            $query->execute([
                ':kurs_id' => $kurs_id,
                ':icerik_ad' => $icerik['icerik_ad'],
                ':icerik_aciklama' => $icerik['icerik_aciklama'],
                ':icerik_ders_sayi' => $icerik['icerik_ders_sayi'],
            ]);
        }
    }

    // Kurslar tablosunda ilgili kursun icerik_durum sütununu güncelle
    $query2 = $db->prepare("UPDATE kurslar SET icerik_durum = :icerik_durum WHERE kurs_id = :kurs_id");
    $query2->execute([
        ':icerik_durum' => 1,
        ':kurs_id' => $kurs_id,
    ]);

    // Yönlendirme işlemi
    if ($query2) {
        header("Location: ../production/kurs-icerik.php?durum=ok");
    } else {
        header("Location: ../production/kurs-icerik.php?durum=no");
    }
    exit; // Güvenlik için çıkış
}
//KURS ICERIK EKLEME ISLEMI BITIS


//PANEL BULTEN GONDERME ISLEMI BASLANGIC

if (isset($_POST['send_bulletin'])) {

    $bultensor = $db->prepare("SELECT bulten_mail FROM bulten WHERE bulten_durum = 1");
    $bultensor->execute();

    $sendgrid_api_key = $_ENV['SENDGRID_API_KEY'];
    $sendgrid = new \SendGrid($sendgrid_api_key);

    $errors = [];
    $success_count = 0;

    while ($bulten_row = $bultensor->fetch(PDO::FETCH_ASSOC)) {
        $email = new Mail();
        $email->setFrom("info@badiakademi.com", "Badi Akademi");
        $email->setSubject("Haftalık Bülten");
        $email->addTo($bulten_row['bulten_mail'], "Bülten Alıcısı");
        $email->addContent("text/plain", "Merhaba, bu haftanın haber bültenini size iletiyoruz.");
        $email->addContent("text/html", "<strong>Merhaba, bu haftanın haber bültenini size iletiyoruz.</strong>");

        try {
            $response = $sendgrid->send($email);
            if ($response->statusCode() == 202) {
                $success_count++;
            } else {
                $errors[] = $bulten_row['bulten_mail'];
            }
        } catch (Exception $e) {
            $errors[] = $bulten_row['bulten_mail'] . ' - ' . $e->getMessage();
        }
    }

    if (count($errors) == 0) {
        header("Location:../bulten.php?durum=ok&sent=$success_count");
    } else {
        header("Location:../bulten.php?durum=no&errors=" . implode(",", $errors));
    }
}
//PANEL BULTEN GONDERME ISLEMI BITIS

// BAŞVURU DETAY GETİRME İŞLEMİ
if(isset($_POST['basvuru_detay']) && $_POST['basvuru_detay'] == 'ok') {
    ob_clean();
    header('Content-Type: application/json; charset=utf-8');
    
    try {
        $basvuru_id = intval($_POST['basvuru_id']);
        
        $basvurusor = $db->prepare("SELECT eb.*, k.kullanici_ad, k.kullanici_soyad 
                                   FROM egitmen_basvuru eb
                                   INNER JOIN kullanici k ON eb.kullanici_id = k.kullanici_id 
                                   WHERE eb.basvuru_id = :id");
        
        $basvurusor->execute(['id' => $basvuru_id]);
        
        if($basvurusor->rowCount() > 0) {
            $basvuru = $basvurusor->fetch(PDO::FETCH_ASSOC);
            
            $response = [
                'status' => 'success',
                'data' => [
                    'ad_soyad' => $basvuru['kullanici_ad'] . ' ' . $basvuru['kullanici_soyad'],
                    'uzmanlik_alani' => $basvuru['uzmanlik_alani'],
                    'hakkinda' => $basvuru['hakkinda'],
                    'basvuru_zaman' => date('d.m.Y H:i', strtotime($basvuru['basvuru_zaman']))
                ]
            ];
        } else {
            $response = [
                'status' => 'error',
                'message' => 'Başvuru bulunamadı'
            ];
        }
        
        die(json_encode($response));
        
    } catch(Exception $e) {
        die(json_encode([
            'status' => 'error',
            'message' => 'Bir hata oluştu'
        ]));
    }
}

// BAŞVURU ONAYLAMA İŞLEMİ
if(isset($_POST['basvuru_onay']) && $_POST['basvuru_onay'] == 'ok') {
    try {
        $basvuru_id = intval($_POST['basvuru_id']);
        
        // Başvuru bilgilerini al
        $basvurusor = $db->prepare("SELECT eb.*, k.kullanici_ad, k.kullanici_soyad 
                                   FROM egitmen_basvuru eb
                                   INNER JOIN kullanici k ON eb.kullanici_id = k.kullanici_id 
                                   WHERE eb.basvuru_id = :id");
        $basvurusor->execute(['id' => $basvuru_id]);
        $basvuru = $basvurusor->fetch(PDO::FETCH_ASSOC);
        
        // Kullanıcı durumunu active yap
        $kullaniciGuncelle = $db->prepare("UPDATE kullanici SET 
            kullanici_durum = :durum,
            kullanici_yetki = :yetki 
            WHERE kullanici_id = :id");
            
        $kullaniciGuncelle->execute([
            'durum' => 'active',
            'yetki' => 'egitmen',
            'id' => $basvuru['kullanici_id']
        ]);
        
        // Eğitmen tablosuna ekle
        $egitmenEkle = $db->prepare("INSERT INTO egitmen SET 
            egitmen_adsoyad = :adsoyad,
            egitmen_hakkinda = :hakkinda,
            egitmen_rol = :rol,
            kullanici_id = :kullanici_id");
            
        $egitmenEkle->execute([
            'adsoyad' => $basvuru['kullanici_ad'] . ' ' . $basvuru['kullanici_soyad'],
            'hakkinda' => $basvuru['hakkinda'],
            'rol' => $basvuru['uzmanlik_alani'],
            'kullanici_id' => $basvuru['kullanici_id']
        ]);
        
        // Başvuru durumunu güncelle
        $basvuruGuncelle = $db->prepare("UPDATE egitmen_basvuru SET 
            basvuru_durum = :durum 
            WHERE basvuru_id = :id");
            
        $basvuruGuncelle->execute([
            'durum' => 'accept',
            'id' => $basvuru_id
        ]);
        
        echo "ok";
        
    } catch(Exception $e) {
        error_log("Eğitmen onaylama hatası: " . $e->getMessage());
        echo "no";
    }
}

// BAŞVURU REDDETME İŞLEMİ
if(isset($_POST['basvuru_red']) && $_POST['basvuru_red'] == 'ok') {
    try {
        $basvuru_id = intval($_POST['basvuru_id']);
        $red_nedeni = $_POST['red_nedeni'];
        
        // Başvuru durumunu güncelle
        $basvuruGuncelle = $db->prepare("UPDATE egitmen_basvuru SET 
            basvuru_durum = :durum,
            red_nedeni = :red_nedeni 
            WHERE basvuru_id = :id");
            
        $basvuruGuncelle->execute([
            'durum' => 'refuse',
            'red_nedeni' => $red_nedeni,
            'id' => $basvuru_id
        ]);
        
        echo "ok";
        
    } catch(Exception $e) {
        echo "no";
    }
}

if(isset($_POST["addToCart"])) {
    // Server-side security check is still needed
    if(!isset($_SESSION['userkullanici_id'])){
        // Instead of redirecting to login, return JSON response
        // This is a fallback in case JavaScript validation is bypassed
        echo json_encode(['error' => 'login_required']);
        exit;
    }
    
    $kurs_id = $_POST['course_id'];
    $selected_cert = isset($_POST['selected_cert']) ? $_POST['selected_cert'] : '';
    $cert_price = isset($_POST['cert_price']) ? floatval($_POST['cert_price']) : 0;
    $total_price = isset($_POST['total_price']) ? floatval($_POST['total_price']) : 0;
    
    // Validate that a certificate option is selected
    if (empty($selected_cert)) {
        Header("Location:../../kurs-detay.php?kurs_id=$kurs_id&durum=sertifikasec");
        exit;
    }

    // Convert single certificate selection to the format expected by the database
    $selected_certs = '';
    if ($selected_cert == 'kurum_cert') {
        $selected_certs = 'kurum_cert';
    } else if ($selected_cert == 'uni_cert') {
        $selected_certs = 'uni_cert';
    } else if ($selected_cert == 'both_cert') {
        $selected_certs = 'both_cert';
    }
    
    // Check if course already in cart
    $cart_check = $db->prepare("SELECT * FROM sepet WHERE user_id = :user_id AND course_id = :course_id");
    $cart_check->execute([
        'user_id' => $_SESSION["userkullanici_id"],
        'course_id' => $kurs_id
    ]);
    
    if($cart_check->rowCount() == 0) {
        // Insert new cart item
        $cart_add = $db->prepare("INSERT INTO sepet 
            (user_id, course_id, selected_certs, cert_total_price) 
            VALUES (:user_id, :course_id, :selected_certs, :cert_total_price)");
            
        $insert = $cart_add->execute([
            'user_id' => $_SESSION["userkullanici_id"],
            'course_id' => $kurs_id,
            'selected_certs' => $selected_certs,
            'cert_total_price' => $total_price
        ]);
        if($insert) {
            Header("Location:../../kurs-detay.php?kurs_id=".$kurs_id."&islem=eklendi");
            exit;
        }
    } else {
        // Update existing cart item
        $cart_update = $db->prepare("UPDATE sepet SET 
            selected_certs = :selected_certs,
            cert_total_price = :cert_total_price
            WHERE user_id = :user_id AND course_id = :course_id");
            
        $update = $cart_update->execute([
            'selected_certs' => $selected_certs,
            'cert_total_price' => $total_price,
            'user_id' => $_SESSION["userkullanici_id"],
            'course_id' => $kurs_id
        ]);
        
        if($update) {
            Header("Location:../../kurs-detay.php?kurs_id=".$kurs_id."&islem=eklendi");
            exit;
        }
    }
}

if(isset($_POST["removeFromCart"])){
	$kurs_id = $_POST["kurs_id"];
	
	$sepet_stmt = $db->prepare("DELETE FROM sepet WHERE user_id = :user_id AND course_id = :course_id");
	$sepet_stmt->execute([
		"user_id" => $_SESSION["userkullanici_id"],
		"course_id" => $kurs_id
	]);
	
	header("Location: ../../kurs-detay.php?kurs_id=".$kurs_id."&islem=silindi");
	exit();	
}

if(isset($_GET["action"]) && $_GET["action"] == "removeCourseFromCart"){
	$kurs_id = $_GET["kurs_id"];
	$sepet_stmt = $db->prepare("DELETE FROM sepet WHERE user_id = :user_id AND course_id = :course_id");
	$sepet_stmt->execute([
		"user_id" => $_SESSION["userkullanici_id"],
		"course_id" => $kurs_id
	]);
	
	header("Location: ../../cart.php");
	exit();	
}

if(isset($_POST['yorum_ekle'])) {
    $kaydet = $db->prepare("INSERT INTO kurs_yorumlar SET
        kurs_id = :kurs_id,
        user_id = :user_id,
        yorum_metni = :yorum_metni,
        puan = :puan
    ");

    $insert = $kaydet->execute([
        'kurs_id' => $_POST['kurs_id'],
        'user_id' => $_POST['user_id'],
        'yorum_metni' => $_POST['yorum_metni'],
        'puan' => $_POST['puan']
    ]);

    if($insert) {
        Header("Location: ../../kurs-detay.php?kurs_id=".$_POST['kurs_id']."&durum=ok");
    } else {
        Header("Location: ../../kurs-detay.php?kurs_id=".$_POST['kurs_id']."&durum=no");
    }
}

if(isset($_POST['kurs_duzenle'])) {
    $kurs_id = $_POST['kurs_id'];

    // Image handling
    if($_FILES['resim_yol']['size'] > 0) {
        $uploads_dir = '../../dimg/kurs_image';
        
        @$tmp_name = $_FILES['resim_yol']["tmp_name"];
        @$name = $_FILES['resim_yol']["name"];
        
        // Get the file extension
        $ext = strtolower(substr($name, strripos($name, '.') + 1));
        
        // Check if it's a valid image file
        $allowed_extensions = array('jpg', 'jpeg', 'png', 'gif');
        if(in_array($ext, $allowed_extensions)) {
            // Generate unique name
            $benzersizsayi1 = rand(20000, 32000);
            $benzersizsayi2 = rand(20000, 32000);
            $benzersizad = $benzersizsayi1 . $benzersizsayi2;
            $resim_yol = substr($uploads_dir, 6) . "/" . $benzersizad . $name;
            
            // Move the uploaded file
            @move_uploaded_file($tmp_name, "$uploads_dir/$benzersizad$name");
            
            // Delete old image if exists
            if(!empty($_POST['eski_resim']) && file_exists("../../" . $_POST['eski_resim'])) {
                unlink("../../" . $_POST['eski_resim']);
            }
        } else {
            // Invalid file type
            header("Location:../production/kurs-duzenle.php?kurs_id=$kurs_id&durum=imgformat");
            exit;
        }
    } else {
        $resim_yol = $_POST['eski_resim'];
    }

    // Video handling
    if($_FILES['onizleme_video']['size'] > 0) {
        $uploads_dir = '../../dimg/kurs_video';
        
        @$tmp_name = $_FILES['onizleme_video']["tmp_name"];
        @$name = $_FILES['onizleme_video']["name"];

        $benzersizsayi1 = rand(20000, 32000);
        $benzersizsayi2 = rand(20000, 32000);
        $benzersizad = $benzersizsayi1 . $benzersizsayi2;
        $video_yol = substr($uploads_dir, 6) . "/" . $benzersizad . $name;
        
        @move_uploaded_file($tmp_name, "$uploads_dir/$benzersizad$name");

        // Delete old video if exists
        if(file_exists("../../" . $_POST['eski_video'])) {
            unlink("../../" . $_POST['eski_video']);
        }
    } else {
        $video_yol = $_POST['eski_video'];
    }

    // Calculate minimum price from certificate prices only (excluding transcripts)
    
    $duzenle=$db->prepare("UPDATE kurslar SET
        baslik=:baslik,
        aciklama=:aciklama,
        kurs_fiyat=:kurs_fiyat,
        vitrin_durum=:vitrin_durum,
        resim_yol=:resim_yol,
        video_yol=:onizleme_video,
        kurs_seviye_id=:kurs_seviye_id,
        kurs_tur=:kurs_tur,
        kurum_onayli_sertifika_fiyat=:kurum_onayli_sertifika_fiyat,
        uni_onayli_sertifika_fiyat=:uni_onayli_sertifika_fiyat,
        sertifikalar_birlikte_fiyat=:sertifikalar_birlikte_fiyat
        WHERE kurs_id=:kurs_id
    ");

    $update=$duzenle->execute([
        'baslik' => $_POST['baslik'],
        'aciklama' => $_POST['aciklama'],
        'kurs_fiyat' => $_POST['kurs_fiyat'],
        'vitrin_durum' => $_POST['vitrin_durum'],
        'resim_yol' => $resim_yol,
        'onizleme_video' => $video_yol,
        'kurs_seviye_id' => $_POST['kurs_seviye_id'],
        'kurs_tur' => $_POST['kurs_tur'],
        'kurs_id' => $_POST['kurs_id'],
        'kurum_onayli_sertifika_fiyat' => $_POST['kurum_onayli_sertifika_fiyat'] ?: 0,
        'uni_onayli_sertifika_fiyat' => $_POST['uni_onayli_sertifika_fiyat'] ?: 0,
        'sertifikalar_birlikte_fiyat' => $_POST['sertifikalar_birlikte_fiyat'] ?: 0
    ]);

    if($update) {
        Header("Location:../production/kurs-duzenle.php?kurs_id=$kurs_id&durum=ok");
    } else {
        Header("Location:../production/kurs-duzenle.php?kurs_id=$kurs_id&durum=no");
    }
}

// Get module data for editing
if(isset($_GET['get_modul'])) {
    $modul_id = $_GET['get_modul'];
    $modulsor = $db->prepare("SELECT * FROM kurs_modulleri WHERE modul_id = ?");
    $modulsor->execute([$modul_id]);
    $modul = $modulsor->fetch(PDO::FETCH_ASSOC);
    echo json_encode($modul);
    exit;
}


// Process module edit
if(isset($_POST['modul_duzenle'])) {
    $modul_id = $_POST['modul_id'];
    $modul_ad = $_POST['modul_ad'];
    $modul_sira = $_POST['modul_sira'];
    
    $update = $db->prepare("UPDATE kurs_modulleri SET 
        modul_ad = ?,
        modul_sira = ?
        WHERE modul_id = ?");
    
    $result = $update->execute([$modul_ad, $modul_sira, $modul_id]);
    
    if($result) {
        Header("Location:../production/kurs-icerik-duzenle.php?kurs_id=".$_POST['kurs_id']."&durum=ok");
    } else {
        Header("Location:../production/kurs-icerik-duzenle.php?kurs_id=".$_POST['kurs_id']."&durum=no");
    }
}

// Add similar handlers for bolum_ekle and bolum_duzenle

// MODÜL EKLEME İŞLEMİ
if(isset($_POST['modul_ekle'])) {
    $kaydet = $db->prepare("INSERT INTO kurs_modulleri SET
        kurs_id = :kurs_id,
        modul_ad = :modul_ad,
        modul_sira = :modul_sira
    ");

    $insert = $kaydet->execute([
        'kurs_id' => $_POST['kurs_id'],
        'modul_ad' => $_POST['modul_ad'],
        'modul_sira' => $_POST['modul_sira']
    ]);

    if($insert) {
        Header("Location:../production/kurs-icerik-duzenle.php?kurs_id=".$_POST['kurs_id']."&durum=ok");
    } else {
        Header("Location:../production/kurs-icerik-duzenle.php?kurs_id=".$_POST['kurs_id']."&durum=no");
    }
}

// MODÜL SİLME İŞLEMİ
if(isset($_GET['modul_sil']) && $_GET['modul_sil'] == 'ok') {
    // Get kurs_id before deleting for redirect
    $modulsor = $db->prepare("SELECT kurs_id FROM kurs_modulleri WHERE modul_id = :id");
    $modulsor->execute(['id' => $_GET['modul_id']]);
    $modul = $modulsor->fetch(PDO::FETCH_ASSOC);
    $kurs_id = $modul['kurs_id'];

    // Delete module (cascade will handle sections)
    $sil = $db->prepare("DELETE FROM kurs_modulleri WHERE modul_id = :id");
    $kontrol = $sil->execute(['id' => $_GET['modul_id']]);

    if($kontrol) {
        Header("Location:../production/kurs-icerik-duzenle.php?kurs_id=".$kurs_id."&durum=ok");
    } else {
        Header("Location:../production/kurs-icerik-duzenle.php?kurs_id=".$kurs_id."&durum=no");
    }
}

// BÖLÜM EKLEME İŞLEMİ
if(isset($_POST['bolum_ekle'])) {
    // Validate time inputs
    $saat = max(0, min(99, intval($_POST['bolum_sure_saat'])));
    $dakika = max(0, min(59, intval($_POST['bolum_sure_dakika'])));
    
    $kaydet = $db->prepare("INSERT INTO kurs_bolumleri SET
        modul_id = :modul_id,
        bolum_ad = :bolum_ad,
        bolum_sure_saat = :bolum_sure_saat,
        bolum_sure_dakika = :bolum_sure_dakika,
        bolum_sira = :bolum_sira
    ");

    $insert = $kaydet->execute([
        'modul_id' => $_POST['modul_id'],
        'bolum_ad' => $_POST['bolum_ad'],
        'bolum_sure_saat' => $saat,
        'bolum_sure_dakika' => $dakika,
        'bolum_sira' => $_POST['bolum_sira']
    ]);

    if($insert) {
        Header("Location:../production/kurs-icerik-duzenle.php?kurs_id=".$_POST['kurs_id']."&durum=ok");
    } else {
        Header("Location:../production/kurs-icerik-duzenle.php?kurs_id=".$_POST['kurs_id']."&durum=no");
    }
}

// BÖLÜM DÜZENLEME İŞLEMİ
if(isset($_POST['bolum_duzenle'])) {
    // Validate time inputs
    $saat = max(0, min(99, intval($_POST['bolum_sure_saat'])));
    $dakika = max(0, min(59, intval($_POST['bolum_sure_dakika'])));
    
    $guncelle = $db->prepare("UPDATE kurs_bolumleri SET
        bolum_ad = :bolum_ad,
        bolum_sure_saat = :bolum_sure_saat,
        bolum_sure_dakika = :bolum_sure_dakika,
        bolum_sira = :bolum_sira
        WHERE bolum_id = :bolum_id
    ");

    $update = $guncelle->execute([
        'bolum_ad' => $_POST['bolum_ad'],
        'bolum_sure_saat' => $saat,
        'bolum_sure_dakika' => $dakika,
        'bolum_sira' => $_POST['bolum_sira'],
        'bolum_id' => $_POST['bolum_id']
    ]);

    if($update) {
        Header("Location:../production/kurs-icerik-duzenle.php?kurs_id=".$_POST['kurs_id']."&durum=ok");
    } else {
        Header("Location:../production/kurs-icerik-duzenle.php?kurs_id=".$_POST['kurs_id']."&durum=no");
    }
}


// Update the get_bolum handler to return the new time format
if(isset($_GET['get_bolum'])) {
    $bolum_id = $_GET['get_bolum'];
    $bolumsor = $db->prepare("SELECT * FROM kurs_bolumleri WHERE bolum_id = ?");
    $bolumsor->execute([$bolum_id]);
    $bolum = $bolumsor->fetch(PDO::FETCH_ASSOC);
    echo json_encode($bolum);
    exit;
}


// Kampanya Kodunu Kaldır
if(isset($_POST['kampanya_kaldir'])) {
    unset($_SESSION['kampanya_id']);
    unset($_SESSION['kampanya_indirim']);
    $_SESSION['kampanya_success'] = 'Kampanya kodu kaldırıldı.';
    header("Location: ../../cart.php");
    exit;
}


// Add new campaign code
if(isset($_POST['kampanya_ekle'])) {
    $indirim_tipi = $_POST['indirim_tipi'];
    $indirim_yuzdesi = $indirim_tipi == 'yuzde' ? $_POST['indirim_yuzdesi'] : null;
    $indirim_tutari = $indirim_tipi == 'tutar' ? $_POST['indirim_tutari'] : null;
    $alt_limit = $_POST['alt_limit'] ? $_POST['alt_limit'] : null;
    
    $kaydet = $db->prepare("INSERT INTO kampanya_kodlari SET
        kod = :kod,
        indirim_orani = :indirim_yuzdesi,
        indirim_tutari = :indirim_tutari,
        alt_limit = :alt_limit,
        gecerli_baslangic = :gecerli_baslangic,
        gecerli_bitis = :gecerli_bitis,
        durum = 'aktif'
    ");
    
    $insert = $kaydet->execute([
        'kod' => $_POST['kod'],
        'indirim_yuzdesi' => $indirim_yuzdesi,
        'indirim_tutari' => $indirim_tutari,
        'alt_limit' => $alt_limit,
        'gecerli_baslangic' => $_POST['gecerli_baslangic'],
        'gecerli_bitis' => $_POST['gecerli_bitis'] ?: null
    ]);
    
    if($insert) {
        Header("Location:../production/kampanya-kodlari.php?durum=ok");
    } else {
        Header("Location:../production/kampanya-kodlari.php?durum=no");
    }
    exit;
}

// Update existing campaign code
if(isset($_POST['kampanya_duzenle'])) {
    $kampanya_id = $_POST['kampanya_kodu_id'];
    $indirim_tipi = $_POST['indirim_tipi'];
    $indirim_yuzdesi = $indirim_tipi == 'yuzde' ? $_POST['indirim_yuzdesi'] : null;
    $indirim_tutari = $indirim_tipi == 'tutar' ? $_POST['indirim_tutari'] : null;
    $alt_limit = $_POST['alt_limit'] ? $_POST['alt_limit'] : null;
    
    $guncelle = $db->prepare("UPDATE kampanya_kodlari SET
        kod = :kod,
        indirim_orani = :indirim_yuzdesi,
        indirim_tutari = :indirim_tutari,
        alt_limit = :alt_limit,
        gecerli_baslangic = :gecerli_baslangic,
        gecerli_bitis = :gecerli_bitis,
        durum = :durum
        WHERE kampanya_kodu_id = :kampanya_kodu_id
    ");
    
    $update = $guncelle->execute([
        'kod' => $_POST['kod'],
        'indirim_yuzdesi' => $indirim_yuzdesi,
        'indirim_tutari' => $indirim_tutari,
        'alt_limit' => $alt_limit,
        'gecerli_baslangic' => $_POST['gecerli_baslangic'],
        'gecerli_bitis' => $_POST['gecerli_bitis'] ?: null,
        'durum' => $_POST['durum'],
        'kampanya_kodu_id' => $kampanya_id
    ]);
    
    if($update) {
        Header("Location:../production/kampanya-kodlari.php?durum=ok");
    } else {
        Header("Location:../production/kampanya-kodlari.php?durum=no");
    }
    exit;
}

// Get campaign code details for edit modal
if(isset($_GET['get_kampanya'])) {
    $kampanya_id = $_GET['get_kampanya'];
    $kampanya_sor = $db->prepare("SELECT * FROM kampanya_kodlari WHERE kampanya_kodu_id = :id");
    $kampanya_sor->execute(['id' => $kampanya_id]);
    $kampanya = $kampanya_sor->fetch(PDO::FETCH_ASSOC);
    
    echo json_encode($kampanya);
    exit;
}

// Delete campaign code
if(isset($_GET['kampanya_sil'])) {
    $kampanya_id = $_GET['kampanya_kodu_id'];
    
    $sil = $db->prepare("DELETE FROM kampanya_kodlari WHERE kampanya_kodu_id = :id");
    $delete = $sil->execute(['id' => $kampanya_id]);
    
    if($delete) {
        Header("Location:../production/kampanya-kodlari.php?durum=ok");
    } else {
        Header("Location:../production/kampanya-kodlari.php?durum=no");
    }
    exit;
}


// Add new card
if(isset($_POST['new_kart_ekle'])) {
    // First, if this is set as default, remove default from other cards
    if(isset($_POST['new_kart_varsayilan'])) {
        $update = $db->prepare("UPDATE kayitli_kartlar SET varsayilan = 0 WHERE user_id = ?");
        $update->execute([$_SESSION['userkullanici_id']]);
    }

    // In production, you should use proper encryption for card details
    $kaydet = $db->prepare("INSERT INTO kayitli_kartlar SET
        user_id = :user_id,
        kart_sahibi = :kart_sahibi,
        kart_no = :kart_no,
        son_kullanim = :son_kullanim,
        varsayilan = :varsayilan
    ");

    $insert = $kaydet->execute([
        'user_id' => $_SESSION['userkullanici_id'],
        'kart_sahibi' => $_POST['new_kart_sahibi'],
        'kart_no' => $_POST['new_kart_no'], // Should be encrypted in production
        'son_kullanim' => $_POST['new_son_kullanim'],
        'varsayilan' => isset($_POST['new_kart_varsayilan']) ? 1 : 0
    ]);

    if($insert) {
        $_SESSION['new_card_id'] = $db->lastInsertId();
        header("Location: ../../checkout-payment.php?durum=ok");
    } else {
        header("Location: ../../checkout-payment.php?durum=no");
    }
}



// Handle billing address submission
if(isset($_POST['fatura_adres_kaydet'])) {
    // If new address is being added
    
    if(!empty($_POST['new_ad_soyad'])) {
        // If this is set as default, remove default from other addresses
        
        if(isset($_POST['new_varsayilan'])) {
            $update = $db->prepare("UPDATE fatura_adresleri SET varsayilan = 0 WHERE user_id = ?");
            $update->execute([$_SESSION['userkullanici_id']]);
        }

        $kaydet = $db->prepare("INSERT INTO fatura_adresleri SET
            user_id = :user_id,
            ad_soyad = :ad_soyad,
            telefon = :telefon,
            tc_no = :tc_no,
            adres = :adres,
            il = :il,
            ilce = :ilce,
            varsayilan = :varsayilan
        ");

        $insert = $kaydet->execute([
            'user_id' => $_SESSION['userkullanici_id'],
            'ad_soyad' => $_POST['new_ad_soyad'],
            'telefon' => $_POST['new_telefon'],
            'tc_no' => $_POST['new_tc_no'],
            'adres' => $_POST['new_adres'],
            'il' => $_POST['new_il'],
            'ilce' => $_POST['new_ilce'],
            'varsayilan' => isset($_POST['new_varsayilan']) ? 1 : 0
        ]);
        if($insert) {
            $_SESSION['fatura_adres_id'] = $db->lastInsertId();
            header("Location: ../../checkout-payment-method.php");
        } else {
            header("Location: ../../checkout-address.php?durum=no");
        }
    }
    // If existing address is selected
    elseif(isset($_POST['fatura_adres_id'])) {
        $_SESSION['fatura_adres_id'] = $_POST['fatura_adres_id'];
        header("Location: ../../checkout-payment-method.php");
    }
    else {
        header("Location: ../../checkout-address.php?durum=no");
    }
    exit;
}

// Handle payment method selection
if(isset($_POST['odeme_yontemi_kaydet'])) {
    $_SESSION['odeme_yontemi'] = $_POST['odeme_yontemi'];
    
    // Redirect based on payment method
    if($_POST['odeme_yontemi'] == 'kredi_karti') {
        header("Location: ../../checkout-paytr.php");
        exit();
    } else if($_POST['odeme_yontemi'] == 'havale') {
        header("Location: ../../checkout-havale.php");
        exit();
    }
}

// Handle credit card payment
if(isset($_POST['siparisi_tamamla']) && isset($_SESSION['odeme_yontemi']) && $_SESSION['odeme_yontemi'] == 'kredi_karti') {
    try {
        $db->beginTransaction();

        // Generate unique invoice number
        $check = true;
        while($check){
            $fatura_no = 'INV' . date('Ymd') . '' . rand(1000, 9999);
            $check = $db->query("SELECT 1 FROM faturalar WHERE fatura_no = '$fatura_no'")->fetch(PDO::FETCH_ASSOC);
        }

        // Save new card if provided
        if(!empty($_POST['new_kart_sahibi'])) {
            if(isset($_POST['new_kart_varsayilan'])) {
                $update = $db->prepare("UPDATE kayitli_kartlar SET varsayilan = 0 WHERE user_id = ?");
                $update->execute([$_SESSION['userkullanici_id']]);
            }

            $kart_kaydet = $db->prepare("INSERT INTO kayitli_kartlar SET
                user_id = :user_id,
                kart_sahibi = :kart_sahibi,
                kart_no = :kart_no,
                son_kullanim = :son_kullanim,
                varsayilan = :varsayilan
            ");

            $kart_insert = $kart_kaydet->execute([
                'user_id' => $_SESSION['userkullanici_id'],
                'kart_sahibi' => $_POST['new_kart_sahibi'],
                'kart_no' => $_POST['new_kart_no'],
                'son_kullanim' => $_POST['new_son_kullanim'],
                'varsayilan' => isset($_POST['new_kart_varsayilan']) ? 1 : 0
            ]);
        }

        // Create invoice and process order
        $fatura = createOrder($db, $fatura_no, 'kredi_karti', 'onaylandi');
        
        $db->commit();

        // Clear checkout session data
        unset($_SESSION['fatura_adres_id']);
        unset($_SESSION['odeme_yontemi']);
        
        // Set success session variable
        $_SESSION['son_siparis_no'] = $fatura_no;
        
        header("Location: ../../siparis-basarili.php");
        exit();

    } catch(Exception $e) {
        $db->rollBack();
        header("Location: ../../checkout-payment.php?durum=no");
    }
}

// Handle bank transfer order
if(isset($_POST['siparisi_tamamla']) && isset($_SESSION['odeme_yontemi']) && $_SESSION['odeme_yontemi'] == 'havale') {
    
    try {
        $db->beginTransaction();

        // Generate unique invoice number
        
        $fatura_no = $_POST['fatura_no'];
        // Create invoice and process order
        
        $fatura = createOrder($db, $fatura_no, 'havale', 'beklemede');
        $db->commit();
        
        // Clear checkout session data
        unset($_SESSION['fatura_adres_id']);
        unset($_SESSION['odeme_yontemi']);
        
        // Set success session variable
        $_SESSION['son_siparis_no'] = $fatura_no;
        
        header("Location: ../../siparis-basarili.php");
        exit();

    } catch(Exception $e) {
        $db->rollBack();
        header("Location: ../../checkout-havale.php?durum=no");
    }
}

// Helper function to create order
function createOrder($db, $fatura_no, $odeme_yontemi, $odeme_durumu) {
    // Get cart items and calculate totals
    $cart_items = $db->prepare("SELECT k.*, s.id as sepet_id, s.selected_certs, s.cert_total_price 
        FROM sepet s 
        JOIN kurslar k ON s.course_id = k.kurs_id 
        WHERE s.user_id = :user_id");
    $cart_items->execute(['user_id' => $_SESSION['userkullanici_id']]);
    
    $ara_toplam = 0;
    $cart_courses = $cart_items->fetchAll(PDO::FETCH_ASSOC);
    

    foreach($cart_courses as $item) {
        // Base course price
        
        $course_total = 0;
        // Add certificate total price if exists
        if(isset($item['cert_total_price']) && $item['cert_total_price'] > 0) {
            $course_total += $item['cert_total_price'];
        }
        
        $ara_toplam += $course_total;
    }
    // Apply campaign discount if exists
    $indirim_tutari = 0;
    $kampanya_id = null;
    if(isset($_SESSION['kampanya_indirim'])) {
        if($_SESSION['kampanya_tur'] == 'yuzde') {
            $indirim_tutari = $ara_toplam * ($_SESSION['kampanya_indirim'] / 100);
        } else {
            $indirim_tutari = $_SESSION['kampanya_indirim'];
        }
        $kampanya_id = $_SESSION['kampanya_id'];
    }
    
    $toplam_tutar = $ara_toplam - $indirim_tutari;

    // Create invoice
    $fatura_ekle = $db->prepare("INSERT INTO faturalar SET
        user_id = :user_id,
        fatura_no = :fatura_no,
        fatura_adres_id = :fatura_adres_id,
        ara_toplam = :ara_toplam,
        kampanya_kodu_id = :kampanya_kodu_id,
        indirim_tutari = :indirim_tutari,
        toplam_tutar = :toplam_tutar,
        odeme_yontemi = :odeme_yontemi,
        odeme_durumu = :odeme_durumu
    ");
    
    $fatura_ekle->execute([
        'user_id' => $_SESSION['userkullanici_id'],
        'fatura_no' => $fatura_no,
        'fatura_adres_id' => $_SESSION['fatura_adres_id'],
        'ara_toplam' => $ara_toplam,
        'kampanya_kodu_id' => $kampanya_id,
        'indirim_tutari' => $indirim_tutari,
        'toplam_tutar' => $toplam_tutar,
        'odeme_yontemi' => $odeme_yontemi,
        'odeme_durumu' => $odeme_durumu
    ]);
    
    $fatura_id = $db->lastInsertId();
    
    // Record sold courses with certificate selections
    foreach($cart_courses as $course) {
        // Parse selected certificates
        $selected_certs = [];
        if(!empty($course['selected_certs'])) {
            $selected_certs = $course['selected_certs'];
        }
        $has_kurum_cert = 0;
        $has_uni_cert = 0;
        if($selected_certs == 'kurum_cert') {
            $has_kurum_cert = 1;
        } else if($selected_certs == 'uni_cert') {
            $has_uni_cert = 1;
        } else if($selected_certs == 'both_cert') {
            $has_kurum_cert = 1;
            $has_uni_cert = 1;
        }

        
        // Create the SQL with certificate fields
        $satis_ekle = $db->prepare("INSERT INTO satilan_kurslar SET
            fatura_id = :fatura_id,
            kurs_id = :kurs_id,
            fiyat = :fiyat,
            has_kurum_cert = :kurum_cert,
            has_uni_cert = :uni_cert
        ");
        $satis_ekle->execute([
            'fatura_id' => $fatura_id,
            'kurs_id' => $course['kurs_id'],
            'fiyat' => $course['cert_total_price'],
            'kurum_cert' => $has_kurum_cert,
            'uni_cert' => $has_uni_cert
        ]);
        
    }

    // Clear cart
    $cart_clear = $db->prepare("DELETE FROM sepet WHERE user_id = ?");
    $cart_clear->execute([$_SESSION['userkullanici_id']]);

    // Clear campaign session data
    unset($_SESSION['kampanya_indirim']);
    unset($_SESSION['kampanya_id']);
    unset($_SESSION['kampanya_tur']);

    return $fatura_id;
}
// Handle campaign code application
if(isset($_POST['kampanya_uygula'])) {
    
    $kampanya_kodu = $_POST['kampanya_kodu'];
    
    // Get cart total first
    $cart_total = $db->prepare("
        SELECT SUM(k.fiyat) as toplam 
        FROM sepet s 
        JOIN kurslar k ON s.course_id = k.kurs_id 
        WHERE s.user_id = :user_id
    ");
    $cart_total->execute(['user_id' => $_SESSION['userkullanici_id']]);
    $total = $cart_total->fetch(PDO::FETCH_ASSOC)['toplam'];
    // Check coupon
    $kampanya_sor = $db->prepare("
        SELECT * FROM kampanya_kodlari 
        WHERE kod = :kod 
        AND gecerli_baslangic <= NOW() 
        AND gecerli_bitis >= NOW() 
    ");
    $kampanya_sor->execute(['kod' => $kampanya_kodu]);
    
    if($kampanya_sor->rowCount() > 0) {
        $kampanya = $kampanya_sor->fetch(PDO::FETCH_ASSOC);
        if($total < $kampanya['alt_limit']) {
            $_SESSION['kampanya_error'] = "Bu kampanya kodu için minimum alışveriş tutarını karşılamadınız.";
            header("Location: ../../cart.php");
            exit();
        }
        
        // Check if user already used this coupon
        
            // Calculate discount
            if(isset($kampanya['indirim_orani']) && $kampanya['indirim_orani'] > 0) {
                $indirim_tutari = $total * ($kampanya['indirim_orani'] / 100);
                $_SESSION['kampanya_indirim'] = $kampanya['indirim_orani'];
                $_SESSION['kampanya_tur'] = 'yuzde';
            } else {
                $indirim_tutari = $kampanya['indirim_tutari'];
                $_SESSION['kampanya_indirim'] = $kampanya['indirim_tutari'];
                $_SESSION['kampanya_tur'] = 'tutar';
            }
            
            $_SESSION['kampanya_id'] = $kampanya['kampanya_id'];
            $_SESSION['kampanya_success'] = "Kampanya kodu başarıyla uygulandı!";
        
    } else {
        $_SESSION['kampanya_error'] = "Geçersiz veya süresi dolmuş kampanya kodu.";
    }
    
    header("Location: ../../cart.php");
    exit();
}

// Handle order approval
if(isset($_GET['siparis_onay'])) {
    $fatura_id = intval($_GET['siparis_onay']);
    
    try {
        $db->beginTransaction();

        // Update order status
        $guncelle = $db->prepare("UPDATE faturalar SET odeme_durumu = 'onaylandi' WHERE fatura_id = ?");
        $guncelle->execute([$fatura_id]);

        $db->commit();
        
        // Preserve existing filters in the redirect
        $filterParams = '';
        if(isset($_GET['durum'])) {
            $filterParams .= '&durum=' . $_GET['durum'];
        }
        if(isset($_GET['odeme_tipi'])) {
            $filterParams .= '&odeme_tipi=' . $_GET['odeme_tipi'];
        }
        
        if($guncelle) {
            Header("Location:../production/siparisler.php?islem=ok" . $filterParams);
        } else {
            Header("Location:../production/siparisler.php?islem=no" . $filterParams);
        }
        exit;

    } catch(Exception $e) {
        $db->rollBack();
        header("Location: ../production/siparisler.php?durum=no");
    }
}

// Handle order cancellation
if(isset($_GET['siparis_iptal'])) {
    $fatura_id = intval($_GET['siparis_iptal']);
    
    $guncelle = $db->prepare("UPDATE faturalar SET odeme_durumu = 'iptal_edildi' WHERE fatura_id = ?");
    if($guncelle->execute([$fatura_id])) {
        header("Location: ../production/siparisler.php?durum=ok");
    } else {
        header("Location: ../production/siparisler.php?durum=no");
    }
    
    // Preserve existing filters in the redirect
    $filterParams = '';
    if(isset($_GET['durum'])) {
        $filterParams .= '&durum=' . $_GET['durum'];
    }
    if(isset($_GET['odeme_tipi'])) {
        $filterParams .= '&odeme_tipi=' . $_GET['odeme_tipi'];
    }
    
    if($guncelle) {
        Header("Location:../production/siparisler.php?islem=ok" . $filterParams);
    } else {
        Header("Location:../production/siparisler.php?islem=no" . $filterParams);
    }
    exit;
}

// Update user info
if(isset($_POST['kullanici_bilgi_guncelle'])) {
    $guncelle = $db->prepare("UPDATE kullanici SET
        kullanici_ad = :ad,
        kullanici_soyad = :soyad,
        kullanici_tel = :gsm
        WHERE kullanici_id = :id");
        
    $update = $guncelle->execute([
        'ad' => $_POST['kullanici_ad'],
        'soyad' => $_POST['kullanici_soyad'],
        'gsm' => $_POST['kullanici_gsm'],
        'id' => $_SESSION['userkullanici_id']
    ]);

    if($update) {
        header("Location: ../../profile.php?durum=ok");
    } else {
        header("Location: ../../profile.php?durum=no");
    }
}

// Update password
if(isset($_POST['kullanici_sifre_guncelle'])) {
    if($_POST['yeni_sifre'] != $_POST['yeni_sifre_tekrar']) {
        header("Location: ../../profile.php?durum=sifre_eslesmiyor");
        exit;
    }

    $kullanici = $db->prepare("SELECT * FROM kullanici WHERE kullanici_id = ?");
    $kullanici->execute([$_SESSION['userkullanici_id']]);
    $kullanici_detay = $kullanici->fetch(PDO::FETCH_ASSOC);

    if(md5($_POST['eski_sifre']) != $kullanici_detay['kullanici_password']) {
        header("Location: ../../profile.php?durum=eski_sifre_yanlis");
        exit;
    }

    $guncelle = $db->prepare("UPDATE kullanici SET
        kullanici_password = :sifre
        WHERE kullanici_id = :id");
        
    $update = $guncelle->execute([
        'sifre' => md5($_POST['yeni_sifre']),
        'id' => $_SESSION['userkullanici_id']
    ]);

    if($update) {
        header("Location: ../../profile.php?durum=ok");
    } else {
        header("Location: ../../profile.php?durum=no");
    }
}

// Add new address
if(isset($_POST['yeni_adres_ekle'])) {
    if(isset($_POST['varsayilan'])) {
        $varsayilan_sifirla = $db->prepare("UPDATE fatura_adresleri SET varsayilan = 0 WHERE user_id = ?");
        $varsayilan_sifirla->execute([$_SESSION['userkullanici_id']]);
    }

    $kaydet = $db->prepare("INSERT INTO fatura_adresleri SET
        user_id = :user_id,
        ad_soyad = :ad_soyad,
        telefon = :telefon,
        tc_no = :tc_no,
        adres = :adres,
        il = :il,
        ilce = :ilce,
        posta_kodu = :posta_kodu,
        varsayilan = :varsayilan
    ");

    $insert = $kaydet->execute([
        'user_id' => $_SESSION['userkullanici_id'],
        'ad_soyad' => $_POST['ad_soyad'],
        'telefon' => $_POST['telefon'],
        'tc_no' => $_POST['tc_no'],
        'adres' => $_POST['adres'],
        'il' => $_POST['il'],
        'ilce' => $_POST['ilce'],
        'posta_kodu' => $_POST['posta_kodu'],
        'varsayilan' => isset($_POST['varsayilan']) ? 1 : 0
    ]);

    if($insert) {
        header("Location: ../../profile.php?durum=ok");
    } else {
        header("Location: ../../profile.php?durum=no");
    }
}

// Make address default
if(isset($_GET['adres_varsayilan'])) {
    $varsayilan_sifirla = $db->prepare("UPDATE fatura_adresleri SET varsayilan = 0 WHERE user_id = ?");
    $varsayilan_sifirla->execute([$_SESSION['userkullanici_id']]);

    $varsayilan_yap = $db->prepare("UPDATE fatura_adresleri SET varsayilan = 1 
        WHERE fatura_adres_id = ? AND user_id = ?");
    $update = $varsayilan_yap->execute([
        $_GET['adres_varsayilan'],
        $_SESSION['userkullanici_id']
    ]);

    if($update) {
        header("Location: ../../profile.php?durum=ok");
    } else {
        header("Location: ../../profile.php?durum=no");
    }
}

// Delete address
if(isset($_GET['adres_sil'])) {
    $sil = $db->prepare("DELETE FROM fatura_adresleri 
        WHERE fatura_adres_id = ? AND user_id = ? AND varsayilan = 0");
    $delete = $sil->execute([
        $_GET['adres_sil'],
        $_SESSION['userkullanici_id']
    ]);

    if($delete) {
        header("Location: ../../profile.php?durum=ok");
    } else {
        header("Location: ../../profile.php?durum=no");
    }
}

// Get address details for editing
if(isset($_GET['adres_getir'])) {
    $adres_id = intval($_GET['adres_getir']);
    
    $adres_sor = $db->prepare("SELECT * FROM fatura_adresleri 
        WHERE fatura_adres_id = ? AND user_id = ?");
    $adres_sor->execute([$adres_id, $_SESSION['userkullanici_id']]);
    $adres = $adres_sor->fetch(PDO::FETCH_ASSOC);
    
    header('Content-Type: application/json');
    echo json_encode($adres);
    exit;
}

// Update address
if(isset($_POST['adres_duzenle'])) {
    $guncelle = $db->prepare("UPDATE fatura_adresleri SET
        ad_soyad = :ad_soyad,
        telefon = :telefon,
        tc_no = :tc_no,
        adres = :adres,
        il = :il,
        ilce = :ilce,
        posta_kodu = :posta_kodu
        WHERE fatura_adres_id = :id AND user_id = :user_id");
        
    $update = $guncelle->execute([
        'ad_soyad' => $_POST['ad_soyad'],
        'telefon' => $_POST['telefon'],
        'tc_no' => $_POST['tc_no'],
        'adres' => $_POST['adres'],
        'il' => $_POST['il'],
        'ilce' => $_POST['ilce'],
        'posta_kodu' => $_POST['posta_kodu'],
        'id' => $_POST['fatura_adres_id'],
        'user_id' => $_SESSION['userkullanici_id']
    ]);

    if($update) {
        header("Location: ../../profile.php?durum=ok");
    } else {
        header("Location: ../../profile.php?durum=no");
    }
}

if(isset($_POST['fatura_adres_guncelle'])) {
    $fatura_adres_id = $_POST['fatura_adres_id'];
    
    // If this address is being set as default, unset all other defaults first
    if(isset($_POST['edit_varsayilan']) && $_POST['edit_varsayilan'] == 1) {
        $varsayilan_sifirla = $db->prepare("
            UPDATE fatura_adresleri 
            SET varsayilan = 0 
            WHERE user_id = :user_id
        ");
        $varsayilan_sifirla->execute(['user_id' => $_SESSION['userkullanici_id']]);
    }
    
    // Update the address
    $update = $db->prepare("
        UPDATE fatura_adresleri SET
            ad_soyad = :ad_soyad,
            telefon = :telefon,
            tc_no = :tc_no,
            adres = :adres,
            il = :il,
            ilce = :ilce,
            varsayilan = :varsayilan
        WHERE fatura_adres_id = :fatura_adres_id 
        AND user_id = :user_id
    ");
    
    $result = $update->execute([
        'ad_soyad' => $_POST['edit_ad_soyad'],
        'telefon' => $_POST['edit_telefon'],
        'tc_no' => $_POST['edit_tc_no'],
        'adres' => $_POST['edit_adres'],
        'il' => $_POST['edit_il'],
        'ilce' => $_POST['edit_ilce'],
        'varsayilan' => isset($_POST['edit_varsayilan']) ? 1 : 0,
        'fatura_adres_id' => $fatura_adres_id,
        'user_id' => $_SESSION['userkullanici_id']
    ]);
    
    if($result) {
        Header("Location:../../checkout-address.php?durum=ok");
    } else {
        Header("Location:../../checkout-address.php?durum=no");
    }
    exit;
}

if(isset($_POST["buyNow"])) {
    // Server-side security check is still needed
    if(!isset($_SESSION['userkullanici_id'])){
        // Instead of redirecting to login, return JSON response
        // This is a fallback in case JavaScript validation is bypassed
        echo json_encode(['error' => 'login_required']);
        exit;
    }
    
    $kurs_id = $_POST['course_id'];
    $selected_cert = isset($_POST['selected_cert']) ? $_POST['selected_cert'] : '';
    $cert_price = isset($_POST['cert_price']) ? floatval($_POST['cert_price']) : 0;
    $total_price = isset($_POST['total_price']) ? floatval($_POST['total_price']) : 0;
    
    // Validate that a certificate option is selected
    if (empty($selected_cert)) {
        Header("Location:../../kurs-detay.php?kurs_id=$kurs_id&durum=sertifikasec");
        exit;
    }

    // Convert single certificate selection to the format expected by the database
    $selected_certs = '';
    if ($selected_cert == 'kurum_cert') {
        $selected_certs = 'kurum_cert';
    } else if ($selected_cert == 'uni_cert') {
        $selected_certs = 'uni_cert';
    } else if ($selected_cert == 'both_cert') {
        $selected_certs = 'both_cert';
    }
    
    // Check if course already in cart
    $cart_check = $db->prepare("SELECT * FROM sepet WHERE user_id = :user_id AND course_id = :course_id");
    $cart_check->execute([
        'user_id' => $_SESSION["userkullanici_id"],
        'course_id' => $kurs_id
    ]);
    
    if($cart_check->rowCount() == 0) {
        // Insert new cart item
        $cart_add = $db->prepare("INSERT INTO sepet 
            (user_id, course_id, selected_certs, cert_total_price) 
            VALUES (:user_id, :course_id, :selected_certs, :cert_total_price)");
            
        $insert = $cart_add->execute([
            'user_id' => $_SESSION["userkullanici_id"],
            'course_id' => $kurs_id,
            'selected_certs' => $selected_certs,
            'cert_total_price' => $total_price
        ]);
        if($insert) {
            Header("Location:../../checkout-address.php");
            exit;
        }
    } else {
        // Update existing cart item
        $cart_update = $db->prepare("UPDATE sepet SET 
            selected_certs = :selected_certs,
            cert_total_price = :cert_total_price
            WHERE user_id = :user_id AND course_id = :course_id");
            
        $update = $cart_update->execute([
            'selected_certs' => $selected_certs,
            'cert_total_price' => $total_price,
            'user_id' => $_SESSION["userkullanici_id"],
            'course_id' => $kurs_id
        ]);
        
        if($update) {
            Header("Location:../../checkout-address.php");
            exit;
        }
    }
}

// Banka Hesabı İşlemleri
if ($_POST['islem'] == 'banka_ekle') {
    try {
        // Debug
        error_log("Banka ekleme başladı - POST verisi: " . print_r($_POST, true));

        $stmt = $db->prepare("INSERT INTO banka_hesaplari 
            (banka_adi, hesap_sahibi, sube_adi, sube_kodu, hesap_no, iban, durum) 
            VALUES (?, ?, ?, ?, ?, ?, ?)");
        
        $insert = $stmt->execute([
            htmlspecialchars($_POST['banka_adi']),
            htmlspecialchars($_POST['hesap_sahibi']),
            htmlspecialchars($_POST['sube_adi']),
            htmlspecialchars($_POST['sube_kodu']),
            htmlspecialchars($_POST['hesap_no']),
            htmlspecialchars($_POST['iban']),
            intval($_POST['durum'])
        ]);

        if ($insert) {
            error_log("Banka ekleme başarılı");
            echo 'ok';
        } else {
            error_log("Banka ekleme başarısız - PDO hata: " . print_r($stmt->errorInfo(), true));
            echo 'no';
        }

    } catch(PDOException $e) {
        error_log("PDO Hatası: " . $e->getMessage());
        echo 'no';
    }
    exit;
}

if ($_POST['islem'] == 'banka_duzenle') {
    try {
        $stmt = $db->prepare("UPDATE banka_hesaplari SET 
            banka_adi = ?,
            hesap_sahibi = ?,
            sube_adi = ?,
            sube_kodu = ?,
            hesap_no = ?,
            iban = ?,
            durum = ?
            WHERE id = ?");
        
        $update = $stmt->execute([
            htmlspecialchars($_POST['banka_adi']),
            htmlspecialchars($_POST['hesap_sahibi']),
            htmlspecialchars($_POST['sube_adi']),
            htmlspecialchars($_POST['sube_kodu']),
            htmlspecialchars($_POST['hesap_no']),
            htmlspecialchars($_POST['iban']),
            intval($_POST['durum']),
            intval($_POST['id'])
        ]);

        echo $update ? 'ok' : 'no';
    } catch(PDOException $e) {
        error_log("PDO Hatası: " . $e->getMessage());
        echo 'no';
    }
    exit;
}

if ($_POST['islem'] == 'banka_sil') {
    try {
        $stmt = $db->prepare("DELETE FROM banka_hesaplari WHERE id = ?");
        $delete = $stmt->execute([intval($_POST['id'])]);
        
        echo $delete ? 'ok' : 'no';
    } catch(PDOException $e) {
        error_log("PDO Hatası: " . $e->getMessage());
        echo 'no';
    }
    exit;
}

if (isset($_POST['kullanici_resim_guncelle'])) {
    $uploads_dir = '../../dimg/user';
    
    $tmp_name = $_FILES['kullanici_resim']["tmp_name"];
    $name = $_FILES['kullanici_resim']["name"];
    $benzersizsayi1 = rand(20000, 32000);
    $benzersizsayi2 = rand(20000, 32000);
    $benzersizad = $benzersizsayi1 . $benzersizsayi2;
    $refimgyol = substr($uploads_dir, 6) . "/" . $benzersizad . $name;
    
    // Check file type
    $allowed = array('jpg', 'jpeg', 'png');
    $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
    if (!in_array($ext, $allowed)) {
        header("Location:../../profile.php?durum=gecersiz-format");
        exit;
    }
    
    // Check file size (1MB max)
    if ($_FILES['kullanici_resim']["size"] > 1048576) {
        header("Location:../../profile.php?durum=buyuk-boyut");
        exit;
    }
    
    // Delete old image if exists
    $kullanici = $db->prepare("SELECT kullanici_resim FROM kullanici WHERE kullanici_id = ?");
    $kullanici->execute([$_SESSION['userkullanici_id']]);
    $eski_resim = $kullanici->fetch(PDO::FETCH_ASSOC)['kullanici_resim'];
    
    if ($eski_resim && $eski_resim != 'dimg/user/imgg.jpg') {
        $eski_resim_yol = "../../" . $eski_resim;
        if (file_exists($eski_resim_yol)) {
            unlink($eski_resim_yol);
        }
    }
    
    // Upload new image
    if (move_uploaded_file($tmp_name, "$uploads_dir/$benzersizad$name")) {
        $duzenle = $db->prepare("UPDATE kullanici SET kullanici_resim = ? WHERE kullanici_id = ?");
        $uploads_dir = str_replace("../../", "", $uploads_dir);
        $update = $duzenle->execute([$uploads_dir . "/" . $benzersizad . $name, $_SESSION['userkullanici_id']]);
        
        if ($update) {
            header("Location:../../profile.php?durum=ok");
        } else {
            header("Location:../../profile.php?durum=no");
        }
    } else {
        header("Location:../../profile.php?durum=no");
    }
}

// PayTR Settings Update
if(isset($_POST['paytr_ayar_kaydet'])) {
    $guncelle = $db->prepare("UPDATE paytr_ayar SET 
        merchant_id = ?,
        merchant_key = ?,
        merchant_salt = ?,
        test_mode = ?,
        installment_mode = ?,
        max_installment = ?
        WHERE id = 1");
        
    $update = $guncelle->execute([
        $_POST['merchant_id'],
        $_POST['merchant_key'],
        $_POST['merchant_salt'],
        $_POST['test_mode'],
        $_POST['installment_mode'],
        $_POST['max_installment']
    ]);

    if($update) {
        header("Location: ../production/paytr-ayar.php?durum=ok");
    } else {
        header("Location: ../production/paytr-ayar.php?durum=no");
    }
}

if(isset($_GET['action']) && $_GET['action'] == 'removeCoupon') {
    // Remove all coupon-related session variables
    unset($_SESSION['kampanya_indirim']);
    unset($_SESSION['kampanya_tur']);
    unset($_SESSION['kampanya_kod']);
    
    // Redirect back to cart
    Header("Location:../../cart.php");
    exit;
}

// SSS Edit Handler
if(isset($_POST['sssduzenle'])) {
    $sss_id = $_POST['sss_id'];
    
    $update = $db->prepare("UPDATE sss SET 
        sss_soru = :sss_soru,
        sss_aciklama = :sss_aciklama,
        sss_sira = :sss_sira
        WHERE sss_id = :sss_id");
        
    $result = $update->execute([
        'sss_soru' => $_POST['sss_soru'],
        'sss_aciklama' => $_POST['sss_aciklama'],
        'sss_sira' => $_POST['sss_sira'],
        'sss_id' => $sss_id
    ]);
    
    if($result) {
        Header("Location:../production/sss.php?durum=ok");
    } else {
        Header("Location:../production/sss.php?durum=no");
    }
    exit;
}

// SSS Delete Handler
if(isset($_GET['ssssil']) && $_GET['ssssil'] == 'ok') {
    $delete = $db->prepare("DELETE FROM sss WHERE sss_id = :id");
    $result = $delete->execute(['id' => $_GET['sss_id']]);
    
    if($result) {
        Header("Location:../production/sss.php?sil=ok");
    } else {
        Header("Location:../production/sss.php?sil=no");
    }
    exit;
}

// Add course access credentials
if(isset($_POST['kurs_erisim_ekle'])) {
    $satis_id = $_POST['satis_id'];
    $link = $_POST['link'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $notlar = $_POST['notlar'];
    
    // Check if credentials already exist for this sale
    $check = $db->prepare("SELECT * FROM satilan_kurslar_credentials WHERE satis_id = :satis_id");
    $check->execute(['satis_id' => $satis_id]);
    
    if($check->rowCount() > 0) {
        // Already exists - redirect with error message
        Header("Location:../production/kurs-erisimi.php?durum=zatenvar");
        exit;
    }
    
    // Insert new credentials
    $insert = $db->prepare("INSERT INTO satilan_kurslar_credentials (satis_id, link, username, password, notlar) 
                           VALUES (:satis_id, :link, :username, :password, :notlar)");
    
    $result = $insert->execute([
        'satis_id' => $satis_id,
        'link' => $link,
        'username' => $username,
        'password' => $password,
        'notlar' => $notlar
    ]);
    
    if($result) {
        Header("Location:../production/kurs-erisimi.php?durum=ok");
    } else {
        Header("Location:../production/kurs-erisimi.php?durum=no");
    }
    exit;
}

// Update course access credentials
if(isset($_POST['kurs_erisim_duzenle'])) {
    $credential_id = $_POST['credential_id'];
    $link = $_POST['link'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $notlar = $_POST['notlar'];
    
    $update = $db->prepare("UPDATE satilan_kurslar_credentials SET 
                           link = :link,
                           username = :username,
                           password = :password,
                           notlar = :notlar
                           WHERE credential_id = :credential_id");
    
    $result = $update->execute([
        'link' => $link,
        'username' => $username,
        'password' => $password,
        'notlar' => $notlar,
        'credential_id' => $credential_id
    ]);
    
    if($result) {
        Header("Location:../production/kurs-erisimi.php?durum=ok");
    } else {
        Header("Location:../production/kurs-erisimi.php?durum=no");
    }
    exit;
}

if(isset($_GET['kurs_sil']) && $_GET['kurs_sil'] == 'ok') {
    // Get kurs_id before deleting for redirect
    $modulsor = $db->prepare("SELECT kurs_id FROM kurslar WHERE kurs_id = :id");
    $modulsor->execute(['id' => $_GET['kurs_id']]);
    $modul = $modulsor->fetch(PDO::FETCH_ASSOC);
    $kurs_id = $modul['kurs_id'];

    // Delete module (cascade will handle sections)
    $sil = $db->prepare("DELETE FROM kurslar WHERE kurs_id = :id");
    $kontrol = $sil->execute(['id' => $_GET['kurs_id']]);

    if($kontrol) {
        Header("Location:../production/kurs.php?durum=ok");
    } else {
        Header("Location:../production/kurs.php?durum=no");
    }
}

// Handle Google login
if (isset($_GET['google_login']) && $_GET['google_login'] == 1) {
    if (isset($_SESSION['google_auth'])) {
        // Set user session variables
        $_SESSION['userkullanici_id'] = $_SESSION['google_auth']['kullanici_id'];
        $_SESSION['userkullanici_mail'] = $_SESSION['google_auth']['kullanici_mail'];
        $_SESSION['userkullanici_adsoyad'] = $_SESSION['google_auth']['kullanici_ad']. " ". $_SESSION['google_auth']['kullanici_soyad'];
        
        // Clean up temporary session data
        unset($_SESSION['google_auth']);
        
        // Redirect to dashboard
        header("Location: ../../index.php");
        exit;
    } else {
        // No Google auth data
        $_SESSION['error'] = "Google ile giriş bilgileri bulunamadı.";
        header("Location: ../../login.php");
        exit;
    }
}

// Handle Google registration
if (isset($_GET['google_register']) && $_GET['google_register'] == 1) {
    if (isset($_SESSION['google_auth'])) {
        // Set user session variables
        $_SESSION['userkullanici_id'] = $_SESSION['google_auth']['kullanici_id'];
        $_SESSION['userkullanici_mail'] = $_SESSION['google_auth']['kullanici_mail'];
        $_SESSION['userkullanici_adsoyad'] = $_SESSION['google_auth']['kullanici_ad']. " ". $_SESSION['google_auth']['kullanici_soyad'];
        
        // Clean up temporary session data
        unset($_SESSION['google_auth']);
        
        // Redirect to dashboard with welcome message
        $_SESSION['success'] = "Google hesabınızla başarıyla kayıt oldunuz.";
        header("Location: ../../index.php");
        exit;
    } else {
        // No Google auth data
        $_SESSION['error'] = "Google ile kayıt bilgileri bulunamadı.";
        header("Location: ../../login.php");
        exit;
    }
}

// Anasayfa Verileri Güncelleme
if (isset($_POST['anasayfaveriayarkaydet'])) {
    
    $ayarkaydet = $db->prepare("UPDATE anasayfa_veri SET
        toplam_ogrenci = :toplam_ogrenci,
        toplam_kurs = :toplam_kurs,
        mutlu_ogrenci = :mutlu_ogrenci,
        deneyim = :deneyim,
        olumlu_yorum = :olumlu_yorum,
        yuzdelik1_isim = :yuzdelik1_isim,
        yuzdelik1_yuzde = :yuzdelik1_yuzde,
        yuzdelik2_isim = :yuzdelik2_isim,
        yuzdelik2_yuzde = :yuzdelik2_yuzde,
        yuzdelik3_isim = :yuzdelik3_isim,
        yuzdelik3_yuzde = :yuzdelik3_yuzde
        WHERE id = 1");
    
    $update = $ayarkaydet->execute([
        'toplam_ogrenci' => $_POST['toplam_ogrenci'],
        'toplam_kurs' => $_POST['toplam_kurs'],
        'mutlu_ogrenci' => $_POST['mutlu_ogrenci'],
        'deneyim' => $_POST['deneyim'],
        'olumlu_yorum' => $_POST['olumlu_yorum'],
        'yuzdelik1_isim' => $_POST['yuzdelik1_isim'],
        'yuzdelik1_yuzde' => $_POST['yuzdelik1_yuzde'],
        'yuzdelik2_isim' => $_POST['yuzdelik2_isim'],
        'yuzdelik2_yuzde' => $_POST['yuzdelik2_yuzde'],
        'yuzdelik3_isim' => $_POST['yuzdelik3_isim'],
        'yuzdelik3_yuzde' => $_POST['yuzdelik3_yuzde']
    ]);

    if ($update) {
        header("Location:../production/anasayfa_veriler.php?durum=ok");
    } else {
        header("Location:../production/anasayfa_veriler.php?durum=no");
    }
}

// KVKK ve Gizlilik Politikası Güncelleme
if (isset($_POST['kvkkgizlilikayarkaydet'])) {

    $ayarkaydet = $db->prepare("UPDATE kvkk_gizlilik SET
        kvkk_metin = :kvkk_metin,
        gizlilik_politikasi = :gizlilik_politikasi
        WHERE id = 1");

    $update = $ayarkaydet->execute([
        'kvkk_metin' => $_POST['kvkk_metin'],
        'gizlilik_politikasi' => $_POST['gizlilik_politikasi']
    ]);
    if ($update) {
        header("Location:../production/kvkk-gizlilik.php?durum=ok");
    } else {
        header("Location:../production/kvkk-gizlilik.php?durum=no");
    }
}

// Forgot Password
if (isset($_POST['forgot_password'])) {
    $email = $_POST['email'];
    
    // Check if email exists
    $kullanicisor = $db->prepare("SELECT * FROM kullanici WHERE kullanici_mail=:mail");
    $kullanicisor->execute([
        'mail' => $email
    ]);
    
    if ($kullanicisor->rowCount() == 0) {
        // Email not found
        header("Location:../../forgot-password.php?durum=mailbulunamadi");
        exit;
    }
    
    // Generate token
    $token = bin2hex(random_bytes(32));
    $token_expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));
    
    // Remove any existing tokens for this email
    $deleteTokens = $db->prepare("DELETE FROM password_reset WHERE email = :email");
    $deleteTokens->execute(['email' => $email]);
    
    // Insert new token
    $tokenInsert = $db->prepare("INSERT INTO password_reset (email, token, token_expiry) VALUES (:email, :token, :token_expiry)");
    $tokenInsert->execute([
        'email' => $email,
        'token' => $token,
        'token_expiry' => $token_expiry
    ]);
    
    // Get site settings for email
    $ayarsor = $db->prepare("SELECT * FROM ayar WHERE ayar_id=:ayar_id");
    $ayarsor->execute([
        'ayar_id' => 0
    ]);
    $ayarcek = $ayarsor->fetch(PDO::FETCH_ASSOC);
    
    // Get user name
    $kullanici = $kullanicisor->fetch(PDO::FETCH_ASSOC);
    $user_name = $kullanici['kullanici_ad'] . " " . $kullanici['kullanici_soyad'];
    
    // Create reset link using ayar_url
    $reset_link = $ayarcek['ayar_url'] . '/reset-password.php?token=' . $token . '&email=' . urlencode($email);
    
    // Email subject and message
    $subject = "Şifre Sıfırlama Talebi - " . $ayarcek['ayar_title'];
    
    $message = '
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <title>Şifre Sıfırlama</title>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #ddd; border-radius: 5px; }
            .header { text-align: center; margin-bottom: 20px; }
            .logo { max-width: 200px; }
            .content { padding: 20px; background-color: #f9f9f9; border-radius: 5px; }
            .button { display: inline-block; padding: 10px 20px; background-color: #26B99A; color: white; text-decoration: none; border-radius: 4px; }
            .footer { margin-top: 20px; font-size: 12px; color: #777; text-align: center; }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="header">
                <img src="' . $ayarcek['ayar_logo'] . '" alt="' . $ayarcek['ayar_title'] . '" class="logo">
                <h2>Şifre Sıfırlama Talebi</h2>
            </div>
            
            <div class="content">
                <p>Merhaba ' . $user_name . ',</p>
                <p>Hesabınız için bir şifre sıfırlama talebinde bulundunuz. Aşağıdaki bağlantıya tıklayarak şifrenizi sıfırlayabilirsiniz:</p>
                
                <p style="text-align: center;">
                    <a href="' . $reset_link . '" class="button">Şifremi Sıfırla</a>
                </p>
                
                <p>Veya aşağıdaki bağlantıyı tarayıcınıza kopyalayabilirsiniz:</p>
                <p>' . $reset_link . '</p>
                
                <p>Bu bağlantı 1 saat boyunca geçerlidir.</p>
                
                <p>Eğer bu talebi siz yapmadıysanız, bu e-postayı görmezden gelebilirsiniz.</p>
            </div>
            
            <div class="footer">
                <p>Bu e-posta ' . $ayarcek['ayar_title'] . ' tarafından gönderilmiştir.</p>
                <p>' . $ayarcek['ayar_adres'] . '</p>
            </div>
        </div>
    </body>
    </html>';
        
    try {
        // Basic email headers
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        $headers .= 'From: ' . $ayarcek['ayar_title'] . ' <' . $ayarcek['ayar_mail'] . '>' . "\r\n";

        // Try sending with PHP mail() function first
        
        // If PHP mail() fails, try with PHPMailer as backup
        require_once '../../vendor/autoload.php';
        
        $mail = new PHPMailer\PHPMailer\PHPMailer(true);
        
        // Server settings
        $mail->SMTPDebug = 0;  // Disable debug output
        $mail->isSMTP();
        $mail->Host = $ayarcek['ayar_smtphost'];
        $mail->SMTPAuth = true;
        $mail->Username = $ayarcek['ayar_smtpuser'];
        $mail->Password = $ayarcek['ayar_smtppassword'];
        $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = $ayarcek['ayar_smtpport'];
        $mail->CharSet = 'UTF-8';

        // Recipients
        $mail->setFrom($ayarcek['ayar_smtpuser'], $ayarcek['ayar_title']);
        $mail->addAddress($email, $user_name);
        
        // Content
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $message;

        // Enable debug output temporarily
        $mail->SMTPDebug = 2;
        $mail->Debugoutput = function($str, $level) {
            error_log("SMTP Debug: $str");
        };

        // Common SMTP configurations based on popular providers
        if (strpos($ayarcek['ayar_smtphost'], 'gmail') !== false) {
            $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;
        } elseif (strpos($ayarcek['ayar_smtphost'], 'outlook') !== false || strpos($ayarcek['ayar_smtphost'], 'hotmail') !== false) {
            $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;
        } else {
            // Default to SSL if not specified
            $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port = 465;
        }

        try {
            $mail->send();
            header("Location:../../forgot-password.php?durum=mailgonderildi");
        } catch (Exception $e) {
            error_log("Mail Send Error: " . $mail->ErrorInfo);
            $_SESSION['error'] = "Mail gönderme hatası: " . $mail->ErrorInfo;
            header("Location:../../forgot-password.php?durum=mailhata");
        }
        exit;
    } catch (Exception $e) {
        // Log the error for debugging
        error_log("Mail Error: " . $e->getMessage());
        
        $_SESSION['error'] = "E-posta gönderilirken bir hata oluştu. Lütfen daha sonra tekrar deneyin.";
        header("Location:../../forgot-password.php?durum=mailhata");
    }
    exit;
}

// Reset Password
if (isset($_POST['reset_password'])) {
    $email = $_POST['email'];
    $token = $_POST['token'];
    $password = $_POST['password'];
    $password_confirm = $_POST['password_confirm'];
    
    // Validate token
    $tokenSor = $db->prepare("SELECT * FROM password_reset WHERE token = :token AND email = :email AND token_expiry > NOW()");
    $tokenSor->execute([
        'token' => $token,
        'email' => $email
    ]);
    
    if ($tokenSor->rowCount() == 0) {
        $_SESSION['error'] = "Geçersiz veya süresi dolmuş bağlantı. Lütfen tekrar şifre sıfırlama talebinde bulunun.";
        header("Location:../../forgot-password.php");
        exit;
    }
    
    // Check if passwords match
    if ($password !== $password_confirm) {
        $_SESSION['error'] = "Şifreler eşleşmiyor.";
        header("Location:../../reset-password.php?token=$token&email=$email");
        exit;
    }
    
    // Update password
    $passwordHash = md5($password);
    $updatePassword = $db->prepare("UPDATE kullanici SET kullanici_password = :password WHERE kullanici_mail = :email");
    $update = $updatePassword->execute([
        'password' => $passwordHash,
        'email' => $email
    ]);
    
    if ($update) {
        // Delete token after successful password reset
        $deleteToken = $db->prepare("DELETE FROM password_reset WHERE email = :email");
        $deleteToken->execute(['email' => $email]);
        
        $_SESSION['success'] = "Şifreniz başarıyla güncellenmiştir. Yeni şifrenizle giriş yapabilirsiniz.";
        header("Location:../../login.php");
    } else {
        $_SESSION['error'] = "Şifre güncellenirken bir hata oluştu. Lütfen tekrar deneyin.";
        header("Location:../../reset-password.php?token=$token&email=$email");
    }
    exit;
}

// KURS İÇERİK EKLEME İŞLEMLERİ

// 1. Bilgisayardan Video Yükleme
if(isset($_POST['bulk_lesson_add'])) {
    $kurs_id = $_POST['course_id'];
    $modul_id = $_POST['section_id']; // section_id aslında modul_id'dir
    
    $kurs_modul_sor = $db->prepare("SELECT km.modul_ad, k.baslik
                                    FROM kurs_modulleri km
                                    JOIN kurslar k ON k.kurs_id = km.kurs_id
                                    WHERE km.modul_id = ?");
    $kurs_modul_sor->execute([$modul_id]);
    $kurs_modul_cek = $kurs_modul_sor->fetch(PDO::FETCH_ASSOC);
    $courseName = $kurs_modul_cek['baslik'];
    $modulName = $kurs_modul_cek['modul_ad'];

    // Hata kontrolü
    if(!isset($_FILES['videos']) || empty($_FILES['videos']['name'][0])) {
        header("Location:../production/kurs-icerik-duzenle.php?kurs_id=" . $kurs_id . "&durum=no&mesaj=Video seçilmedi");
        exit;
    }
    
    // Get course name for folder organization
    try {
        // Get course name
        
        // Sanitize name for folder structure (remove special chars that might cause issues)
        
        $titles = $_POST['titles'] ?? [];
        $descriptions = $_POST['descriptions'] ?? [];
        $durations = $_POST['durations'] ?? [];
        
        // Include the BunnyStream class
        require_once '../../includes/classes/BunnyStream.php';
        require_once '../../includes/config.php';
        
        // Initialize BunnyStream
        $bunnyStream = new BunnyStream(
            BUNNY_STREAM_API_KEY,
            BUNNY_STREAM_LIBRARY_ID,
            BUNNY_STREAM_HOSTNAME
        );
        
        $db->beginTransaction();
        
        // Her video için işlem yap
        foreach ($_FILES['videos']['tmp_name'] as $key => $tmp_name) {
            if ($_FILES['videos']['error'][$key] == 0) {
                // Video boyut kontrolü (100MB)
                if ($_FILES['videos']['size'][$key] > 100 * 1024 * 1024) {
                    throw new Exception("Video boyutu çok büyük! Maksimum 100MB olabilir.");
                }
                
                // Format kontrolü
                $file_extension = strtolower(pathinfo($_FILES['videos']['name'][$key], PATHINFO_EXTENSION));
                if ($file_extension !== 'mp4') {
                    throw new Exception("Sadece MP4 formatı desteklenmektedir!");
                }
                
                $title = $titles[$key] ?? pathinfo($_FILES['videos']['name'][$key], PATHINFO_FILENAME);
                $description = $descriptions[$key] ?? '';
                $duration = $durations[$key] ?? 0;
                
                
                // Include module name in video title for better organization
                $fullTitle = !empty($modulName) ? "[{$modulName}] {$title}" : $title;
                
                // Upload to Bunny Stream in course collection
                $videoData = $bunnyStream->uploadToCourseCollection(
                    $tmp_name,
                    $fullTitle,
                    $courseName
                );
                
                if (!$videoData || !isset($videoData['guid'])) {
                    throw new Exception("Video Bunny Stream'e yüklenirken hata oluştu.");
                }
                
                // Store the video GUID
                $videoGuid = $videoData['guid'];
                
                // Get duration from uploaded video if not provided
                if ($duration === 0 && isset($videoData['length'])) {
                    $duration = $videoData['length'];
                }
                
                // Süreyi saat ve dakikaya çevir
                $sure_saat = floor($duration / 3600);
                $sure_dakika = floor(($duration % 3600) / 60);
                
                // Sıradaki mevcut son ders sırasını bul
                $sirasor = $db->prepare("SELECT MAX(bolum_sira) as max_sira FROM kurs_bolumleri WHERE modul_id = ?");
                $sirasor->execute([$modul_id]);
                $siracek = $sirasor->fetch(PDO::FETCH_ASSOC);
                $bolum_sira = ($siracek['max_sira'] ?? 0) + 1;
                
                // Generate embed code
                $embed_code = $bunnyStream->getEmbedCode($videoGuid);
                
                // Veritabanına kaydet
                $kaydet = $db->prepare("INSERT INTO kurs_bolumleri SET
                    modul_id = ?,
                    bolum_ad = ?,
                    bolum_sira = ?,
                    bolum_sure_saat = ?,
                    bolum_sure_dakika = ?,
                    icerik_tipi = ?,
                    video_url = ?,
                    embed_kodu = ?");
                
                $insert = $kaydet->execute([
                    $modul_id,
                    $title,
                    $bolum_sira,
                    $sure_saat,
                    $sure_dakika,
                    'bunnystream',
                    $videoGuid,
                    $embed_code
                ]);
                
                if(!$insert) {
                    throw new Exception("Ders kaydedilirken bir hata oluştu.");
                }
            }
        }
        
        $db->commit();
        header("Location:../production/kurs-icerik-duzenle.php?kurs_id=" . $kurs_id . "&durum=ok");
        exit;
        
    } catch(Exception $e) {
        if($db->inTransaction()) {
            $db->rollBack();
        }
        error_log("Bunny Stream upload error: " . $e->getMessage());
        header("Location:../production/kurs-icerik-duzenle.php?kurs_id=" . $kurs_id . "&durum=no&mesaj=" . urlencode($e->getMessage()));
        exit;
    }
}

// 2. YouTube Video İçe Aktarma
if(isset($_POST['youtube_import'])) {
    try {
        $kurs_id = $_POST['kurs_id'];
        $modul_id = $_POST['bolum_id']; // aslında modul_id
        $title = $_POST['title'];
        $description = $_POST['description'] ?? '';
        $video_id = $_POST['video_id'];
        $duration = isset($_POST['duration']) ? intval($_POST['duration']) : 0;
        
        if(empty($title) || empty($video_id)) {
            throw new Exception("Başlık ve video ID bilgileri gereklidir.");
        }
        
        $db->beginTransaction();
        
        // Sıradaki mevcut son ders sırasını bul
        $sirasor = $db->prepare("SELECT MAX(bolum_sira) as max_sira FROM kurs_bolumleri WHERE modul_id = ?");
        $sirasor->execute([$modul_id]);
        $siracek = $sirasor->fetch(PDO::FETCH_ASSOC);
        $bolum_sira = ($siracek['max_sira'] ?? 0) + 1;
        
        // Süreyi saat ve dakikaya çevir
        $sure_saat = floor($duration / 3600);
        $sure_dakika = floor(($duration % 3600) / 60);
        
        // Veritabanına kaydet
        $kaydet = $db->prepare("INSERT INTO kurs_bolumleri SET
            modul_id = ?,
            bolum_ad = ?,
            bolum_sira = ?,
            bolum_sure_saat = ?,
            bolum_sure_dakika = ?,
            icerik_tipi = ?,
            video_url = ?");
        
        $insert = $kaydet->execute([
            $modul_id,
            $title,
            $bolum_sira,
            $sure_saat,
            $sure_dakika,
            'youtube',
            $video_id
        ]);
        
        if(!$insert) {
            throw new Exception("Ders kaydedilirken bir hata oluştu.");
        }
        
        $db->commit();
        
        // AJAX isteği için JSON yanıtı döndür
        echo json_encode(['success' => true, 'message' => 'YouTube videosu başarıyla eklendi.']);
        exit;
        
    } catch(Exception $e) {
        if($db->inTransaction()) {
            $db->rollBack();
        }
        
        // AJAX isteği için JSON hata yanıtı döndür
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        exit;
    }
}

// 3. Vimeo Video İçe Aktarma
if(isset($_POST['vimeo_import'])) {
    try {
        $kurs_id = $_POST['kurs_id'];
        $modul_id = $_POST['bolum_id']; // aslında modul_id
        $title = $_POST['title'];
        $description = $_POST['description'] ?? '';
        $video_id = $_POST['video_id'];
        
        // Duration fixing - ensure it's properly converted to integer
        $duration = 0;
        if(isset($_POST['duration']) && !empty($_POST['duration'])) {
            $duration = intval($_POST['duration']);
            
            // Log the duration for debugging
            error_log("Vimeo duration from form: " . $_POST['duration']);
            error_log("Converted duration: " . $duration);
        }
        
        if(empty($title) || empty($video_id)) {
            throw new Exception("Başlık ve video ID bilgileri gereklidir.");
        }
        
        $db->beginTransaction();
        
        // Sıradaki mevcut son ders sırasını bul
        $sirasor = $db->prepare("SELECT MAX(bolum_sira) as max_sira FROM kurs_bolumleri WHERE modul_id = ?");
        $sirasor->execute([$modul_id]);
        $siracek = $sirasor->fetch(PDO::FETCH_ASSOC);
        $bolum_sira = ($siracek['max_sira'] ?? 0) + 1;
        
        // Süreyi saat ve dakikaya çevir
        $sure_saat = floor($duration / 3600);
        $sure_dakika = floor(($duration % 3600) / 60);
        
        // Log the converted times
        error_log("Vimeo süre (saat): " . $sure_saat);
        error_log("Vimeo süre (dakika): " . $sure_dakika);
        
        // Veritabanına kaydet
        $kaydet = $db->prepare("INSERT INTO kurs_bolumleri SET
            modul_id = ?,
            bolum_ad = ?,
            bolum_sira = ?,
            bolum_sure_saat = ?,
            bolum_sure_dakika = ?,
            icerik_tipi = ?,
            video_url = ?");
        
        $insert = $kaydet->execute([
            $modul_id,
            $title,
            $bolum_sira,
            $sure_saat,
            $sure_dakika,
            'vimeo',
            $video_id
        ]);
        
        if(!$insert) {
            throw new Exception("Ders kaydedilirken bir hata oluştu.");
        }
        
        $db->commit();
        
        // AJAX isteği için JSON yanıtı döndür
        echo json_encode(['success' => true, 'message' => 'Vimeo videosu başarıyla eklendi.']);
        exit;
        
    } catch(Exception $e) {
        if($db->inTransaction()) {
            $db->rollBack();
        }
        
        // Hata mesajını logla
        error_log("Vimeo import error: " . $e->getMessage());
        
        // AJAX isteği için JSON hata yanıtı döndür
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        exit;
    }
}

// 4. URL ile İçerik Ekleme
if(isset($_POST['url_upload'])) {
    try {
        $kurs_id = $_POST['kurs_id'];
        $modul_id = $_POST['bolum_id']; // aslında modul_id
        $title = $_POST['title'];
        $url = $_POST['url'];
        $description = $_POST['description'] ?? '';
        $duration_hour = intval($_POST['duration_hour'] ?? 0);
        $duration_minute = intval($_POST['duration_minute'] ?? 0);
        $is_preview = isset($_POST['is_preview']) ? 1 : 0;
        
        // Temel doğrulamalar
        if(empty($title)) {
            throw new Exception("İçerik başlığı gereklidir.");
        }
        
        if(empty($url)) {
            throw new Exception("İçerik URL'si gereklidir.");
        }
        
        // URL formatını doğrula
        if(!filter_var($url, FILTER_VALIDATE_URL)) {
            throw new Exception("Geçerli bir URL giriniz.");
        }
        
        $db->beginTransaction();
        
        // Sıradaki mevcut son ders sırasını bul
        $sirasor = $db->prepare("SELECT MAX(bolum_sira) as max_sira FROM kurs_bolumleri WHERE modul_id = ?");
        $sirasor->execute([$modul_id]);
        $siracek = $sirasor->fetch(PDO::FETCH_ASSOC);
        $bolum_sira = ($siracek['max_sira'] ?? 0) + 1;
        
        // Veritabanına kaydet
        $kaydet = $db->prepare("INSERT INTO kurs_bolumleri SET
            modul_id = ?,
            bolum_ad = ?,
            bolum_sira = ?,
            bolum_sure_saat = ?,
            bolum_sure_dakika = ?,
            icerik_tipi = ?,
            video_url = ?,
            is_preview = ?");
        
        $insert = $kaydet->execute([
            $modul_id,
            $title,
            $bolum_sira,
            $duration_hour,
            $duration_minute,
            'url',
            $url,
            $is_preview
        ]);
        
        if(!$insert) {
            throw new Exception("İçerik kaydedilirken bir hata oluştu.");
        }
        
        $db->commit();
        
        // AJAX isteği için JSON yanıtı döndür
        echo json_encode(['success' => true, 'message' => 'URL içeriği başarıyla eklendi.']);
        exit;
        
    } catch(Exception $e) {
        if($db->inTransaction()) {
            $db->rollBack();
        }
        
        // AJAX isteği için JSON hata yanıtı döndür
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        exit;
    }
}

// 5. H5P İçeriği Yükleme
/*if(isset($_POST['h5p_upload'])) {
    try {
        $kurs_id = $_POST['kurs_id'];
        $modul_id = $_POST['bolum_id']; // aslında modul_id
        $title = $_POST['title'];
        $h5p_url = $_POST['h5p_url'];
        
        if(empty($title) || empty($h5p_url)) {
            throw new Exception("Başlık ve H5P içerik URL bilgileri gereklidir.");
        }
        
        $db->beginTransaction();
        
        // Sıradaki mevcut son ders sırasını bul
        $sirasor = $db->prepare("SELECT MAX(bolum_sira) as max_sira FROM kurs_bolumleri WHERE modul_id = ?");
        $sirasor->execute([$modul_id]);
        $siracek = $sirasor->fetch(PDO::FETCH_ASSOC);
        $bolum_sira = ($siracek['max_sira'] ?? 0) + 1;
        
        // Veritabanına kaydet
        $kaydet = $db->prepare("INSERT INTO kurs_bolumleri SET
            modul_id = ?,
            bolum_ad = ?,
            bolum_sira = ?,
            bolum_sure_saat = ?,
            bolum_sure_dakika = ?,
            icerik_tipi = ?,
            video_url = ?");
        
        $insert = $kaydet->execute([
            $modul_id,
            $title,
            $bolum_sira,
            0, // Süre bilgisi yok
            0, // Süre bilgisi yok
            'h5p',
            $h5p_url
        ]);
        
        if(!$insert) {
            throw new Exception("İçerik kaydedilirken bir hata oluştu.");
        }
        
        $db->commit();
        
        // AJAX isteği için JSON yanıtı döndür
        echo json_encode(['success' => true, 'message' => 'H5P içeriği başarıyla eklendi.']);
        exit;
        
    } catch(Exception $e) {
        if($db->inTransaction()) {
            $db->rollBack();
        }
        
        // AJAX isteği için JSON hata yanıtı döndür
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        exit;
    }
}*/

// 6. SCORM Paketi Yükleme
if(isset($_POST['scorm_upload'])) {
    try {
        // Get form data
        $kurs_id = $_POST['kurs_id'] ?? null;
        $bolum_id = $_POST['bolum_id'] ?? null;
        $title = $_POST['title'] ?? '';
        $duration = intval($_POST['duration'] ?? 30);
        
        // Validate required fields
        if(empty($title)) {
            throw new Exception("Başlık bilgisi gereklidir.");
        }
        
        if(empty($bolum_id)) {
            throw new Exception("Modül seçilmedi!");
        }
        
        if(!isset($_FILES['scorm_file']) || $_FILES['scorm_file']['error'] != 0) {
            throw new Exception("SCORM dosyası yükleme hatası!");
        }
        
        // Validate file is a ZIP
        $scorm_file = $_FILES['scorm_file'];
        $file_ext = strtolower(pathinfo($scorm_file['name'], PATHINFO_EXTENSION));
        
        if($file_ext != 'zip') {
            throw new Exception("Geçersiz dosya formatı! Sadece .zip uzantılı SCORM paketleri kabul edilir.");
        }
        
        $db->beginTransaction();
        
        // Get next lesson order
        $sirasor = $db->prepare("SELECT MAX(bolum_sira) as max_sira FROM kurs_bolumleri WHERE modul_id = ?");
        $sirasor->execute([$bolum_id]);
        $siracek = $sirasor->fetch(PDO::FETCH_ASSOC);
        $bolum_sira = ($siracek['max_sira'] ?? 0) + 1;
        
        // Create unique folder name for the SCORM package
        $folder_name = 'scorm_' . time() . '_' . uniqid();
        $upload_dir = '../../uploads/scorm/' . $folder_name;
        
        // Create directory if it doesn't exist
        if(!file_exists('../../uploads/scorm/')) {
            mkdir('../../uploads/scorm/', 0755, true);
        }
        
        // Create folder for this SCORM package
        if(!mkdir($upload_dir, 0755, true)) {
            throw new Exception("Dizin oluşturulamadı. Lütfen yetkileri kontrol edin.");
        }
        
        // Upload the zip file
        $zip_path = $upload_dir . '/' . basename($scorm_file['name']);
        if(!move_uploaded_file($scorm_file['tmp_name'], $zip_path)) {
            throw new Exception("Dosya yükleme hatası!");
        }
        
        // Extract the zip file
        $zip = new ZipArchive;

        if($zip->open($zip_path) === TRUE) {
            
            $zip->extractTo($upload_dir);
            $zip->close();
            
            // Delete the original zip file to save space
            unlink($zip_path);
            
            // Find index.html or imsmanifest.xml - common SCORM entry points
            $entry_file = '';
            if(file_exists($upload_dir . '/index.html')) {
                $entry_file = 'index.html';
            } elseif(file_exists($upload_dir . '/imsmanifest.xml')) {
                // Find the first HTML file referenced in the manifest
                $manifest = simplexml_load_file($upload_dir . '/imsmanifest.xml');
                if($manifest) {
                    $namespace = $manifest->getNamespaces(true);
                    $resources = $manifest->resources->resource ?? $manifest->resource;
                    if($resources && count($resources) > 0) {
                        foreach($resources as $resource) {
                            $href = (string)$resource['href'];
                            if(!empty($href) && (strtolower(pathinfo($href, PATHINFO_EXTENSION)) == 'html' || 
                                strtolower(pathinfo($href, PATHINFO_EXTENSION)) == 'htm')) {
                                $entry_file = $href;
                                break;
                            }
                        }
                    }
                }
                
                // If no entry file found, set to imsmanifest.xml
                if(empty($entry_file)) {
                    $entry_file = 'imsmanifest.xml';
                }
            } else {
                // Look for any HTML file
                $files = glob($upload_dir . '/*.{html,htm}', GLOB_BRACE);
                if(!empty($files)) {
                    $entry_file = basename($files[0]);
                } else {
                    throw new Exception("SCORM paketinde giriş dosyası bulunamadı!");
                }
            }
            
            // Create URL path for the entry file
            $scorm_url = '/uploads/scorm/' . $folder_name . '/' . $entry_file;
            
            // Calculate hours and minutes
            $sure_saat = floor($duration / 60);
            $sure_dakika = $duration % 60;
            
            // Create embed code
            $embed_code = '<iframe src="' . $scorm_url . '" width="100%" height="600" frameborder="0" allowfullscreen="allowfullscreen"></iframe>';
            
            // Save to database
            $kaydet = $db->prepare("INSERT INTO kurs_bolumleri SET
                modul_id = ?,
                bolum_ad = ?,
                bolum_sira = ?,
                bolum_sure_saat = ?,
                bolum_sure_dakika = ?,
                icerik_tipi = ?,
                video_url = ?,
                embed_kodu = ?,
                is_preview = ?");
            
            $insert = $kaydet->execute([
                $bolum_id,
                $title,
                $bolum_sira,
                $sure_saat,
                $sure_dakika,
                'scorm',
                $scorm_url,
                $embed_code,
                isset($_POST['is_preview']) ? 1 : 0
            ]);
            
            if(!$insert) {
                throw new Exception("İçerik kaydedilirken bir hata oluştu.");
            }
            
            $db->commit();
            
            // Redirect to course content page
            header("Location: ../production/kurs-icerik-duzenle.php?kurs_id=$kurs_id&durum=ok");
            exit;
            
        } else {
            throw new Exception("ZIP dosyası açılamadı!");
        }
        
    } catch(Exception $e) {
        if(isset($db) && $db->inTransaction()) {
            $db->rollBack();
        }
        
        // Clean up any created directories if there was an error
        if(isset($upload_dir) && file_exists($upload_dir)) {
            // Recursive function to delete directory and its contents
            function rrmdir($dir) {
                if(is_dir($dir)) {
                    $objects = scandir($dir);
                    foreach($objects as $object) {
                        if($object != "." && $object != "..") {
                            if(is_dir($dir . "/" . $object))
                                rrmdir($dir . "/" . $object);
                            else
                                unlink($dir . "/" . $object);
                        }
                    }
                    rmdir($dir);
                }
            }
            rrmdir($upload_dir);
        }
        
        header("Location: ../production/kurs-icerik-duzenle.php?kurs_id=$kurs_id&durum=no&hata=" . urlencode($e->getMessage()));
        exit;
    }
}

// 7. Embed Kodu ile İçerik Ekleme
if(isset($_POST['embed_upload'])) {
    try {
        $kurs_id = $_POST['kurs_id'];
        $modul_id = $_POST['bolum_id']; // aslında modul_id
        $title = $_POST['title'];
        $embed_code = $_POST['embed_code'];
        
        if(empty($title) || empty($embed_code)) {
            throw new Exception("Başlık ve embed kodu gereklidir.");
        }
        
        $db->beginTransaction();
        
        // Sıradaki mevcut son ders sırasını bul
        $sirasor = $db->prepare("SELECT MAX(bolum_sira) as max_sira FROM kurs_bolumleri WHERE modul_id = ?");
        $sirasor->execute([$modul_id]);
        $siracek = $sirasor->fetch(PDO::FETCH_ASSOC);
        $bolum_sira = ($siracek['max_sira'] ?? 0) + 1;
        
        // Veritabanına kaydet
        $kaydet = $db->prepare("INSERT INTO kurs_bolumleri SET
            modul_id = ?,
            bolum_ad = ?,
            bolum_sira = ?,
            bolum_sure_saat = ?,
            bolum_sure_dakika = ?,
            icerik_tipi = ?,
            embed_kodu = ?");
        
        $insert = $kaydet->execute([
            $modul_id,
            $title,
            $bolum_sira,
            0, // Süre bilgisi yok
            0, // Süre bilgisi yok
            'embed',
            $embed_code
        ]);
        
        if(!$insert) {
            throw new Exception("İçerik kaydedilirken bir hata oluştu.");
        }
        
        $db->commit();
        
        // AJAX isteği için JSON yanıtı döndür
        echo json_encode(['success' => true, 'message' => 'Embed içeriği başarıyla eklendi.']);
        exit;
        
    } catch(Exception $e) {
        if($db->inTransaction()) {
            $db->rollBack();
        }
        
        // AJAX isteği için JSON hata yanıtı döndür
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        exit;
    }
}

// 8. Bunny CDN'den Video Seçme
if(isset($_POST['bunny_upload'])) {
    try {
        $kurs_id = $_POST['kurs_id'];
        $modul_id = $_POST['bolum_id']; // aslında modul_id
        $title = $_POST['title'];
        $video_id = $_POST['video_id'];
        
        if(empty($title) || empty($video_id)) {
            throw new Exception("Başlık ve video ID bilgileri gereklidir.");
        }
        
        $db->beginTransaction();
        
        // Sıradaki mevcut son ders sırasını bul
        $sirasor = $db->prepare("SELECT MAX(bolum_sira) as max_sira FROM kurs_bolumleri WHERE modul_id = ?");
        $sirasor->execute([$modul_id]);
        $siracek = $sirasor->fetch(PDO::FETCH_ASSOC);
        $bolum_sira = ($siracek['max_sira'] ?? 0) + 1;
        
        // Veritabanına kaydet
        $kaydet = $db->prepare("INSERT INTO kurs_bolumleri SET
            modul_id = ?,
            bolum_ad = ?,
            bolum_sira = ?,
            bolum_sure_saat = ?,
            bolum_sure_dakika = ?,
            icerik_tipi = ?,
            video_url = ?");
        
        $insert = $kaydet->execute([
            $modul_id,
            $title,
            $bolum_sira,
            0, // Süre bilgisi yok
            0, // Süre bilgisi yok
            'video', // Bunny CDN'deki videoları normal video gibi işaretleyebiliriz
            $video_id
        ]);
        
        if(!$insert) {
            throw new Exception("İçerik kaydedilirken bir hata oluştu.");
        }
        
        $db->commit();
        
        // AJAX isteği için JSON yanıtı döndür
        echo json_encode(['success' => true, 'message' => 'Video başarıyla eklendi.']);
        exit;
        
    } catch(Exception $e) {
        if($db->inTransaction()) {
            $db->rollBack();
        }
        
        // AJAX isteği için JSON hata yanıtı döndür
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        exit;
    }
}

// Ders Düzenleme İşlemi
if(isset($_POST['bolum_duzenle'])) {
    try {
        $bolum_id = $_POST['bolum_id'];
        $bolum_ad = $_POST['bolum_ad'];
        $bolum_sira = $_POST['bolum_sira'];
        $bolum_sure_saat = $_POST['bolum_sure_saat'];
        $bolum_sure_dakika = $_POST['bolum_sure_dakika'];
        $kurs_id = $_POST['kurs_id'];
        
        if(empty($bolum_ad) || empty($bolum_sira)) {
            header("Location:../production/kurs-icerik-duzenle.php?kurs_id=" . $kurs_id . "&durum=no&mesaj=Ders adı ve sıra bilgisi zorunludur");
            exit;
        }
        
        $db->beginTransaction();
        
        $update = $db->prepare("UPDATE kurs_bolumleri SET
            bolum_ad = ?,
            bolum_sira = ?,
            bolum_sure_saat = ?,
            bolum_sure_dakika = ?
            WHERE bolum_id = ?");
            
        $result = $update->execute([
            $bolum_ad,
            $bolum_sira,
            $bolum_sure_saat,
            $bolum_sure_dakika,
            $bolum_id
        ]);
        
        if(!$result) {
            throw new Exception("Ders güncellenirken bir hata oluştu.");
        }
        
        $db->commit();
        header("Location:../production/kurs-icerik-duzenle.php?kurs_id=" . $kurs_id . "&durum=ok");
        exit;
        
    } catch(Exception $e) {
        if($db->inTransaction()) {
            $db->rollBack();
        }
        header("Location:../production/kurs-icerik-duzenle.php?kurs_id=" . $_POST['kurs_id'] . "&durum=no&mesaj=" . urlencode($e->getMessage()));
        exit;
    }
}

// Ders Silme İşlemi
if (isset($_GET['bolum_sil'])) {
    $bolum_id = $_GET['bolum_id'];
    $fetch_kurs_id = $db->prepare("SELECT k.kurs_id
                                    FROM kurs_bolumleri kb
                                    JOIN kurs_modulleri km ON km.modul_id = kb.modul_id
                                    JOIN kurslar k ON k.kurs_id = km.kurs_id
                                    WHERE kb.bolum_id = ?");
    $fetch_kurs_id->execute([$bolum_id]);
    $kurs = $fetch_kurs_id->fetch(PDO::FETCH_ASSOC);
    $kurs_id = $kurs['kurs_id'];
    try {
        // First check if this is a Bunny Stream video, H5P content, or SCORM package
        $videosor = $db->prepare("SELECT icerik_tipi, video_url FROM kurs_bolumleri WHERE bolum_id = ?");
        $videosor->execute([$bolum_id]);
        $videocek = $videosor->fetch(PDO::FETCH_ASSOC);
        
        // If it's a Bunny Stream video, delete it from the platform
        if ($videocek && $videocek['icerik_tipi'] == 'bunnystream' && !empty($videocek['video_url'])) {
            // Include BunnyStream class and config
            require_once '../../includes/classes/BunnyStream.php';
            require_once '../../includes/config.php';
            
            // Initialize BunnyStream with API credentials
            $bunnyStream = new BunnyStream(
                BUNNY_STREAM_API_KEY,
                BUNNY_STREAM_LIBRARY_ID,
                BUNNY_STREAM_HOSTNAME
            );
            
            // Delete the video from Bunny Stream
            $deleteResult = $bunnyStream->deleteVideo($videocek['video_url']);
            if (!$deleteResult) {
                error_log("Warning: Failed to delete video from Bunny Stream: " . $videocek['video_url']);
                // Continue with database deletion even if Bunny Stream deletion fails
            }
        }
        
        // If it's an H5P content, delete the file from server
        if ($videocek && $videocek['icerik_tipi'] == 'h5p' && !empty($videocek['video_url'])) {
            $file_path = $_SERVER['DOCUMENT_ROOT'] . $videocek['video_url'];
            $file_path = "../../" . $videocek['video_url'];
            // Delete the file if it exists
            if (file_exists($file_path)) {
                unlink($file_path);
                error_log("Deleted H5P file: " . $videocek['video_url']);
            } else {
                error_log("H5P file not found: " . $file_path);
            }
        }
        
        // If it's a SCORM package, delete the directory
        if ($videocek && $videocek['icerik_tipi'] == 'scorm' && !empty($videocek['video_url'])) {
            // Extract folder path from the URL more reliably
            $video_url = $videocek['video_url'];
            
            // Get folder path excluding the file
            $url_parts = explode('/', $video_url);
            array_pop($url_parts); // Remove file name
            $relative_folder = implode('/', $url_parts);
            
            // Extract the SCORM folder name - it should follow a pattern like "scorm_timestamp_uniqueid"
            $scorm_folder_pattern = 'scorm_\d+_[a-f0-9]+';
            $matches = [];
            if (preg_match('/' . $scorm_folder_pattern . '/', $video_url, $matches)) {
                $scorm_folder = $matches[0];
                error_log("Detected SCORM folder name: " . $scorm_folder);
            } else {
                error_log("Could not detect SCORM folder pattern in URL: " . $video_url);
                $scorm_folder = '';
            }
            
            // Get document root and script path
            $doc_root = $_SERVER['DOCUMENT_ROOT'];
            $script_path = dirname($_SERVER['SCRIPT_FILENAME']);
            
            // Attempt to find the actual path to the uploads directory by looking at our script location
            $script_to_root = str_replace($doc_root, '', $script_path);
            $parts = explode('/', trim($script_to_root, '/'));
            
            // Detect badiakademi-lms from the script path
            $base_app = '';
            foreach ($parts as $part) {
                if (strpos($part, 'badiakademi') !== false) {
                    $base_app = $part;
                    break;
                }
            }
            
            error_log("Base application folder detected: " . ($base_app ?: "None"));
            
            // Now try multiple path possibilities
            $possible_paths = [
                // Try with the detected base application folder
                $doc_root . '/' . $base_app . '/uploads/scorm/' . $scorm_folder,
                
                // Try with fully qualified path using the original URL structure
                $doc_root . '/badiakademi-lms/badiakademi/uploads/scorm/' . $scorm_folder,
                
                // Try direct path from document root
                $doc_root . '/uploads/scorm/' . $scorm_folder,
                
                // Try with just badiakademi
                $doc_root . '/badiakademi/uploads/scorm/' . $scorm_folder,
                
                // Try with document root and relative folder
                $doc_root . $relative_folder,
                
                // Try parent directory (for subdirectory installations)
                dirname($doc_root) . '/badiakademi/uploads/scorm/' . $scorm_folder,
                
                // Try with xampp/htdocs standard path
                'C:/xampp/htdocs/badiakademi-lms/badiakademi/uploads/scorm/' . $scorm_folder,
            ];
            
            // Make sure rrmdir function is defined only once
            if (!function_exists('rrmdir')) {
                // Recursive function to delete directory and its contents
                function rrmdir($dir) {
                    if (is_dir($dir)) {
                        $objects = scandir($dir);
                        foreach ($objects as $object) {
                            if ($object != "." && $object != "..") {
                                if (is_dir($dir . "/" . $object))
                                    rrmdir($dir . "/" . $object);
                                else
                                    unlink($dir . "/" . $object);
                            }
                        }
                        rmdir($dir);
                        return true;
                    }
                    return false;
                }
            }
            
            // Try to use realpath to resolve actual directory
            $realpath_tried = false;
            foreach ($possible_paths as $index => $test_path) {
                if (!$realpath_tried) {
                    // First try with realpath to make sure we have correct slashes
                    $real_test_path = str_replace('\\', '/', realpath($test_path));
                    if ($real_test_path !== false) {
                        $possible_paths[] = $real_test_path;
                        $realpath_tried = true;
                    }
                }
            }
            
            $deleted = false;
            
            // Try each possible path
            foreach ($possible_paths as $path) {
                $path = str_replace('\\', '/', $path);
                error_log("Trying path: " . $path);
                
                if (file_exists($path)) {
                    error_log("Found SCORM directory at: " . $path);
                    $delete_result = rrmdir($path);
                    error_log("Deleted SCORM directory: " . $path . " Result: " . ($delete_result ? "Success" : "Failed"));
                    $deleted = true;
                    break;
                }
            }
            
            if (!$deleted) {
                error_log("Could not find SCORM directory to delete. URL was: " . $video_url);
            }
        }

        if ($videocek && ($videocek['icerik_tipi'] == 'pdf' || $videocek['icerik_tipi'] == 'presentation') && !empty($videocek['video_url'])) {
            $file_path = $_SERVER['DOCUMENT_ROOT'] . $videocek['video_url'];
            
            // Delete the file if it exists
            if (file_exists($file_path)) {
                unlink($file_path);
                error_log("Deleted file: " . $videocek['video_url']);
            } else {
                // Try with different root path
                $alt_path = dirname($_SERVER['DOCUMENT_ROOT']) . '/badiakademi' . $videocek['video_url'];
                if (file_exists($alt_path)) {
                    unlink($alt_path);
                    error_log("Deleted file (alternative path): " . $alt_path);
                } else {
                    error_log("File not found: " . $file_path);
                }
            }
        }
        
        // Now delete from database
        $sil = $db->prepare("DELETE FROM kurs_bolumleri WHERE bolum_id = ?");
        $kontrol = $sil->execute([$bolum_id]);
        
        if ($kontrol) {
            header("Location:../production/kurs-icerik-duzenle.php?kurs_id=" . $kurs_id . "&durum=ok");
            exit;
        } else {
            header("Location:../production/kurs-icerik-duzenle.php?kurs_id=" . $kurs_id . "&durum=no");
            exit;
        }
        
    } catch (Exception $e) {
        error_log("Error deleting lesson: " . $e->getMessage());
        header("Location:../production/kurs-icerik-duzenle.php?kurs_id=" . $kurs_id . "&durum=no");
        exit;
    }
}

// YouTube Video Bilgilerini Getirme
if(isset($_GET['action']) && $_GET['action'] == 'get_youtube_info') {
    // YouTube API anahtarı - BURAYA KENDİ API ANAHTARINIZI EKLEYİN
    $api_key = "AIzaSyDhPUIePpZe4nDd7EkY_qn7Ler1HU0uSk8";
    $video_id = "";
    
    // URL veya video ID kontrolü
    if(isset($_GET['url'])) {
        $url = $_GET['url'];
        
        // YouTube URL formatlarını kontrol et ve video_id'yi çıkar
        if(preg_match('/youtube\.com\/watch\?v=([^&]+)/', $url, $matches)) {
            $video_id = $matches[1];
        } else if(preg_match('/youtu\.be\/([^&]+)/', $url, $matches)) {
            $video_id = $matches[1];
        } else if(preg_match('/youtube\.com\/embed\/([^&]+)/', $url, $matches)) {
            $video_id = $matches[1];
        } else if(preg_match('/^[a-zA-Z0-9_-]{11}$/', $url)) {
            // Doğrudan video ID girildiyse
            $video_id = $url;
        }
    }
    
    if(empty($video_id)) {
        echo json_encode([
            'success' => false,
            'error' => 'Geçerli bir YouTube video URL\'si veya ID\'si giriniz.'
        ]);
        exit;
    }
    
    try {
        // YouTube API'den video bilgilerini al
        $api_url = "https://www.googleapis.com/youtube/v3/videos?id={$video_id}&key={$api_key}&part=snippet,contentDetails,statistics";
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $api_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $api_response = curl_exec($ch);
        
        if(curl_errno($ch)) {
            throw new Exception('cURL Error: ' . curl_error($ch));
        }
        
        curl_close($ch);
        
        $data = json_decode($api_response, true);
        
        if(empty($data['items'])) {
            throw new Exception('Video bulunamadı. Lütfen URL\'yi kontrol edin.');
        }
        
        $video_data = $data['items'][0];
        $snippet = $video_data['snippet'];
        $content_details = $video_data['contentDetails'];
        
        // ISO 8601 formatındaki süreyi dakika:saniye formatına çevir
        $duration = $content_details['duration']; // PT1M30S formatında
        $minutes = 0;
        $seconds = 0;
        
        if(preg_match('/PT((\d+)H)?((\d+)M)?((\d+)S)?/', $duration, $matches)) {
            $hours = (isset($matches[2]) && !empty($matches[2])) ? (int)$matches[2] : 0;
            $minutes = (isset($matches[4]) && !empty($matches[4])) ? (int)$matches[4] : 0;
            $seconds = (isset($matches[6]) && !empty($matches[6])) ? (int)$matches[6] : 0;
            
            $minutes += $hours * 60; // Saatleri dakikaya çevir
        }
        
        $duration_formatted = sprintf("%d:%02d", $minutes, $seconds);
        
        // Toplam saniye olarak süre
        $total_seconds = $minutes * 60 + $seconds;
        
        // Video bilgilerini döndür
        echo json_encode([
            'success' => true,
            'video' => [
                'id' => $video_id,
                'title' => $snippet['title'],
                'description' => $snippet['description'],
                'thumbnail' => $snippet['thumbnails']['high']['url'],
                'channel_title' => $snippet['channelTitle'],
                'duration' => $duration_formatted,
                'total_seconds' => $total_seconds,
                'published_at' => $snippet['publishedAt']
            ]
        ]);
        
    } catch(Exception $e) {
        echo json_encode([
            'success' => false,
            'error' => $e->getMessage()
        ]);
    }
    
    exit;
}

// Vimeo Video Bilgilerini Getirme
if(isset($_GET['action']) && $_GET['action'] == 'get_vimeo_info') {
    $video_id = "";
    
    // URL veya video ID kontrolü
    if(isset($_GET['url'])) {
        $url = $_GET['url'];
        
        // Vimeo URL formatlarını kontrol et ve video_id'yi çıkar
        if(preg_match('/vimeo\.com\/([0-9]+)/', $url, $matches)) {
            $video_id = $matches[1];
        } else if(preg_match('/player\.vimeo\.com\/video\/([0-9]+)/', $url, $matches)) {
            $video_id = $matches[1];
        } else if(is_numeric($url)) {
            // Doğrudan video ID girildiyse
            $video_id = $url;
        }
    }
    
    if(empty($video_id)) {
        echo json_encode([
            'success' => false,
            'error' => 'Geçerli bir Vimeo video URL\'si veya ID\'si giriniz.'
        ]);
        exit;
    }
    
    try {
        // Vimeo oEmbed API'den video bilgilerini al
        $oembed_url = "https://vimeo.com/api/oembed.json?url=https://vimeo.com/{$video_id}";
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $oembed_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $api_response = curl_exec($ch);
        
        if(curl_errno($ch)) {
            throw new Exception('cURL Error: ' . curl_error($ch));
        }
        
        curl_close($ch);
        
        $data = json_decode($api_response, true);
        
        if(empty($data)) {
            throw new Exception('Video bulunamadı. Lütfen URL\'yi kontrol edin.');
        }
        
        // Video süresini formatla
        $duration = $data['duration']; // saniye cinsinden
        $minutes = floor($duration / 60);
        $seconds = $duration % 60;
        $duration_formatted = sprintf("%d:%02d", $minutes, $seconds);
        
        // Video bilgilerini döndür
        echo json_encode([
            'success' => true,
            'video' => [
                'id' => $video_id,
                'title' => $data['title'],
                'description' => $data['description'] ?? '',
                'thumbnail' => $data['thumbnail_url'],
                'author_name' => $data['author_name'],
                'duration' => $duration_formatted,
                'total_seconds' => $duration,
                'height' => $data['height'],
                'width' => $data['width']
            ]
        ]);
        
    } catch(Exception $e) {
        echo json_encode([
            'success' => false,
            'error' => $e->getMessage()
        ]);
    }
    
    exit;
}

// Vimeo Video İçe Aktarma
if(isset($_POST['vimeo_import'])) {
    try {
        $kurs_id = $_POST['kurs_id'];
        $modul_id = $_POST['bolum_id']; // aslında modul_id
        $title = $_POST['title'];
        $description = $_POST['description'] ?? '';
        $video_id = $_POST['video_id'];
        $duration = isset($_POST['duration']) ? intval($_POST['duration']) : 0;
        
        if(empty($title) || empty($video_id)) {
            throw new Exception("Başlık ve video ID bilgileri gereklidir.");
        }
        
        $db->beginTransaction();
        
        // Sıradaki mevcut son ders sırasını bul
        $sirasor = $db->prepare("SELECT MAX(bolum_sira) as max_sira FROM kurs_bolumleri WHERE modul_id = ?");
        $sirasor->execute([$modul_id]);
        $siracek = $sirasor->fetch(PDO::FETCH_ASSOC);
        $bolum_sira = ($siracek['max_sira'] ?? 0) + 1;
        
        // Süreyi saat ve dakikaya çevir
        $sure_saat = floor($duration / 3600);
        $sure_dakika = floor(($duration % 3600) / 60);
        
        // Veritabanına kaydet
        $kaydet = $db->prepare("INSERT INTO kurs_bolumleri SET
            modul_id = ?,
            bolum_ad = ?,
            bolum_sira = ?,
            bolum_sure_saat = ?,
            bolum_sure_dakika = ?,
            icerik_tipi = ?,
            video_url = ?");
        
        $insert = $kaydet->execute([
            $modul_id,
            $title,
            $bolum_sira,
            $sure_saat,
            $sure_dakika,
            'vimeo',
            $video_id
        ]);
        
        if(!$insert) {
            throw new Exception("Ders kaydedilirken bir hata oluştu.");
        }
        
        $db->commit();
        
        // AJAX isteği için JSON yanıtı döndür
        echo json_encode(['success' => true, 'message' => 'Vimeo videosu başarıyla eklendi.']);
        exit;
        
    } catch(Exception $e) {
        if($db->inTransaction()) {
            $db->rollBack();
        }
        
        // AJAX isteği için JSON hata yanıtı döndür
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        exit;
    }
}

// Fetch videos from Bunny Stream
if(isset($_POST['bunny_fetch_videos'])) {
    try {
        // Make sure no output has been sent before our JSON response
        if (headers_sent($file, $line)) {
            error_log("Headers already sent in $file:$line");
        }
        
        $api_key = $_POST['api_key'];
        $library_id = $_POST['library_id'];
        
        if(empty($api_key) || empty($library_id)) {
            throw new Exception("API Key ve Library ID gereklidir.");
        }
        
        // Include BunnyStream class
        require_once '../../includes/classes/BunnyStream.php';
        
        // Initialize with user provided credentials
        $bunnyStream = new BunnyStream(
            $api_key,
            $library_id,
            $library_id . ".b-cdn.net" // Default hostname pattern
        );
        
        // Function to get videos
        $videos = $bunnyStream->listVideos();
        
        if($videos === false) {
            throw new Exception("Videolar alınırken bir hata oluştu. Lütfen bilgilerinizi kontrol edin.");
        }
        
        // Make sure the response is valid JSON
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'videos' => $videos]);
        exit;
        
    } catch(Exception $e) {
        error_log("Bunny fetch error: " . $e->getMessage());
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        exit;
    }
}

// Add selected video from Bunny Stream
if(isset($_POST['bunny_add_selected_video'])) {
    try {
        $kurs_id = $_POST['kurs_id'];
        $modul_id = $_POST['bolum_id'];
        $video_guid = $_POST['video_guid'];
        $title = $_POST['title'];
        $duration = intval($_POST['duration']);
        
        // Validation
        if(empty($modul_id)) {
            throw new Exception("Modül seçilmedi!");
        }
        
        if(empty($video_guid)) {
            throw new Exception("Video GUID bulunamadı!");
        }
        
        $db->beginTransaction();
        
        // Calculate hours and minutes
        $sure_saat = floor($duration / 3600);
        $sure_dakika = floor(($duration % 3600) / 60);
        
        // Get next lesson order
        $sirasor = $db->prepare("SELECT MAX(bolum_sira) as max_sira FROM kurs_bolumleri WHERE modul_id = ?");
        $sirasor->execute([$modul_id]);
        $siracek = $sirasor->fetch(PDO::FETCH_ASSOC);
        $bolum_sira = ($siracek['max_sira'] ?? 0) + 1;
        
        // Create embed code instead of just saving the GUID
        require_once '../../includes/config.php';
        $embed_code = '<iframe src="https://' . BUNNY_STREAM_HOSTNAME . '/embed/' . $video_guid . '" width="100%" height="400" frameborder="0" allowfullscreen></iframe>';
        
        // Save to database with embed code
        $kaydet = $db->prepare("INSERT INTO kurs_bolumleri SET
            modul_id = ?,
            bolum_ad = ?,
            bolum_sira = ?,
            bolum_sure_saat = ?,
            bolum_sure_dakika = ?,
            icerik_tipi = ?,
            video_url = ?,
            embed_kodu = ?");
        
        $insert = $kaydet->execute([
            $modul_id,
            $title,
            $bolum_sira,
            $sure_saat,
            $sure_dakika,
            'bunnystream',
            $video_guid,
            $embed_code
        ]);
        
        if(!$insert) {
            throw new Exception("İçerik kaydedilirken bir hata oluştu.");
        }
        
        $db->commit();
        
        // Return success response
        echo json_encode(['success' => true, 'message' => 'Video başarıyla eklendi.']);
        exit;
        
    } catch(Exception $e) {
        if($db->inTransaction()) {
            $db->rollBack();
        }
        
        // AJAX isteği için JSON hata yanıtı döndür
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        exit;
    }
}

// Handle H5P content upload
if(isset($_POST['h5p_upload'])) {
    try {
        $kurs_id = $_POST['kurs_id'];
        $bolum_id = $_POST['bolum_id']; // This is actually the module ID
        $title = $_POST['title'];
        $description = $_POST['description'] ?? '';
        
        // Validation
        if(empty($bolum_id)) {
            throw new Exception("Modül seçilmedi!");
        }
        
        if(!isset($_FILES['h5p_file']) || $_FILES['h5p_file']['error'] != 0) {
            throw new Exception("H5P dosyası yükleme hatası!");
        }
        
        $db->beginTransaction();
        
        // Get next lesson order
        $sirasor = $db->prepare("SELECT MAX(bolum_sira) as max_sira FROM kurs_bolumleri WHERE modul_id = ?");
        $sirasor->execute([$bolum_id]);
        $siracek = $sirasor->fetch(PDO::FETCH_ASSOC);
        $bolum_sira = ($siracek['max_sira'] ?? 0) + 1;
        
        // Process H5P file
        $h5p_file = $_FILES['h5p_file'];
        $file_ext = strtolower(pathinfo($h5p_file['name'], PATHINFO_EXTENSION));
        
        if($file_ext != 'h5p') {
            throw new Exception("Geçersiz dosya formatı! Sadece .h5p uzantılı dosyalar kabul edilir.");
        }
        
        // Create unique filename
        $new_filename = time() . '_' . uniqid() . '.h5p';
        $upload_dir = '../../uploads/h5p/';
        
        // Create directory if it doesn't exist
        if(!file_exists($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        
        $upload_path = $upload_dir . $new_filename;
        
        if(!move_uploaded_file($h5p_file['tmp_name'], $upload_path)) {
            throw new Exception("Dosya yükleme hatası!");
        }
        
        // Create embed code for H5P content
        $h5p_url = 'uploads/h5p/' . $new_filename;
        $embed_code = '<iframe src="' . $h5p_url . '" width="100%" height="500" frameborder="0" allowfullscreen="allowfullscreen"></iframe>';
        
        // For now, we'll store a simple embed placeholder
        // In a production system, you'd likely have an H5P integration library or service
        
        // Save to database with embed code
        $kaydet = $db->prepare("INSERT INTO kurs_bolumleri SET
            modul_id = ?,
            bolum_ad = ?,
            bolum_sira = ?,
            bolum_sure_saat = ?,
            bolum_sure_dakika = ?,
            icerik_tipi = ?,
            video_url = ?,
            embed_kodu = ?,
            is_preview = ?");
        
        $insert = $kaydet->execute([
            $bolum_id,
            $title,
            $bolum_sira,
            0, // Default hours
            30, // Default minutes (for h5p content)
            'h5p',
            $h5p_url,
            $embed_code,
            isset($_POST['is_preview']) ? 1 : 0
        ]);
        
        if(!$insert) {
            throw new Exception("İçerik kaydedilirken bir hata oluştu.");
        }
        
        $db->commit();
        
        // Redirect to course content page
        header("Location: ../production/kurs-icerik-duzenle.php?kurs_id=$kurs_id&durum=ok");
        exit;
        
    } catch(Exception $e) {
        if(isset($db) && $db->inTransaction()) {
            $db->rollBack();
        }
        
        header("Location: ../production/kurs-icerik-duzenle.php?kurs_id=$kurs_id&durum=no&hata=" . urlencode($e->getMessage()));
        exit;
    }
}

// PDF Upload Handler
if(isset($_POST['pdf_upload'])) {
    try {
        $kurs_id = $_POST['kurs_id'];
        $bolum_id = $_POST['bolum_id']; // modul_id
        $title = $_POST['title'];
        
        if(empty($title) || empty($bolum_id)) {
            throw new Exception("Başlık ve bölüm bilgileri gereklidir.");
        }
        
        // PDF dosyası kontrolü
        if(!isset($_FILES['pdf_file']) || $_FILES['pdf_file']['error'] != 0) {
            throw new Exception("PDF dosyası yükleme hatası!");
        }
        
        $pdf_file = $_FILES['pdf_file'];
        $file_ext = strtolower(pathinfo($pdf_file['name'], PATHINFO_EXTENSION));
        
        if($file_ext != 'pdf') {
            throw new Exception("Geçersiz dosya formatı! Sadece PDF dosyaları kabul edilir.");
        }
        
        $db->beginTransaction();
        
        // Sıradaki mevcut son ders sırasını bul
        $sirasor = $db->prepare("SELECT MAX(bolum_sira) as max_sira FROM kurs_bolumleri WHERE modul_id = ?");
        $sirasor->execute([$bolum_id]);
        $siracek = $sirasor->fetch(PDO::FETCH_ASSOC);
        $bolum_sira = ($siracek['max_sira'] ?? 0) + 1;
        
        // PDF için klasör oluştur
        $upload_dir = '../../uploads/pdf/';
        if(!file_exists($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        
        // Benzersiz dosya adı oluştur
        $fileName = 'pdf_' . time() . '_' . uniqid() . '.pdf';
        $filePath = $upload_dir . $fileName;
        
        // Dosyayı yükle
        if(!move_uploaded_file($pdf_file['tmp_name'], $filePath)) {
            throw new Exception("Dosya yüklenirken bir hata oluştu!");
        }
        
        // Veritabanına kaydet
        $file_url = '/uploads/pdf/' . $fileName;
        $embed_code = '<iframe src="' . $file_url . '" width="100%" height="600" frameborder="0"></iframe>';
        
        $kaydet = $db->prepare("INSERT INTO kurs_bolumleri SET
            modul_id = ?,
            bolum_ad = ?,
            bolum_sira = ?,
            bolum_sure_saat = ?,
            bolum_sure_dakika = ?,
            icerik_tipi = ?,
            video_url = ?,
            embed_kodu = ?,
            is_preview = ?");
        
        $insert = $kaydet->execute([
            $bolum_id,
            $title,
            $bolum_sira,
            0, // Süre bilgisi yok
            0, // Süre bilgisi yok
            'pdf',
            $file_url,
            $embed_code,
            isset($_POST['is_preview']) ? 1 : 0
        ]);
        
        if(!$insert) {
            throw new Exception("PDF kaydedilirken bir hata oluştu.");
        }
        
        $db->commit();
        
        header("Location: ../production/kurs-icerik-duzenle.php?kurs_id=$kurs_id&durum=ok");
        exit;
        
    } catch(Exception $e) {
        if(isset($db) && $db->inTransaction()) {
            $db->rollBack();
        }
        
        // Hata varsa yüklenen dosyayı sil
        if(isset($filePath) && file_exists($filePath)) {
            unlink($filePath);
        }
        
        header("Location: ../production/kurs-icerik-duzenle.php?kurs_id=" . $_POST['kurs_id'] . "&durum=no&hata=" . urlencode($e->getMessage()));
        exit;
    }
}

// Presentation Upload Handler
if(isset($_POST['presentation_upload'])) {
    try {
        $kurs_id = $_POST['kurs_id'];
        $bolum_id = $_POST['bolum_id']; // modul_id
        $title = $_POST['title'];
        
        if(empty($title) || empty($bolum_id)) {
            throw new Exception("Başlık ve bölüm bilgileri gereklidir.");
        }
        
        // Sunum dosyası kontrolü
        if(!isset($_FILES['ppt_file']) || $_FILES['ppt_file']['error'] != 0) {
            throw new Exception("Sunum dosyası yükleme hatası!");
        }
        
        $ppt_file = $_FILES['ppt_file'];
        $file_ext = strtolower(pathinfo($ppt_file['name'], PATHINFO_EXTENSION));
        
        $allowed_exts = ['ppt', 'pptx', 'odp', 'key'];
        if(!in_array($file_ext, $allowed_exts)) {
            throw new Exception("Geçersiz dosya formatı! Sadece PPT, PPTX, ODP veya KEY dosyaları kabul edilir.");
        }
        
        $db->beginTransaction();
        
        // Sıradaki mevcut son ders sırasını bul
        $sirasor = $db->prepare("SELECT MAX(bolum_sira) as max_sira FROM kurs_bolumleri WHERE modul_id = ?");
        $sirasor->execute([$bolum_id]);
        $siracek = $sirasor->fetch(PDO::FETCH_ASSOC);
        $bolum_sira = ($siracek['max_sira'] ?? 0) + 1;
        
        // Sunum için klasör oluştur
        $upload_dir = '../../uploads/presentations/';
        if(!file_exists($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        
        // Benzersiz dosya adı oluştur
        $fileName = 'ppt_' . time() . '_' . uniqid() . '.' . $file_ext;
        $filePath = $upload_dir . $fileName;
        
        // Dosyayı yükle
        if(!move_uploaded_file($ppt_file['tmp_name'], $filePath)) {
            throw new Exception("Dosya yüklenirken bir hata oluştu!");
        }
        
        // Veritabanına kaydet
        $file_url = '/uploads/presentations/' . $fileName;
        $embed_code = '<div class="presentation-container">
                          <a href="' . $file_url . '" class="btn btn-primary btn-lg" target="_blank">
                              <i class="fa fa-file-powerpoint-o"></i> Sunumu Görüntüle/İndir
                          </a>
                      </div>';
        
        $kaydet = $db->prepare("INSERT INTO kurs_bolumleri SET
            modul_id = ?,
            bolum_ad = ?,
            bolum_sira = ?,
            bolum_sure_saat = ?,
            bolum_sure_dakika = ?,
            icerik_tipi = ?,
            video_url = ?,
            embed_kodu = ?,
            is_preview = ?");
        
        $insert = $kaydet->execute([
            $bolum_id,
            $title,
            $bolum_sira,
            0, // Süre bilgisi yok
            0, // Süre bilgisi yok
            'presentation',
            $file_url,
            $embed_code,
            isset($_POST['is_preview']) ? 1 : 0
        ]);
        
        if(!$insert) {
            throw new Exception("Sunum kaydedilirken bir hata oluştu.");
        }
        
        $db->commit();
        
        header("Location: ../production/kurs-icerik-duzenle.php?kurs_id=$kurs_id&durum=ok");
        exit;
        
    } catch(Exception $e) {
        if(isset($db) && $db->inTransaction()) {
            $db->rollBack();
        }
        
        // Hata varsa yüklenen dosyayı sil
        if(isset($filePath) && file_exists($filePath)) {
            unlink($filePath);
        }
        
        header("Location: ../production/kurs-icerik-duzenle.php?kurs_id=" . $_POST['kurs_id'] . "&durum=no&hata=" . urlencode($e->getMessage()));
        exit;
    }
}

?>



