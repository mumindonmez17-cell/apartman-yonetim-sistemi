# 🏢 Apartman Yönetim Sistemi

![PHP Version](https://img.shields.io/badge/PHP-%3E%3D%208.0-blue.svg)
![License](https://img.shields.io/badge/License-MIT-green.svg)
![Status](https://img.shields.io/badge/Status-Stable-brightgreen.svg)

Apartman ve site yönetim süreçlerini modernize eden, şeffaf ve kullanıcı dostu bir yönetim yazılımıdır. Bu sistem, karmaşık aidat takiplerini, gider yönetimini ve sakin iletişimini tek bir merkezden yönetmek isteyen apartman yöneticileri ve sakinleri için tasarlanmıştır. Kağıt-kalem devrini kapatarak, dijital ve güvenli bir yönetim deneyimi sunar.

# 🚀 Özellikler

*   **💳 Aidat Yönetimi:** Aylık aidatların oluşturulması ve takibi.
*   **📊 Gider Takibi:** Bina masraflarının kategorize edilerek kayıt altına alınması.
*   **🏢 Apartman / Blok Yönetimi:** Sınırsız blok ve daire tanımlama imkanı.
*   **👥 Kullanıcı Yönetimi:** Kiracı ve ev sahibi bilgilerinin detaylı takibi.
*   **💰 Ödeme Takibi:** Borç tahsilatları ve geçmiş ödeme dökümleri.
*   **📱 WhatsApp Bildirim Sistemi:** Meta Cloud API ile otomatik aidat hatırlatma.
*   **💻 Yönetim Paneli:** Modern ve responsive (mobil uyumlu) yönetici arayüzü.
*   **⚙️ Dinamik Site Ayarları:** Site adı ve parametrelerini panel üzerinden kolayca yönetme.

# 🛠️ Teknolojiler

*   **PHP:** Versiyon 8.0 ve üzeri (MVC Mimarisi)
*   **MySQL / MariaDB:** Veri depolama ve ilişkisel yönetim.
*   **JavaScript:** Dinamik ve interaktif kullanıcı deneyimi.
*   **Bootstrap 5:** Modern ve duyarlı (responsive) tasarım.

# ⚙️ Kurulum

Sistemi kullanıma hazır hale getirmek için aşağıdaki adımları sırasıyla uygulayın:

1.  **Projeyi İndirin:** Bu depoyu klonlayın veya `.zip` olarak indirin.
2.  **Yapılandırma:** Ana dizindeki `.env.example` dosyasını kopyalayın ve adını `.env` olarak değiştirin.
3.  **Hassas Veriler:** `.env` dosyasını açarak veritabanı bilgilerinizi (Host, DB Adı, Kullanıcı, Şifre) ve `SITE_URL` adresinizi girin.
4.  **Sihirbazı Başlatın:** Tarayıcınızdan `http://domain-veya-ip/install` adresine gidin.
5.  **Otomatik Kurulum:** Kurulum sihirbazındaki adımları tamamlayarak veritabanı tablolarını oluşturun ve yönetici hesabınızı kurun.
6.  **Giriş Yapın:** Kurulum bittikten sonra `http://domain-veya-ip/public` adresinden admin paneline giriş yapabilirsiniz.

# 🔐 Ortam Değişkenleri (.env)

Sistemin çalışması için gerekli olan yapılandırma ayarları:

| Değişken | Açıklama |
| :--- | :--- |
| `DB_HOST` | Veritabanı sunucu adresi (genellikle `localhost`) |
| `DB_NAME` | Sistemin kullanacağı veritabanı adı |
| `DB_USER` | Veritabanı kullanıcı adı |
| `DB_PASS` | Veritabanı şifresi |
| `SITE_URL` | Projenin çalıştığı tam URL (Örn: `http://localhost/site/public/`) |
| `APP_DEBUG` | Hataları görmek için `true`, yayında `false` yapın |

# 📸 Ekran Görüntüleri

#### Dashboard
![Dashboard Placeholder](https://via.placeholder.com/1200x600?text=Apartman+Yonetim+Sistemi+Dashboard)

#### Yönetim Paneli
![Admin Panel Placeholder](https://via.placeholder.com/1200x600?text=Yonetim+Paneli+Gorunumu)

# ⚠️ Güvenlik Notları

*   **Şifre Güvenliği:** Sisteme ilk girişte varsayılan yönetici şifresini mutlaka değiştirin.
*   **Gizlilik:** Hassas verilerini içeren `.env` dosyasını asla paylaşmayın ve Git'e pushlamayın.
*   **Erişim Engeli:** Kurulum işlemi başarıyla tamamlandıktan sonra `/install` klasörünü güvenliğiniz için sunucudan silin.

# 🤝 Katkı Sağlama

Projeyi daha iyi hale getirmek için katkılarınızı bekliyoruz:
1. Projeyi **Fork**layın.
2. Yeni bir özellik için **Branch** açın (`git checkout -b feature/YeniOzellik`).
3. Değişikliklerinizi **Commit** edin (`git commit -m 'Eklendi: Yeni özellik'`).
4. Branch'inizi **Push**layın (`git push origin feature/YeniOzellik`).
5. Bir **Pull Request** gönderin.

# 📄 Lisans

Bu proje [MIT License](LICENSE) altında lisanslanmıştır.

# 🎯 Vizyon

Bu proje, herkesin kendi apartmanını teknik bilgiye boğulmadan kolayca yönetebileceği, kurulumu basit, güvenli ve şeffaf bir yönetim altyapısı sunmayı vizyon edinmiştir.
