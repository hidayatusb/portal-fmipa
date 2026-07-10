# LMS Portal — Dokumentasi API Mobile

Base URL: `{APP_URL}/api`

Contoh lokal: `http://127.0.0.1:8000/api`

---

## Autentikasi

API menggunakan **Laravel Sanctum** dengan Bearer Token.

### Header wajib (endpoint terproteksi)

```http
Authorization: Bearer {token}
Accept: application/json
Content-Type: application/json
```

Untuk upload file gunakan `Content-Type: multipart/form-data`.

### Upload File (Materi, Tugas, Jawaban)

| Aturan | Nilai |
|--------|-------|
| Ukuran maksimal | **10 MB** per file |
| Jenis file | **Semua format** diterima (tidak ada batasan ekstensi/MIME) |
| Penyimpanan | **Google Drive** (folder `PORTAL-FMIPA`) |
| Akses file | Via endpoint download API + **Bearer token** (bukan URL publik Drive) |

Struktur folder di Google Drive:

```
PORTAL-FMIPA/
└── {ID DOSEN} - {NAMA DOSEN}/
    └── {KODE KELAS} - {NAMA KELAS}/
        ├── materi/
        ├── tugas 1/
        ├── tugas 2/
        ├── jawaban 1/
        └── jawaban 2/
```

Nomor `tugas N` / `jawaban N` mengikuti urutan tugas dalam kelas (berdasarkan deadline, lalu ID).

---

## Format Response

### Sukses

```json
{
  "success": true,
  "message": "OK",
  "data": {}
}
```

### Error

```json
{
  "success": false,
  "message": "Pesan error",
  "errors": {
    "field": ["Detail validasi"]
  }
}
```

### HTTP Status Umum

| Code | Arti |
|------|------|
| 200 | Sukses |
| 201 | Created |
| 401 | Belum login / token invalid |
| 403 | Role tidak sesuai |
| 404 | Data tidak ditemukan |
| 422 | Validasi gagal |

---

## Auth (Semua Role)

### POST `/auth/login`

Login dan dapatkan token.

**Body (JSON)**

| Field | Tipe | Wajib | Keterangan |
|-------|------|-------|------------|
| username | string | ya | Username akun |
| password | string | ya | Password |
| device_name | string | tidak | Nama perangkat (default: `mobile-app`) |

**Response `data`**

```json
{
  "token": "1|xxxx",
  "token_type": "Bearer",
  "user": {
    "id": 1,
    "name": "Dr. Budi",
    "username": "dosen",
    "email": "dosen@lms.test",
    "role": "dosen",
    "role_label": "Dosen",
    "profile_picture_url": "http://...",
    "initials": "D"
  }
}
```

---

### GET `/auth/me`

Profil user yang sedang login. **Auth required**

---

### POST `/auth/logout`

Hapus token aktif. **Auth required**

---

## Profil (Semua Role)

### GET `/profile`

Ambil profil user. **Auth required**

### GET `/profile/picture`

Unduh foto profil user yang sedang login. **Auth required**

| Header | Nilai |
|--------|-------|
| Authorization | `Bearer {token}` |

Response: file gambar (`Content-Disposition: inline`). Jika user belum punya foto custom, endpoint mengembalikan **404**.

Field `profile_picture_url` di response JSON mengarah ke endpoint ini (dengan query `v` untuk cache busting). Avatar default (belum upload foto) tetap URL publik statis.

**Flutter — tampilkan foto:**

```dart
Image.network(
  user.profilePictureUrl,
  headers: {'Authorization': 'Bearer $token'},
)
```

### PUT/PATCH `/profile`

Update profil. **Auth required**

**Body (JSON atau multipart)**

| Field | Tipe | Keterangan |
|-------|------|------------|
| name | string | Nama lengkap |
| username | string | Username |
| email | string | Email |
| current_password | string | Wajib jika ganti password |
| password | string | Password baru |
| password_confirmation | string | Konfirmasi password |
| profile_picture | file | Foto profil (max 2MB) |
| remove_profile_picture | boolean | Hapus foto profil |

---

## Notifikasi (Semua Role)

### GET `/notifications`

Daftar notifikasi.

**Query**

| Param | Default | Keterangan |
|-------|---------|------------|
| per_page | 20 | Jumlah per halaman |

