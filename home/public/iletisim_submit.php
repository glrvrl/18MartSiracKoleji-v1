<?php
session_start();

// ─── RATE LIMIT KONTROL ──────────────────────────────────
$rateLimitKey = 'iletisim_submit';
$rateLimitTime = 60; // 1 dakika
$maxSubmissions = 1;

if (isset($_SESSION[$rateLimitKey])) {
    $lastSubmission = $_SESSION[$rateLimitKey];
    if (time() - $lastSubmission < $rateLimitTime) {
        header('Location: iletisim.html?status=error&msg=rate_limit');
        exit;
    }
}

$_SESSION[$rateLimitKey] = time();

// ─── FORM VERİLERİNİ AL ──────────────────────────────────
$fullname = isset($_POST['fullname']) ? htmlspecialchars(trim($_POST['fullname'])) : '';
$email = isset($_POST['email']) ? htmlspecialchars(trim($_POST['email'])) : '';
$subject = isset($_POST['subject']) ? htmlspecialchars(trim($_POST['subject'])) : '';
$message = isset($_POST['message']) ? htmlspecialchars(trim($_POST['message'])) : '';

// ─── ZORUNLU ALAN KONTROL ────────────────────────────────
if (empty($fullname) || empty($email) || empty($message)) {
    header('Location: iletisim.html?status=error&msg=zorunlu');
    exit;
}

// ─── KONU TÜRKÇE KARŞILIKLAR ─────────────────────────────
$konuMap = [
    'genel' => 'Genel Bilgi',
    'kayit' => 'Okul Kayit Surecleri',
    'is' => 'Is Basvurusu',
    'oneri' => 'Gorus & Oneri',
    'diger' => 'Diger'
];
$konuTR = isset($konuMap[$subject]) ? $konuMap[$subject] : $subject;

// ─── BAŞVURU TARİHİ ──────────────────────────────────────
date_default_timezone_set('Europe/Istanbul');
$mesajTarihi = date('d.m.Y H:i');

// ─── E-POSTA İÇERİĞİ (HTML) ─────────────────────────────
$emailIcerik = "
<html>
<head>
    <meta charset='UTF-8'>
    <style>
        body { font-family: 'Segoe UI', Arial, sans-serif; background: #f4f4f4; margin: 0; padding: 20px; }
        .container { max-width: 650px; margin: 0 auto; background: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.1); }
        .header { background: linear-gradient(135deg, #1a3a5c, #0af1d3); padding: 30px; text-align: center; color: white; }
        .header h1 { margin: 0; font-size: 22px; font-weight: 600; }
        .header p { margin: 8px 0 0; opacity: 0.85; font-size: 14px; }
        .content { padding: 30px; }
        .section-title { background: #f8f9fa; padding: 10px 15px; border-left: 4px solid #0af1d3; font-weight: 600; color: #1a3a5c; margin: 20px 0 15px; border-radius: 0 6px 6px 0; font-size: 15px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        th, td { padding: 10px 14px; text-align: left; border-bottom: 1px solid #eee; font-size: 14px; }
        th { width: 35%; color: #666; font-weight: 500; background: #fafafa; }
        td { color: #333; font-weight: 500; }
        .badge { display: inline-block; background: #1a3a5c; color: white; padding: 4px 12px; border-radius: 20px; font-size: 12px; }
        .message-box { background: #f8f9fa; border: 1px solid #e9ecef; border-radius: 10px; padding: 20px; margin: 15px 0; font-size: 14px; color: #333; line-height: 1.7; }
        .footer { text-align: center; padding: 20px; color: #999; font-size: 12px; border-top: 1px solid #eee; }
        .note { background: #e3f2fd; border: 1px solid #2196f3; border-radius: 8px; padding: 12px 15px; margin-top: 20px; font-size: 13px; color: #0d47a1; }
    </style>
</head>
<body>
    <div class='container'>
        <div class='header'>
            <h1>Yeni Iletisim Mesaji</h1>
            <p>Sirac Koleji Ilkokulu - Web Sitesi Iletisim Formu</p>
        </div>
        <div class='content'>
            <div class='section-title'>Gonderen Bilgileri</div>
            <table>
                <tr><th>Ad Soyad</th><td><strong>{$fullname}</strong></td></tr>
                <tr><th>E-posta</th><td>{$email}</td></tr>
                <tr><th>Konu</th><td><span class='badge'>{$konuTR}</span></td></tr>
            </table>

            <div class='section-title'>Mesaj Icerigi</div>
            <div class='message-box'>
                " . nl2br($message) . "
            </div>

            <div class='note'>
                <strong>Mesaj Tarihi:</strong> {$mesajTarihi}<br>
                Bu mesaj web sitesi iletisim formundan gonderilmistir. Yanit vermek icin dogrudan e-postaya cevap yazabilirsiniz.
            </div>
        </div>
        <div class='footer'>
            2025 Sirac Koleji Ilkokulu | Bu e-posta otomatik olarak gonderilmistir.
        </div>
    </div>
</body>
</html>";

// ─── MAİL GÖNDERİMİ ─────────────────────────────────────
try {
    $mailHandler = new MailHandler();
    $aliciEmail = $mailHandler->getRecipientEmail();
    $emailKonu = 'Sirac Koleji - Yeni Iletisim Mesaji - ' . $konuTR;

    $mailHandler->sendEmail(
        $aliciEmail,
        $emailKonu,
        $emailIcerik,
        '',
        'Sirac Koleji Iletisim',
        ['email' => $email, 'name' => $fullname]
    );

    header('Location: iletisim.html?status=success');

} catch (Exception $e) {
    header('Location: iletisim.html?status=error&msg=mail');
}

exit;
?>