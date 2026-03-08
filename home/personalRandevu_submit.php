<?php
// PHPMailer Autoloader
require_once __DIR__ . '/vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// ─── AYARLAR ──────────────────────────────────────────────
$aliciEmail = 'test@gmail.com';
$emailKonu = 'Sirac Koleji - Yeni Personel Basvurusu';

$gmailKullanici = 'test@gmail.com';
$gmailSifre = 'APP_PASSWORD';  // Gmail App Password

// ─── POST KONTROLÜ ───────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: personalRandevu.html');
    exit;
}

// ─── FORM VERİLERİNİ AL ──────────────────────────────────
$fullname = isset($_POST['fullname']) ? htmlspecialchars(trim($_POST['fullname'])) : '';
$phone = isset($_POST['phone']) ? htmlspecialchars(trim($_POST['phone'])) : '';
$email = isset($_POST['email']) ? htmlspecialchars(trim($_POST['email'])) : '';
$birthdate = isset($_POST['birthdate']) ? htmlspecialchars(trim($_POST['birthdate'])) : '';
$city = isset($_POST['city']) ? htmlspecialchars(trim($_POST['city'])) : '';
$gender = isset($_POST['gender']) ? htmlspecialchars(trim($_POST['gender'])) : '';
$marital = isset($_POST['marital']) ? htmlspecialchars(trim($_POST['marital'])) : '';
$military = isset($_POST['military']) ? htmlspecialchars(trim($_POST['military'])) : '';
$position = isset($_POST['position']) ? htmlspecialchars(trim($_POST['position'])) : '';
$subjects = isset($_POST['subjects']) ? htmlspecialchars(trim($_POST['subjects'])) : '';
$education = isset($_POST['education']) ? htmlspecialchars(trim($_POST['education'])) : '';
$experience = isset($_POST['experience']) ? htmlspecialchars(trim($_POST['experience'])) : '';
$about = isset($_POST['about']) ? htmlspecialchars(trim($_POST['about'])) : '';
$kvkk = isset($_POST['kvkk']) ? 'Evet' : 'Hayır';
$approval = isset($_POST['approval']) ? 'Evet' : 'Hayır';

// ─── ZORUNLU ALAN KONTROL ────────────────────────────────
if (empty($fullname) || empty($phone) || empty($email) || empty($position)) {
    header('Location: personalRandevu.html?status=error&msg=zorunlu');
    exit;
}

// ─── DEĞERLERİ TÜRKÇE KARŞILIKLARA ÇEVİR ───────────────
$cinsiyetMap = [
    'male' => 'Erkek',
    'female' => 'Kadın'
];

$medeniMap = [
    'single' => 'Bekar',
    'married' => 'Evli'
];

$askerlikMap = [
    'done' => 'Yapıldı',
    'postponed' => 'Tecilli',
    'exempt' => 'Muaf'
];

$gorevMap = [
    'teacher' => 'Öğretmen',
    'it' => 'Bilişim / IT',
    'management' => 'Yönetim',
    'finance' => 'Finans / Muhasebe',
    'hr' => 'İnsan Kaynakları',
    'public' => 'Halkla İlişkiler',
    'admin' => 'İdari İşler',
    'security' => 'Güvenlik',
    'psychologist' => 'Psikolog / Rehber',
    'cook' => 'Mutfak / Aşçı',
    'cleaning' => 'Hizmetli / Temizlik',
    'driver' => 'Şoför / Servis',
    'gardener' => 'Bahçe / Teknik',
    'intern' => 'Stajyer',
    'other' => 'Diğer'
];