**Response `data`**

```json
{
  "items": [],
  "meta": {
    "current_page": 1,
    "last_page": 1,
    "per_page": 20,
    "total": 0,
    "unread_count": 0
  }
}
```

### POST `/notifications/{notificationId}/read`

Tandai satu notifikasi dibaca.

### POST `/notifications/read-all`

Tandai semua notifikasi dibaca.

---

## Pengumuman (Semua Role)

**Auth required · Role: admin, dosen, mahasiswa.**

Endpoint **read** ini bisa diakses semua role yang sudah login. Hanya pengumuman yang **dipublikasikan** yang tampil.  
CRUD (buat/edit/hapus) hanya di `/admin/announcements` (role admin).

### GET `/announcements`

Daftar pengumuman publik.

**Query**

| Param | Default | Keterangan |
|-------|---------|------------|
| per_page | 20 | Jumlah per halaman |

**Response `data`**

```json
{
  "items": [
    {
      "id": 1,
      "title": "Jadwal UTS",
      "content_type": "text",
      "content_type_label": "Teks",
      "body": "UTS dilaksanakan minggu depan.",
      "url": null,
      "has_image": true,
      "image_url": "http://.../api/announcements/1/image?v=...",
      "is_published": true,
      "published_at": "2026-07-10T10:00:00+08:00",
      "author": { "id": 1, "name": "Admin" },
      "created_at": "2026-07-10T10:00:00+08:00",
      "updated_at": "2026-07-10T10:00:00+08:00"
    }
  ],
  "meta": {
    "current_page": 1,
    "last_page": 1,
    "per_page": 20,
    "total": 1
  }
}
```

> `content_type`: `text` (isi di `body`) atau `url` (tautan di `body`, juga tersedia di field `url`).  
> `image` **wajib** saat membuat pengumuman.

### GET `/announcements/{announcement}`

Detail satu pengumuman publik.

### GET `/announcements/{announcement}/image`

Stream gambar pengumuman. **Wajib kirim Bearer token.** Response `Content-Disposition: inline`.

> `image_url` di response JSON mengarah ke endpoint ini. Bukan URL publik storage.

---

## Push Notification (FCM) — Hybrid (Topic + Token)

Push **melengkapi** notifikasi database. Model **hybrid**:

| Metode | Digunakan untuk |
|--------|-----------------|
| **FCM Topic** (per kelas) | Tugas baru, deadline mendekat, pengumpulan tugas → dosen |
| **FCM Token** (per user) | Tugas dinilai (personal) |

### Setup Backend

1. Download **Service Account JSON** dari Firebase Console → Service accounts
2. Simpan ke `storage/app/firebase/service-account.json`
3. Set env: `FIREBASE_CREDENTIALS`, `FCM_ENABLED=true`

> File `google-services.json` hanya untuk app Android. Laravel membutuhkan **service-account.json** untuk kirim push.

### FCM Topic (Flutter subscribe)

Format topic:

| Role | Topic |
|------|-------|
| Mahasiswa | `course_{course_id}_students` |
| Dosen | `course_{course_id}_lecturer` |

**Flutter — setelah login:**

```dart
// 1. Ambil daftar topic dari API
final res = await api.get('/fcm/topics');
for (final topic in res.data['topics']) {
  await FirebaseMessaging.instance.subscribeToTopic(topic);
}

// 2. Saat join kelas → subscribe topic baru
await FirebaseMessaging.instance.subscribeToTopic('course_${courseId}_students');

// 3. Saat keluar kelas → unsubscribe
await FirebaseMessaging.instance.unsubscribeFromTopic('course_${courseId}_students');
```

### GET `/fcm/topics`

Daftar topic yang harus di-subscribe user login. **Auth required**

**Response `data`**

```json
{
  "topics": ["course_1_students", "course_2_students"],
  "subscribe_hint": {
    "students": "course_{course_id}_students",
    "lecturer": "course_{course_id}_lecturer"
  }
}
```

### POST `/device-tokens`

Daftar FCM token untuk notifikasi **personal** (tugas dinilai). **Auth required — kirim setelah login**

**Body (JSON)**

| Field | Tipe | Wajib | Keterangan |
|-------|------|-------|------------|
| token | string | ya | FCM device token dari Flutter |
| platform | string | ya | `android` atau `ios` |
| device_name | string | tidak | Nama perangkat |

