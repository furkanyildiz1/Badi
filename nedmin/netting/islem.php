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

	$kullanicisor=$db->prepare("SELECT * FROM kullanici where kullanici_mail=:kullanici_mail AND kullanici_password=:kullanici_password AND kullanici_yetki=:kullanici_yetki");
	$kullanicisor->execute([
		'kullanici_mail' => $kullanici_mail,
		'kullanici_password' => $kullanici_password,
		'kullanici_yetki' => 5
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
			kullanici_adsoyad=:kullanici_adsoyad,
			kullanici_tc=:kullanici_tc, 
			kullanici_gsm=:kullanici_gsm,
			kullanici_durum=:kullanici_durum
			WHERE kullanici_id={$_POST['kullanici_id']}
			"); // where satırında degisken kullanılacağı icin süslü parantez icine yazdı
		// AYRICA SÜTUNLARI BİRBİRİNE ESİTLERKEN DEMEK İSTEDİGİMİ ANLADIN ARALARA ASLA BOSLUK BIRAKMA

		$update=$kullaniciduzenle->execute([
			'kullanici_adsoyad' => $_POST['kullanici_adsoyad'],
			'kullanici_tc' => $_POST['kullanici_tc'],
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
            
            // Kullanıcı tipine göre yönlendirme
            if($kullanici['kullanici_tip'] == 'egitmen') {
                header("Location:../../index.php");
            } else {
                header("Location:../../index2.php");
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
            'kullanici_durum' => 1
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
		fiyat=:fiyat,
		video_yol=:video_yol,
		resim_yol=:resim_yol");

	$kaydet=$kurskaydet->execute([

		'baslik' => $_POST['kurs_baslik'],
		'kategori_id' => $_POST['kategori_id'],
		'alt_kategori_id' => $_POST['alt_kategori_id'],
		'kurs_seviye_id' => $_POST['kurs_seviye_id'],
		'egitmen_id' => $_POST['egitmen_id'],
		'aciklama' => $_POST['kurs_aciklama'],
		'sure' => $_POST['kurs_sure'],
		'fiyat' => $_POST['kurs_fiyat'],
		'video_yol' => $refvideoyol,
		'resim_yol' => $refimgyol
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
// ANA HAKKIMIZDA DUZENLEME ISLEMI BITIS


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
			header("Location: ../../index2.php?durum=ok");
		}
		else
		{
			header("Location: ../../index2.php?durum=no");
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

?>