$dersMap = [
    'turkce' => 'Türkçe',
    'ingilizce' => 'İngilizce',
    'arapca' => 'Arapça',
    'matematik' => 'Matematik',
    'fen' => 'Fen Bilimleri',
    'bilgisayar' => 'Bilişim Teknolojileri',
    'sosyal' => 'Sosyal Bilgiler',
    'din' => 'Din Kültürü ve Ahlak Bilgisi',
    'tarih' => 'Tarih',
    'beden' => 'Beden Eğitimi',
    'muzik' => 'Müzik',
    'resim' => 'Görsel Sanatlar',
    'sinif' => 'Sınıf Öğretmenliği',
    'okuloncesi' => 'Okul Öncesi',
    'rehberlik' => 'Rehberlik',
    'teknoloji' => 'Teknoloji Tasarım',
    'zeka' => 'Zeka Oyunları',
    'drama' => 'Drama'
];

$cinsiyetTR = isset($cinsiyetMap[$gender]) ? $cinsiyetMap[$gender] : $gender;
$medeniTR = isset($medeniMap[$marital]) ? $medeniMap[$marital] : $marital;
$askerlikTR = isset($askerlikMap[$military]) ? $askerlikMap[$military] : $military;
$gorevTR = isset($gorevMap[$position]) ? $gorevMap[$position] : $position;

// Branş bilgisini çevir
$bransTR = '';
if (!empty($subjects)) {
    $bransArray = explode(',', $subjects);
    $bransTurkce = array_map(function ($b) use ($dersMap) {
        $b = trim($b);
        return isset($dersMap[$b]) ? $dersMap[$b] : $b;
    }, $bransArray);
    $bransTR = implode(', ', $bransTurkce);
}

// ─── TARİH FORMATI ──────────────────────────────────────
$tarihFormatli = '';
if (!empty($birthdate)) {
    $tarih = DateTime::createFromFormat('Y-m-d', $birthdate);
    if ($tarih) {
        $tarihFormatli = $tarih->format('d.m.Y');
    } else {
        $tarihFormatli = $birthdate;
    }
}

// ─── BAŞVURU TARİHİ ──────────────────────────────────────
date_default_timezone_set('Europe/Istanbul');
$basvuruTarihi = date('d.m.Y H:i');