### DELETE `/device-tokens`

Hapus token saat logout. **Auth required**

**Body (JSON)**

| Field | Tipe | Wajib |
|-------|------|-------|
| token | string | ya |

### Tipe Push (`data.type`)

| type | Metode | Penerima | Kapan |
|------|--------|----------|-------|
| `assignment_submitted` | **Topic** `course_{id}_lecturer` | Dosen | Mahasiswa kumpul / update tugas |
| `assignment_graded` | **Token** per user | Mahasiswa | Tugas dinilai dosen |
| `assignment_new` | **Topic** `course_{id}_students` | Mahasiswa | Tugas baru dibuat |
| `assignment_deadline` | **Topic** `course_{id}_students` | Mahasiswa | Deadline mendekat (24 jam & 72 jam) |

**Contoh payload `data` (untuk deep link Flutter)**

```json
{
  "type": "assignment_graded",
  "course_id": "1",
  "assignment_id": "2",
  "submission_id": "5",
  "score": "88"
}
```

### Troubleshooting: notifikasi hanya muncul saat app terbuka (Android)

Backend mengirim payload **`notification` + `data`** dengan prioritas **high** dan channel `high_importance_channel`. Jika push hanya muncul saat app foreground, periksa di **Flutter/Android**:

1. **Izin notifikasi (Android 13+)** — wajib request runtime permission:
   ```dart
   await FirebaseMessaging.instance.requestPermission();
   // + permission_handler: Permission.notification.request()
   ```

2. **Notification channel** — buat channel dengan ID yang sama dengan backend:
   ```dart
   const channel = AndroidNotificationChannel(
     'high_importance_channel', // harus sama dengan FCM_ANDROID_CHANNEL_ID
     'Notifikasi Penting',
     importance: Importance.high,
   );
   await flutterLocalNotificationsPlugin
       .resolvePlatformSpecificImplementation<AndroidFlutterLocalNotificationsPlugin>()
       ?.createNotificationChannel(channel);
   ```

3. **AndroidManifest.xml** — tambahkan di `<application>`:
   ```xml
   <meta-data
       android:name="com.google.firebase.messaging.default_notification_channel_id"
       android:value="high_importance_channel" />
   ```

4. **Foreground vs background** — perilaku berbeda:
   - **Background/killed**: sistem Android otomatis tampilkan tray notifikasi (jika ada block `notification` di FCM)
   - **Foreground**: `FirebaseMessaging.onMessage` — **harus** tampilkan manual via `flutter_local_notifications`

   ```dart
   FirebaseMessaging.onMessage.listen((message) {
     // WAJIB show local notification saat app terbuka
     showLocalNotification(message);
   });

   // Background handler (top-level function, bukan di dalam class)
   @pragma('vm:entry-point')
   Future<void> firebaseMessagingBackgroundHandler(RemoteMessage message) async {
     await Firebase.initializeApp();
   }

   void main() async {
     WidgetsFlutterBinding.ensureInitialized();
     await Firebase.initializeApp();
     FirebaseMessaging.onBackgroundMessage(firebaseMessagingBackgroundHandler);
   }
   ```

5. **Subscribe topic** — pastikan sudah jalan **setelah login**:
   ```dart
   final res = await api.get('/fcm/topics');
   for (final topic in res.data['topics']) {
     await FirebaseMessaging.instance.subscribeToTopic(topic);
   }
   ```

6. **Jangan campur dengan polling API** — notifikasi di drawer in-app (dari `GET /notifications`) bukan push FCM. Push harus muncul di **system tray** meski app ditutup.

7. **Battery optimization** — matikan battery saver untuk app di pengaturan HP (Xiaomi/Oppo/Vivo sering memblokir FCM).

---

## Admin

**Prefix:** `/admin` · **Role:** `admin`

### GET `/admin/dashboard`

Statistik sistem.

```json
{
  "stats": {
    "users": 10,
    "dosen": 2,
    "mahasiswa": 7,
    "courses": 5
  }
}
```

### GET `/admin/users`

Daftar user.

**Query:** `role`, `search`, `per_page`

### GET `/admin/users/{user}`

Detail user.

### POST `/admin/users/{user}/approve`

Setujui akun (dosen/mahasiswa yang pending).

