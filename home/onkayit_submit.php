<?php
require_once __DIR__ . '/vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// ─── AYARLAR ──────────────────────────────────────────────
$aliciEmail = 'test@gmail.com';
$emailKonu = 'Sirac Koleji - Yeni On Kayit Basvurusu';
$gmailKullanici = 'test@gmail.com';
$gmailSifre = 'APP_PASSWORD';  // Gmail App Password

// ─── POST KONTROLÜ ───────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: onkayit.html');
    exit;
}

// ─── FORM VERİLERİNİ AL ──────────────────────────────────
$studentName = isset($_POST['student_name']) ? htmlspecialchars(trim($_POST['student_name'])) : '';
$studentTc = isset($_POST['student_tc']) ? htmlspecialchars(trim($_POST['student_tc'])) : '';
$studentBirth = isset($_POST['student_birth']) ? htmlspecialchars(trim($_POST['student_birth'])) : '';
$studentGrade = isset($_POST['student_grade']) ? htmlspecialchars(trim($_POST['student_grade'])) : '';
$parentName = isset($_POST['parent_name']) ? htmlspecialchars(trim($_POST['parent_name'])) : '';
$parentPhone = isset($_POST['parent_phone']) ? htmlspecialchars(trim($_POST['parent_phone'])) : '';
$parentEmail = isset($_POST['parent_email']) ? htmlspecialchars(trim($_POST['parent_email'])) : '';
$parentRelation = isset($_POST['parent_relation']) ? htmlspecialchars(trim($_POST['parent_relation'])) : '';
$address = isset($_POST['address']) ? htmlspecialchars(trim($_POST['address'])) : '';
$kvkk = isset($_POST['kvkkCheck']) ? 'Evet' : 'Hayir';

// ─── ZORUNLU ALAN KONTROL ────────────────────────────────
if (empty($studentName) || empty($parentName) || empty($parentPhone) || empty($parentEmail)) {
    header('Location: onkayit.html?status=error&msg=zorunlu');
    exit;
}

// ─── TÜRKÇE KARŞILIKLAR ──────────────────────────────────
$sinifMap = [
    'okul_oncesi' => 'Okul Oncesi',
    '1' => '1. Sinif',
    '2' => '2. Sinif',
    '3' => '3. Sinif',
    '4' => '4. Sinif'
];

$yakinlikMap = [
    'anne' => 'Anne',
    'baba' => 'Baba',
    'diger' => 'Diger'
];

$sinifTR = isset($sinifMap[$studentGrade]) ? $sinifMap[$studentGrade] : $studentGrade;
$yakinlikTR = isset($yakinlikMap[$parentRelation]) ? $yakinlikMap[$parentRelation] : $parentRelation;

// ─── TARİH FORMATI ──────────────────────────────────────
$tarihFormatli = '';
if (!empty($studentBirth)) {
    $tarih = DateTime::createFromFormat('Y-m-d', $studentBirth);
    $tarihFormatli = $tarih ? $tarih->format('d.m.Y') : $studentBirth;
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
        .header { background: linear-gradient(135deg, #1a5276, #e68d08); padding: 30px; text-align: center; color: white; }
        .header h1 { margin: 0; font-size: 22px; font-weight: 600; }
        .header p { margin: 8px 0 0; opacity: 0.85; font-size: 14px; }
        .content { padding: 30px; }
        .section-title { background: #f8f9fa; padding: 10px 15px; border-left: 4px solid #e68d08; font-weight: 600; color: #1a5276; margin: 20px 0 15px; border-radius: 0 6px 6px 0; font-size: 15px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        th, td { padding: 10px 14px; text-align: left; border-bottom: 1px solid #eee; font-size: 14px; }
        th { width: 40%; color: #666; font-weight: 500; background: #fafafa; }
        td { color: #333; font-weight: 500; }
        .badge { display: inline-block; background: #e68d08; color: white; padding: 4px 12px; border-radius: 20px; font-size: 12px; }
        .footer { text-align: center; padding: 20px; color: #999; font-size: 12px; border-top: 1px solid #eee; }
        .note { background: #d4edda; border: 1px solid #28a745; border-radius: 8px; padding: 12px 15px; margin-top: 20px; font-size: 13px; color: #155724; }
    </style>
</head>
<body>
    <div class='container'>
        <div class='header'>
            <h1>Yeni On Kayit Basvurusu</h1>
            <p>Sirac Koleji Ilkokulu - 2026-2027 On Kayit</p>
        </div>
        <div class='content'>
            <div class='section-title'>Ogrenci Bilgileri</div>
            <table>
                <tr><th>Ogrenci Ad Soyad</th><td><strong>{$studentName}</strong></td></tr>
                <tr><th>T.C. Kimlik No</th><td>" . (!empty($studentTc) ? $studentTc : 'Belirtilmedi') . "</td></tr>
                <tr><th>Dogum Tarihi</th><td>{$tarihFormatli}</td></tr>
                <tr><th>Basvurulan Sinif</th><td><span class='badge'>{$sinifTR}</span></td></tr>
            </table>

            <div class='section-title'>Veli Bilgileri</div>
            <table>
                <tr><th>Veli Ad Soyad</th><td><strong>{$parentName}</strong></td></tr>
                <tr><th>Telefon</th><td>{$parentPhone}</td></tr>
                <tr><th>E-posta</th><td>{$parentEmail}</td></tr>
                <tr><th>Yakinlik Derecesi</th><td>{$yakinlikTR}</td></tr>
            </table>

            <div class='section-title'>Ek Bilgiler</div>
            <table>
                <tr><th>Adres</th><td>" . (!empty($address) ? nl2br($address) : 'Belirtilmedi') . "</td></tr>
                <tr><th>KVKK Onayi</th><td>{$kvkk}</td></tr>
            </table>

            <div class='note'>
                <strong>Basvuru Tarihi:</strong> {$basvuruTarihi}<br>
                Bu basvuru on kayit formundan otomatik olarak gonderilmistir.
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
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = $gmailKullanici;
    $mail->Password = $gmailSifre;
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;
    $mail->CharSet = 'UTF-8';

    $mail->setFrom($gmailKullanici, 'Sirac Koleji On Kayit');
    $mail->addAddress($aliciEmail);
    $mail->addReplyTo($parentEmail, $parentName);

    $mail->isHTML(true);
    $mail->Subject = $emailKonu . ' - ' . $studentName;
    $mail->Body = $emailIcerik;
    $mail->AltBody = strip_tags(str_replace(['<br>', '<br/>', '<br />'], "\n", $emailIcerik));

    $mail->send();
    header('Location: onkayit.html?status=success');

} catch (Exception $e) {
    error_log("Mail gonderilemedi: " . $mail->ErrorInfo);
    header('Location: onkayit.html?status=error&msg=mail');
}

exit;
?>