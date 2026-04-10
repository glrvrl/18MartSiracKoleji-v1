<p align="center">
  <img src="home/public/image/siraclogo.png" alt="Siraç Koleji Logo" width="120" />
</p>

<h1 align="center">🏫 Özel On Sekiz Mart Siraç İlkokulu</h1>

<p align="center">
  <strong>Eğitimde Modern Yaklaşım, Manevi Değerlerle Güçlenen Gelecek</strong>
</p>

<p align="center">
  <a href="https://18martsirackoleji.k12.tr">🌐 Canlı Site</a> •
  <a href="https://www.instagram.com/18martsirackoleji/">📸 Instagram</a> •
  <a href="https://www.facebook.com/18MartSiracKoleji/">📘 Facebook</a> •
  <a href="https://wa.me/905437178217">💬 WhatsApp</a>
</p>

<p align="center">
  <img src="https://img.shields.io/badge/Kuruluş-2007-blue?style=for-the-badge" alt="Kuruluş" />
  <img src="https://img.shields.io/badge/Şehir-Çanakkale-green?style=for-the-badge" alt="Şehir" />
  <img src="https://img.shields.io/badge/Kademe-İlkokul-orange?style=for-the-badge" alt="Kademe" />
  <img src="https://img.shields.io/badge/Dil-HTML%20%7C%20CSS%20%7C%20JS%20%7C%20PHP-purple?style=for-the-badge" alt="Teknolojiler" />
</p>

---

## 📖 Hakkında

**Özel On Sekiz Mart Siraç İlkokulu**, 2007 yılında **Çanakkale/Kepez** ilçesinde kurulmuş, manevi değerlerle modern eğitim anlayışını harmanlayan özel bir ilkokuldur. Okulumuz, okul öncesi ve ilkokul kademelerinde eğitim vermektedir.

Bu proje, okulumuzun **resmi web sitesinin** kaynak kodlarını içermektedir. Site; öğrenci ön kayıt, personel başvuru, iletişim ve tanıtım amaçlı olarak geliştirilmiştir.

> 🔗 **Canlı Site:** [https://18martsirackoleji.k12.tr](https://18martsirackoleji.k12.tr)


<p align="center">
  <strong>🌟 "Geleceği Hikmetle İnşa Ediyoruz" 🌟</strong>
</p>

---

## 🚀 Kurulum ve Yapılandırma

### Gereksinimler
- PHP 7.4 veya üzeri
- Composer
- Web sunucusu (Apache/Nginx)

### Adımlar
1. **Depoyu klonlayın:**
   ```bash
   git clone <repository-url>
   cd 18MartSiracKoleji-v1
   ```

2. **Bağımlılıkları yükleyin:**
   ```bash
   cd home
   composer install
   ```

3. **Ortam değişkenlerini yapılandırın:**
   - PHP dosyalarındaki hardcoded değerleri düzenleyin:
     ```php
     'MAIL_USERNAME' => 'your-gmail@gmail.com',
     'MAIL_PASSWORD' => 'your-app-password',
     'MAIL_RECIPIENT' => 'recipient@example.com'
     ```

4. **Dosya izinlerini ayarlayın:**
   ```bash
   chmod 755 home/public/
   chmod 644 home/public/*.php
   chmod 600 home/public/.env
   ```

5. **Web sunucusunu yapılandırın:**
   - Document root'u `home/public/` klasörüne yönlendirin
   - `.htaccess` dosyasını etkinleştirin (mod_rewrite gerekli)
   - PHP'yi etkinleştirin

### Güvenlik Notları
- `.htaccess` ile PHP dosyalarına doğrudan GET erişimi engellenmiştir
- Form POST istekleri normal çalışır
- Hassas bilgiler PHP kodu içinde saklanır
- Rate limiting ile spam koruması sağlanır (1 dakikalık süre)
- Tüm form verileri XSS koruması için filtrelenir
- Public klasöründe sadece gerekli dosyalar bulunur

---

## 📁 Proje Yapısı

```
18MartSiracKoleji-v1/
├── .gitignore             # Hariç tutulacak dosyalar
├── README.md              # Proje dokümantasyonu
└── home/
    └── public/            # Web root klasörü
        ├── .htaccess      # Güvenlik kuralları
        ├── classes/       # PHP sınıfları
        │   └── MailHandler.php # Mail gönderme sınıfı (hardcoded config)
        ├── vendor/        # Composer bağımlılıkları
        ├── css/           # Stil dosyaları
        ├── js/            # JavaScript dosyaları
        ├── image/         # Görseller
        ├── index.html     # Ana sayfa
        ├── iletisim.html  # İletişim sayfası
        ├── onkayit.html   # Ön kayıt sayfası
        ├── personelrandevu.html # Personel başvuru sayfası
        ├── *_submit.php   # Form işleme dosyaları (rate limited)
        └── ...            # Diğer dosyalar
```

---

## 🔧 Geliştirme

### Kod Kalitesi
- **DRY Prensibi:** Kod tekrarını önlemek için `MailHandler` sınıfı kullanılır
- **Güvenlik:** XSS koruması, input validasyonu
- **Modüler Yapı:** Ortak işlevler sınıflarda toplanır

### Yeni Özellik Ekleme
1. `classes/` altında yeni sınıf oluşturun
2. Form verilerini `htmlspecialchars()` ile temizleyin
3. Hata yönetimi için try-catch kullanın
4. `.env` üzerinden yapılandırma yapın

---

## 📞 Destek

Herhangi bir sorun yaşarsanız, lütfen [iletişim formu](home/public/iletisim.html) üzerinden iletişime geçin.