### POST `/admin/users/{user}/reject`

Tolak akun (dosen/mahasiswa yang pending).

---

### Pengumuman (Admin)

| Method | Endpoint | Keterangan |
|--------|----------|------------|
| GET | `/admin/announcements` | Daftar semua (termasuk draft) |
| POST | `/admin/announcements` | Buat pengumuman |
| GET | `/admin/announcements/{announcement}` | Detail |
| PUT/PATCH/POST | `/admin/announcements/{announcement}` | Update |
| DELETE | `/admin/announcements/{announcement}` | Hapus |

**Query GET `/admin/announcements`:** `search`, `is_published` (`true`/`false`), `content_type` (`text`/`url`), `per_page`

**POST `/admin/announcements` — multipart**

| Field | Tipe | Wajib | Keterangan |
|-------|------|-------|------------|
| title | string | ya | Judul (min 3, max 200) |
| content_type | string | ya | `text` atau `url` |
| body | string | ya | Teks isi, atau URL jika `content_type=url` |
| image | file | ya | Gambar JPG/PNG/GIF/WEBP, maks. 4 MB |
| is_published | boolean | tidak | Default `true` |

**Update — multipart** (disarankan `POST` agar upload gambar lancar di mobile)

| Field | Tipe | Keterangan |
|-------|------|------------|
| title | string | Opsional |
| content_type | string | `text` atau `url` |
| body | string | Teks atau URL sesuai tipe |
| image | file | Ganti gambar (wajib jika belum ada gambar) |
| is_published | boolean | Publikasikan / draft |

Gambar tetap diunduh lewat `GET /announcements/{id}/image` (shared).

---

## Dosen

**Prefix:** `/dosen` · **Role:** `dosen`

### GET `/dosen/dashboard`

Ringkasan dashboard dosen (statistik + kelas terbaru).

---

### Mata Kuliah

| Method | Endpoint | Keterangan |
|--------|----------|------------|
| GET | `/dosen/courses` | Daftar mata kuliah |
| POST | `/dosen/courses` | Buat mata kuliah |
| GET | `/dosen/courses/{course}` | Detail + materi + tugas + mahasiswa |
| PUT/PATCH | `/dosen/courses/{course}` | Update mata kuliah |
| GET | `/dosen/courses/{course}/grades` | Laporan nilai lengkap |

**POST `/dosen/courses` — Body**

```json
{
  "title": "Pemrograman Web",
  "code": "PW101",
  "description": "Deskripsi opsional"
}
```

**Query GET `/dosen/courses`:** `search`

---

### Materi

| Method | Endpoint | Keterangan |
|--------|----------|------------|
| POST | `/dosen/courses/{course}/materials` | Tambah materi |
| DELETE | `/dosen/courses/{course}/materials/{material}` | Hapus materi |
| GET | `/dosen/courses/{course}/materials/{material}/file` | Unduh file materi |

**POST materi — multipart**

| Field | Tipe | Keterangan |
|-------|------|------------|
| title | string | Judul |
| type | string | `document`, `video`, `link`, `text` |
| content | string | Konten teks/link |
| file | file | File lampiran (opsional, max **10 MB**, semua format) |

---

### Tugas

| Method | Endpoint | Keterangan |
|--------|----------|------------|
| POST | `/dosen/courses/{course}/assignments` | Buat tugas |
| GET | `/dosen/courses/{course}/assignments/{assignment}` | Detail + daftar pengumpulan |
| PUT/PATCH | `/dosen/courses/{course}/assignments/{assignment}` | Update tugas |
| DELETE | `/dosen/courses/{course}/assignments/{assignment}` | Hapus tugas |
| GET | `/dosen/courses/{course}/assignments/{assignment}/attachment` | Unduh lampiran tugas |

**POST tugas — multipart**

| Field | Tipe | Keterangan |
|-------|------|------------|
| title | string | Judul tugas |
| description | string | Instruksi |
| due_date | datetime | Batas waktu (ISO 8601) |
| accept_late_submissions | boolean | Terima pengumpulan setelah deadline |
| attachment | file | Lampiran tugas (opsional, max **10 MB**, semua format) |

**PUT/PATCH tugas — multipart (field opsional)**

