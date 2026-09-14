<?php

namespace Tests\Feature;

use App\Models\Absensi;
use App\Models\Jadwal;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use App\Models\Pertemuan;
use App\Models\Tugas;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ApiFlowTest extends TestCase
{
    use RefreshDatabase;

    private User $guru;
    private User $siswa;
    private User $siswa2;
    private Kelas $kelas;
    private MataPelajaran $mapel;
    private Jadwal $jadwal;
    private Pertemuan $pertemuan;

    protected function setUp(): void
    {
        parent::setUp();

        $this->guru = User::create(['name' => 'Bu Yayu', 'email' => 'guru@test.dev', 'password' => bcrypt('password'), 'role' => 'guru']);
        $this->siswa = User::create(['name' => 'Andra', 'email' => 'siswa@test.dev', 'password' => bcrypt('password'), 'role' => 'siswa']);
        $this->siswa2 = User::create(['name' => 'Siswa 2', 'email' => 'siswa2@test.dev', 'password' => bcrypt('password'), 'role' => 'siswa']);

        $this->kelas = Kelas::create(['nama' => 'XII PPLG 1', 'tahun_ajaran' => '2025/2026']);
        $this->kelas->siswa()->sync([$this->siswa->id, $this->siswa2->id]);

        $this->mapel = MataPelajaran::create(['nama' => 'Pemrograman Web', 'deskripsi' => 'Laravel + React', 'guru_id' => $this->guru->id]);

        $this->jadwal = Jadwal::create([
            'kelas_id' => $this->kelas->id,
            'mapel_id' => $this->mapel->id,
            'guru_id' => $this->guru->id,
            'hari' => 'senin',
            'jam_mulai' => '07:30',
            'jam_selesai' => '09:00',
        ]);

        $this->pertemuan = Pertemuan::create([
            'jadwal_id' => $this->jadwal->id,
            'tanggal' => '2026-09-14',
            'pertemuan_ke' => 1,
            'topik' => 'Pengenalan Laravel',
        ]);
    }

    private function auth(User $user): array
    {
        return ['Authorization' => 'Bearer '.$user->createToken('test')->plainTextToken];
    }

    public function test_login_memberi_token(): void
    {
        $res = $this->postJson('/api/login', ['email' => 'guru@test.dev', 'password' => 'password']);

        $res->assertOk()->assertJsonStructure(['token', 'user']);
    }

    public function test_siswa_bisa_lihat_materi(): void
    {
        $this->pertemuan->materi()->create(['judul' => 'Slide MVC', 'konten' => 'Intro']);

        $this->getJson('/api/pertemuan/'.$this->pertemuan->id.'/materi', $this->auth($this->siswa))
            ->assertOk()
            ->assertJsonCount(1)
            ->assertJsonPath('0.judul', 'Slide MVC');
    }

    public function test_guru_bisa_upload_materi(): void
    {
        Storage::fake('public');
        $file = UploadedFile::fake()->create('slide.pdf', 100, 'application/pdf');

        $this->postJson('/api/pertemuan/'.$this->pertemuan->id.'/materi', [
            'judul' => 'Slide Laravel',
            'file' => $file,
        ], $this->auth($this->guru))->assertCreated();

        $this->assertDatabaseHas('materi', ['judul' => 'Slide Laravel']);
        $materi = \App\Models\Materi::where('judul', 'Slide Laravel')->first();
        $this->assertNotNull($materi->file_path);
        Storage::disk('public')->assertExists($materi->file_path);
    }

    public function test_siswa_tidak_bisa_upload_materi(): void
    {
        $this->postJson('/api/pertemuan/'.$this->pertemuan->id.'/materi', [
            'judul' => 'Hack',
            'konten' => 'x',
        ], $this->auth($this->siswa))->assertForbidden();
    }

    public function test_guru_bisa_isi_absensi(): void
    {
        $this->postJson('/api/pertemuan/'.$this->pertemuan->id.'/absensi', [
            'absensi' => [
                ['siswa_id' => $this->siswa->id, 'status' => 'hadir'],
                ['siswa_id' => $this->siswa2->id, 'status' => 'sakit', 'keterangan' => 'Demam'],
            ],
        ], $this->auth($this->guru))->assertCreated();

        $this->assertDatabaseCount('absensi', 2);

        // idempotent: isi ulang tidak dobel
        $this->postJson('/api/pertemuan/'.$this->pertemuan->id.'/absensi', [
            'absensi' => [
                ['siswa_id' => $this->siswa->id, 'status' => 'izin'],
            ],
        ], $this->auth($this->guru))->assertCreated();

        $this->assertDatabaseCount('absensi', 2);
        $this->assertDatabaseHas('absensi', ['siswa_id' => $this->siswa->id, 'status' => 'izin']);
    }

    public function test_rekap_absensi(): void
    {
        Absensi::create(['pertemuan_id' => $this->pertemuan->id, 'siswa_id' => $this->siswa->id, 'status' => 'hadir']);

        $this->getJson('/api/absensi/rekap?kelas_id='.$this->kelas->id.'&mapel_id='.$this->mapel->id, $this->auth($this->guru))
            ->assertOk()
            ->assertJsonPath('0.hadir', 1);
    }

    public function test_siswa_kumpulkan_tugas(): void
    {
        Storage::fake('public');
        $tugas = Tugas::create([
            'pertemuan_id' => $this->pertemuan->id,
            'judul' => 'Tugas 1',
            'deskripsi' => 'Buat CRUD',
            'deadline' => now()->addDays(3),
        ]);
        $file = UploadedFile::fake()->create('jawaban.zip', 50);

        $this->postJson('/api/tugas/'.$tugas->id.'/kumpulkan', [
            'file' => $file,
            'catatan' => 'Ini jawaban saya',
        ], $this->auth($this->siswa))->assertCreated();

        $this->assertDatabaseHas('pengumpulan_tugas', ['tugas_id' => $tugas->id, 'siswa_id' => $this->siswa->id]);
    }

    public function test_guru_nilai_tugas(): void
    {
        Storage::fake('public');
        $tugas = Tugas::create([
            'pertemuan_id' => $this->pertemuan->id,
            'judul' => 'Tugas 1',
            'deskripsi' => 'Buat CRUD',
            'deadline' => now()->addDays(3),
        ]);
        $pengumpulan = $tugas->pengumpulan()->create([
            'siswa_id' => $this->siswa->id,
            'file_path' => 'tugas/a.zip',
            'dikumpulkan_at' => now(),
        ]);

        $this->putJson('/api/pengumpulan/'.$pengumpulan->id.'/nilai', [
            'nilai' => 90,
            'feedback' => 'Bagus',
        ], $this->auth($this->guru))->assertOk();

        $this->assertDatabaseHas('pengumpulan_tugas', ['id' => $pengumpulan->id, 'nilai' => 90]);
    }

    public function test_jurnal_mengajar(): void
    {
        $this->putJson('/api/pertemuan/'.$this->pertemuan->id.'/jurnal', [
            'uraian_kegiatan' => 'Pendahuluan + praktik',
            'hambatan' => 'Wifi lambat',
        ], $this->auth($this->guru))->assertOk();

        $this->assertDatabaseHas('jurnal_mengajar', ['pertemuan_id' => $this->pertemuan->id]);

        $this->getJson('/api/pertemuan/'.$this->pertemuan->id.'/jurnal', $this->auth($this->guru))
            ->assertOk()
            ->assertJsonPath('uraian_kegiatan', 'Pendahuluan + praktik');
    }

    public function test_register_default_role_siswa(): void
    {
        $this->postJson('/api/register', [
            'name' => 'Siswa Baru',
            'email' => 'baru@test.dev',
            'password' => 'password',
            'password_confirmation' => 'password',
        ])->assertCreated()
            ->assertJsonPath('user.role', 'siswa');
    }
}