// ─── E-POSTA İÇERİĞİ (HTML) ─────────────────────────────
$emailIcerik = "
<html>
<head>
    <meta charset='UTF-8'>
    <style>
        body { font-family: 'Segoe UI', Arial, sans-serif; background: #f4f4f4; margin: 0; padding: 20px; }
        .container { max-width: 650px; margin: 0 auto; background: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.1); }
        .header { background: linear-gradient(135deg, #1a3a5c, #2a9d8f); padding: 30px; text-align: center; color: white; }
        .header h1 { margin: 0; font-size: 22px; font-weight: 600; }
        .header p { margin: 8px 0 0; opacity: 0.85; font-size: 14px; }
        .content { padding: 30px; }
        .section-title { background: #f8f9fa; padding: 10px 15px; border-left: 4px solid #2a9d8f; font-weight: 600; color: #1a3a5c; margin: 20px 0 15px; border-radius: 0 6px 6px 0; font-size: 15px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        th, td { padding: 10px 14px; text-align: left; border-bottom: 1px solid #eee; font-size: 14px; }
        th { width: 40%; color: #666; font-weight: 500; background: #fafafa; }
        td { color: #333; font-weight: 500; }
        .badge { display: inline-block; background: #2a9d8f; color: white; padding: 4px 12px; border-radius: 20px; font-size: 12px; }
        .footer { text-align: center; padding: 20px; color: #999; font-size: 12px; border-top: 1px solid #eee; }
        .note { background: #fff3cd; border: 1px solid #ffc107; border-radius: 8px; padding: 12px 15px; margin-top: 20px; font-size: 13px; color: #856404; }
    </style>
</head>
<body>
    <div class='container'>
        <div class='header'>
            <h1>Yeni Personel Basvurusu</h1>
            <p>Sirac Koleji Ilkokulu - Is Basvuru Formu</p>
        </div>
        <div class='content'>
            <div class='section-title'>Kisisel Bilgiler</div>
            <table>
                <tr><th>Ad Soyad</th><td><strong>{$fullname}</strong></td></tr>
                <tr><th>Telefon</th><td>{$phone}</td></tr>
                <tr><th>E-posta</th><td>{$email}</td></tr>
                <tr><th>Dogum Tarihi</th><td>{$tarihFormatli}</td></tr>
                <tr><th>Il / Ilce</th><td>{$city}</td></tr>
                <tr><th>Cinsiyet</th><td>{$cinsiyetTR}</td></tr>
                <tr><th>Medeni Hal</th><td>{$medeniTR}</td></tr>
                <tr><th>Askerlik Durumu</th><td>{$askerlikTR}</td></tr>
            </table>

            <div class='section-title'>Gorev Bilgileri</div>
            <table>
                <tr><th>Basvurulan Pozisyon</th><td><span class='badge'>{$gorevTR}</span></td></tr>";

if (!empty($bransTR)) {
    $emailIcerik .= "
                <tr><th>Brans / Dersler</th><td>{$bransTR}</td></tr>";
}

$emailIcerik .= "
            </table>

            <div class='section-title'>Egitim ve Deneyim</div>
            <table>
                <tr><th>Egitim Durumu</th><td>" . (!empty($education) ? $education : 'Belirtilmedi') . "</td></tr>
                <tr><th>Deneyim (Yil)</th><td>" . (!empty($experience) ? $experience . ' yil' : 'Belirtilmedi') . "</td></tr>
                <tr><th>Ek Bilgiler</th><td>" . (!empty($about) ? nl2br($about) : 'Belirtilmedi') . "</td></tr>
            </table>

            <div class='section-title'>Onay Bilgileri</div>
            <table>
                <tr><th>KVKK Onayi</th><td>{$kvkk}</td></tr>
                <tr><th>Basvuru Onayi</th><td>{$approval}</td></tr>
            </table>

            <div class='note'>
                <strong>Basvuru Tarihi:</strong> {$basvuruTarihi}<br>
                Dosya ekleri (CV / Fotograf) varsa bu e-postanin ekinde bulabilirsiniz.
            </div>
        </div>
        <div class='footer'>
            2025 Sirac Koleji Ilkokulu | Bu e-posta otomatik olarak gonderilmistir.
        </div>
    </div>
</body>
</html>";

// ─── PHPMailer İLE E-POSTA GÖNDERİMİ ────────────────────
$mail = new PHPMailer(true);

try {
    // SMTP Ayarları
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = $gmailKullanici;
    $mail->Password = $gmailSifre;
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;
    $mail->CharSet = 'UTF-8';

    // Gönderici / Alıcı
    $mail->setFrom($gmailKullanici, 'Sirac Koleji Basvuru');
    $mail->addAddress($aliciEmail);
    $mail->addReplyTo($email, $fullname);

    // İçerik
    $mail->isHTML(true);
    $mail->Subject = $emailKonu;
    $mail->Body = $emailIcerik;
    $mail->AltBody = strip_tags(str_replace(['<br>', '<br/>', '<br />'], "\n", $emailIcerik));

    // CV Dosyası Ekleme
    if (isset($_FILES['cv']) && $_FILES['cv']['error'] === UPLOAD_ERR_OK) {
        $mail->addAttachment(
            $_FILES['cv']['tmp_name'],
            $_FILES['cv']['name']
        );
    }

    // Fotoğraf Dosyası Ekleme
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $mail->addAttachment(
            $_FILES['photo']['tmp_name'],
            $_FILES['photo']['name']
        );
    }

    // Gönder
    $mail->send();
    header('Location: personalRandevu.html?status=success');

} catch (Exception $e) {
    // Hata detayını logla
    error_log("Mail gonderilemedi: " . $mail->ErrorInfo);
    header('Location: personalRandevu.html?status=error&msg=mail&detail=' . urlencode($mail->ErrorInfo));
}

exit;
?>