| Field | Tipe | Keterangan |
|-------|------|------------|
| title | string | Judul tugas |
| description | string | Instruksi |
| due_date | datetime | Batas waktu |
| accept_late_submissions | boolean | Terima pengumpulan setelah deadline |
| attachment | file | Lampiran baru (max **10 MB**, semua format) |
| remove_attachment | boolean | `true` untuk hapus lampiran yang ada |

---

### Pengumpulan & Penilaian

| Method | Endpoint | Keterangan |
|--------|----------|------------|
| GET | `/dosen/courses/{course}/assignments/{assignment}/submissions/{submission}` | Detail jawaban mahasiswa |
| PATCH | `/dosen/courses/{course}/assignments/{assignment}/submissions/{submission}/grade` | Beri nilai |
| GET | `/dosen/courses/{course}/assignments/{assignment}/submissions/{submission}/file` | Unduh file jawaban |

**PATCH grade — Body**

```json
{
  "score": 85,
  "feedback": "Bagus, perbaiki bagian referensi."
}
```

**Response navigation (detail submission)**

```json
{
  "submission": {},
  "navigation": {
    "position": 2,
    "total": 10,
    "previous_submission_id": 1,
    "next_submission_id": 3
  }
}
```

---

### Pengaturan Nilai

| Method | Endpoint | Keterangan |
|--------|----------|------------|
| GET | `/dosen/courses/{course}/grades/settings` | Bobot + nilai mahasiswa |
| PUT | `/dosen/courses/{course}/grades/settings/weights` | Simpan bobot |
| PUT | `/dosen/courses/{course}/grades/settings/students` | Simpan nilai kehadiran/UTS/UAS |

**PUT weights — Body**

```json
{
  "weight_attendance": 10,
  "weight_assignment": 30,
  "weight_uts": 30,
  "weight_uas": 30
}
```

Total bobot **harus 100**.

**PUT students — Body**

```json
{
  "grades": [
    {
      "user_id": 3,
      "attendance_score": 90,
      "uts_score": 80,
      "uas_score": 85
    }
  ]
}
```

---

## Mahasiswa

**Prefix:** `/mahasiswa` · **Role:** `mahasiswa`

### GET `/mahasiswa/dashboard`

Ringkasan dashboard mahasiswa.

---

### Mata Kuliah

| Method | Endpoint | Keterangan |
|--------|----------|------------|
| GET | `/mahasiswa/courses` | Daftar kelas diikuti |
| POST | `/mahasiswa/courses/join` | Gabung kelas via kode |
| GET | `/mahasiswa/courses/{course}` | Detail kelas + materi + tugas |
| GET | `/mahasiswa/courses/{course}/materials/{material}/file` | Unduh file materi |

**POST join — Body**

```json
{
  "code": "PW101"
}
```

**Query GET courses:** `search`

**Response assignment di detail kelas** menyertakan `my_submission`:

```json
{
  "my_submission": {
    "id": 5,
    "submitted_at": "2026-06-26T10:00:00+08:00",
    "is_late": false,
    "is_graded": true,
    "score": 88
  }
}
```

---

### Tugas

| Method | Endpoint | Keterangan |
|--------|----------|------------|
| GET | `/mahasiswa/courses/{course}/assignments/{assignment}` | Detail tugas + submission saya |
| POST | `/mahasiswa/courses/{course}/assignments/{assignment}/submit` | Kumpul / update jawaban |
| GET | `/mahasiswa/courses/{course}/assignments/{assignment}/attachment` | Unduh lampiran tugas dosen |
| GET | `/mahasiswa/courses/{course}/assignments/{assignment}/submissions/{submission}/file` | Unduh file jawaban sendiri |

**POST submit — multipart**

| Field | Tipe | Keterangan |
|-------|------|------------|
| content | string | Jawaban teks |
| file | file | File jawaban (max **10 MB**, semua format) |
| remove_file | boolean | Hapus file yang sudah ada |

> Minimal salah satu: `content` atau `file` harus diisi.

> Setelah submit berhasil, dosen pengampu menerima notifikasi.

---

## Objek Data Penting

### Announcement (Pengumuman)

