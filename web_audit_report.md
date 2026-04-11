# Sirac Koleji Web Sitesi Denetim Raporu (Audit Report)

Bu rapor, "Sirac Koleji İlkokulu" web sitesinin tüm sayfaları üzerinde yapılan detaylı inceleme sonucunda tespit edilen hataları ve iyileştirme önerilerini içermektedir.

## 1. Kurumsal Kimlik ve Marka Tutarsızlıkları
Web sitesinin genelinde okul ismi farklı şekillerde yazılmıştır. Bu durum profesyonel imajı zedelemektedir.
- **Siraç vs Sirac:** Bazı sayfalarda "Siraç" (Türkçe karakterli), bazılarında "Sirac" kullanılmaktadır.
- **Tam İsim:** Önceki taleplerde "Özel On Sekiz Mart Sirac Koleji İlkokulu" olarak belirtilmesine rağmen, `index.html`, `iletisim.html` ve `robotik.html` gibi sayfalarda farklı varyasyonlar mevcuttur.
- **Logo Alt Metinleri:** Bazı `alt` etiketlerinde "18 Mart Sirac", bazılarında "On Sekiz Mart" kullanılmaktadır.

## 2. Teknik Hatalar ve Performans
### E-Posta Gönderim Sistemi (Kritik)
- `classes/MailHandler.php` dosyasında SMTP bilgileri (kullanıcı adı ve şifre) **"test@gmail.com"** ve **"APP_PASSWORD"** olarak bırakılmıştır. Bu bilgiler güncellenmeden iletişim formları çalışmayacaktır.
- `iletisim.html` ve `iletisim_submit.php` arasında e-posta adresi uyuşmazlığı vardır (`bilgi@sirackoleji.com` vs `bilgi@18martsirackoleji.com`).

### Kütüphane Eksiklikleri
- `iletisim.html` sayfasında animasyonlar için **AOS (Animate On Scroll)** kütüphanesinin JavaScript dosyası eklenmiş ancak **CSS dosyası** unutulmuştur. Bu durum animasyonların çalışmamasına veya öğelerin gizli kalmasına neden olabilir.

### Kod Tekrarı (Redundancy)
- Header (Navbar) ve Footer bölümleri her HTML dosyasında manuel olarak kopyalanmıştır. Bu, bir değişiklik yapıldığında tüm sayfaların tek tek güncellenmesini gerektiren hatalı bir mimaridir. Projenin PHP desteği olduğu için `include` yapısına geçilmelidir.
- Bazı sayfalarda (`robotik.html` gibi) Footer için mükerrer (duplicate) CSS kodları sayfa içine gömülmüştür.

## 3. Dosya ve Klasör Yapısı (Yazım Hataları)
Sunucu üzerindeki klasör isimlerinde ciddi yazım hataları mevcuttur. Bu durum ileride dosya yolu (path) hatalarına yol açabilir:
- `image/habarlar/` -> Doğrusu: `image/haberler/`
- `image/odevlar/` -> Doğrusu: `image/ödevler/`
- `image/ogretman/` -> Doğrusu: `image/öğretmenler/`
- `image/maneve/` -> Doğrusu: `image/manevi/`
- `image/siınif_goruntulu/` -> Türkçe karakter ve yazım hatası.

## 4. İçerik ve Yazım Hataları (Typos)
Sayfa içeriklerinde kullanıcıların dikkatini çekecek yazım hataları tespit edilmiştir:
- **Genel:** `index.html` sayfasında "Çannakale" (Çanakkale olmalı).
- **Robotik Sayfası:** "yapa Zeka" (Yapay Zeka olmalı).
- **Robotik Sayfası:** "Mühandıslık Becerılerı" (Mühendislik Becerileri olmalı).
- **Haberler:** "habarlar" yazımı haberler başlığında da görülebilmektedir.

## 5. Görsel ve Medya Sorunları
- **Favicon:** Bazı sayfalarda `.ico`, bazılarında `.png` referansı verilmiş.
- **Video:** `index.html`'deki drone videosu (`.mov` formatı) 200MB üzerindedir. Web kullanımı için çok büyüktür, `.mp4` formatına dönüştürülmeli ve sıkıştırılmalıdır.
- **Eksik Elementler:** `index.html`'deki JavaScript kodları `imagePopup` ID'li bir elementi aramaktadır ancak HTML içerisinde böyle bir element bulunmamaktadır.

## Önerilen Öncelikli Adımlar
1. **SMTP Bilgilerinin Güncellenmesi:** Formların çalışması için `MailHandler.php` yapılandırılmalıdır.
2. **Marka Standardizasyonu:** Tüm sayfalarda okul ismi "Özel On Sekiz Mart Sirac Koleji İlkokulu" olarak güncellenmelidir.
3. **Yazım Hatalarının Düzeltilmesi:** Content içindeki ve klasör isimlerindeki hatalar temizlenmelidir.
4. **Mimarinin İyileştirilmesi:** Kod tekrarını önlemek için ortak header/footer yapıları oluşturulmalıdır.

---
*Not: Bu denetim sonucunda herhangi bir dosya üzerinde değişiklik yapılmamıştır. Onay vermeniz durumunda düzeltmelere başlanabilir.*