```json
{
  "id": 1,
  "title": "Jadwal UTS",
  "content_type": "text",
  "content_type_label": "Teks",
  "body": "UTS dilaksanakan minggu depan.",
  "url": null,
  "has_image": true,
  "image_url": "http://.../api/announcements/1/image?v=1710000000",
  "is_published": true,
  "published_at": "2026-07-10T10:00:00+08:00",
  "author": { "id": 1, "name": "Admin" },
  "created_at": "2026-07-10T10:00:00+08:00",
  "updated_at": "2026-07-10T10:00:00+08:00"
}
```

Contoh tipe URL:

```json
{
  "content_type": "url",
  "content_type_label": "URL",
  "body": "https://fmipa.example.com/pengumuman/uts",
  "url": "https://fmipa.example.com/pengumuman/uts"
}
```

> `image_url` adalah endpoint stream API. Request harus menyertakan header `Authorization: Bearer {token}`.

### Material

```json
{
  "id": 1,
  "title": "Slide Pertemuan 1",
  "type": "document",
  "content": null,
  "sort_order": 1,
  "has_file": true,
  "file_name": "slide.zip",
  "file_url": "http://.../api/dosen/courses/1/materials/1/file",
  "created_at": "2026-06-26T10:00:00+08:00"
}
```

> `file_url` dan `attachment_url` adalah endpoint download API. Request harus menyertakan header `Authorization: Bearer {token}`. Response berupa stream file (bukan redirect ke Google Drive).

### Course

```json
{
  "id": 1,
  "title": "Pemrograman Web",
  "code": "PW101",
  "description": "...",
  "materials_count": 3,
  "students_count": 25,
  "assignments_count": 2,
  "lecturer": {}
}
```

### Assignment

```json
{
  "id": 1,
  "course_id": 1,
  "title": "Tugas 1",
  "description": "...",
  "due_date": "2026-07-01T23:59:00+08:00",
  "accept_late_submissions": false,
  "is_overdue": false,
  "is_closed_for_submissions": false,
  "deadline_tone": "active",
  "remaining_label": "5 hari lagi",
  "has_attachment": true,
  "attachment_name": "soal-tugas.zip",
  "attachment_url": "http://.../api/dosen/courses/1/assignments/1/attachment"
}
```

### Submission

```json
{
  "id": 5,
  "content": "Jawaban saya",
  "has_file": true,
  "file_name": "jawaban.zip",
  "file_url": "http://.../api/mahasiswa/courses/1/assignments/2/submissions/5/file",
  "submitted_at": "2026-06-26T10:00:00+08:00",
  "is_late": false,
  "score": 88,
  "feedback": "Bagus",
  "is_graded": true,
  "student": {}
}
```

---

## Contoh Alur Mobile

### 1. Login Dosen

```http
POST /api/auth/login
Content-Type: application/json

{
  "username": "dosen",
  "password": "password",
  "device_name": "Android Pixel 8"
}
```

Simpan `data.token` untuk request berikutnya.

### 2. Lihat Notifikasi Pengumpulan Tugas

```http
GET /api/notifications
Authorization: Bearer {token}
```

### 3. Login Mahasiswa & Kumpul Tugas

```http
POST /api/auth/login
```

```http
POST /api/mahasiswa/courses/1/assignments/2/submit
Authorization: Bearer {token}
Content-Type: multipart/form-data

content=Jawaban tugas
file=@/path/to/file.zip
```

### 4. Dosen Nilai Jawaban

```http
PATCH /api/dosen/courses/1/assignments/2/submissions/5/grade
Authorization: Bearer {token}
Content-Type: application/json

{
  "score": 90,
  "feedback": "Sangat baik"
}
```

---

## Akun Demo (Seeder)

| Role | Username | Password |
|------|----------|----------|
| Admin | admin | password |
| Dosen | dosen | password |
| Mahasiswa | mahasiswa | password |

---

## Catatan Pengembangan

1. Semua endpoint role-specific mengembalikan **403** jika role tidak sesuai.
2. File download membutuhkan **Bearer token** yang sama; file disimpan di **Google Drive** dan di-stream melalui aplikasi.
3. Gunakan `Accept: application/json` agar error validasi konsisten JSON.
4. Timestamp menggunakan format **ISO 8601**.
5. Base URL production ganti sesuai deployment (`https://domain.com/api`).
6. Upload materi, tugas, dan jawaban: **max 10 MB**, **tanpa batasan jenis file**. Foto profil tetap terbatas gambar (max 2 MB).
