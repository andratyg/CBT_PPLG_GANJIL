import React, { useState, useEffect } from 'react'
import api from '../api'
import Card from '../components/Card'
import Button from '../components/Button'
import FormInput from '../components/FormInput'

export default function JurnalPage({ user }) {
  const [pertemuanList, setPertemuanList] = useState([])
  const [selectedPertemuanId, setSelectedPertemuanId] = useState('')
  const [loading, setLoading] = useState(true)
  const [saving, setSaving] = useState(false)
  const [successMsg, setSuccessMsg] = useState('')
  const [errorMsg, setErrorMsg] = useState('')
  const [jurnalInfo, setJurnalInfo] = useState(null)

  const [form, setForm] = useState({
    uraian_kegiatan: '',
    hambatan: ''
  })

  const isGuru = user?.role === 'guru'

  const loadJurnal = async (pertemuanId) => {
    if (!pertemuanId) return
    setLoading(true)
    setSuccessMsg('')
    setErrorMsg('')
    try {
      const res = await api.get(`/pertemuan/${pertemuanId}/jurnal`)
      setJurnalInfo(res.data)
      setForm({
        uraian_kegiatan: res.data?.uraian_kegiatan || '',
        hambatan: res.data?.hambatan || ''
      })
    } catch (err) {
      console.error('Gagal memuat jurnal:', err)
      setJurnalInfo(null)
      setForm({ uraian_kegiatan: '', hambatan: '' })
    } finally {
      setLoading(false)
    }
  }

  useEffect(() => {
    api.get('/pertemuan')
      .then((res) => {
        const list = res.data || []
        setPertemuanList(list)
        if (list.length > 0) {
          setSelectedPertemuanId(list[0].id)
          loadJurnal(list[0].id)
        } else {
          setLoading(false)
        }
      })
      .catch((err) => {
        console.error(err)
        setLoading(false)
      })
  }, [])

  const handleSelectPertemuan = (id) => {
    setSelectedPertemuanId(id)
    loadJurnal(id)
  }

  const handleSave = async (e) => {
    e.preventDefault()
    if (!selectedPertemuanId) return

    const token = localStorage.getItem('token')
    if (!token) {
      setErrorMsg('Sesi login Guru tidak aktif. Silakan login kembali.')
      setTimeout(() => { window.location.href = '/login' }, 1500)
      return
    }

    setSaving(true)
    setSuccessMsg('')
    setErrorMsg('')
    try {
      const res = await api.put(`/pertemuan/${selectedPertemuanId}/jurnal`, {
        uraian_kegiatan: form.uraian_kegiatan,
        hambatan: form.hambatan
      })
      setJurnalInfo(res.data)
      setSuccessMsg('Jurnal mengajar berhasil disimpan permanen ke database!')
      setTimeout(() => setSuccessMsg(''), 5000)
    } catch (err) {
      console.error(err)
      if (err.response?.status === 401) {
        setErrorMsg('Sesi login telah kedaluwarsa (401). Mengarahkan ke login...')
        setTimeout(() => { window.location.href = '/login' }, 1500)
      } else {
        setErrorMsg('Gagal menyimpan jurnal mengajar.')
      }
    } finally {
      setSaving(false)
    }
  }

  const selectedPertemuan = pertemuanList.find((p) => String(p.id) === String(selectedPertemuanId))

  return (
    <div>
      <div className="page-header">
        <div>
          <h1 className="page-title">
            <span>Jurnal Mengajar Guru</span>
            <span className="badge badge-accent">Tersimpan Permanen</span>
          </h1>
          <p className="page-subtitle">Dokumentasi keterlaksanaan proses belajar mengajar, materi, dan kendala kelas</p>
        </div>
      </div>

      {successMsg && (
        <div className="badge badge-accent mb-4" style={{ width: '100%', padding: '0.75rem', fontSize: '0.9rem' }}>
          ✅ {successMsg}
        </div>
      )}

      {errorMsg && (
        <div className="badge badge-danger mb-4" style={{ width: '100%', padding: '0.75rem', fontSize: '0.9rem' }}>
          ⚠️ {errorMsg}
        </div>
      )}

      <div style={{ display: 'grid', gridTemplateColumns: 'repeat(auto-fit, minmax(280px, 1fr))', gap: '1.5rem', alignItems: 'flex-start' }}>
        {/* Left Column: Sesi Selector */}
        <Card title="Pilih Sesi Mengajar" subtitle="Daftar pertemuan yang telah terjadwal">
          <div className="form-group mb-4">
            <select
              className="form-select"
              value={selectedPertemuanId}
              onChange={(e) => handleSelectPertemuan(e.target.value)}
            >
              {pertemuanList.length === 0 && <option value="">Belum ada pertemuan</option>}
              {pertemuanList.map((p) => (
                <option key={p.id} value={p.id}>
                  P{p.pertemuan_ke}: {p.topik} ({p.tanggal})
                </option>
              ))}
            </select>
          </div>

          {selectedPertemuan ? (
            <div style={{ background: 'var(--card-subtle)', padding: '1rem', borderRadius: 'var(--radius-md)', border: '1px solid var(--border)' }}>
              <div style={{ marginBottom: '0.5rem' }}>
                <span className="text-muted" style={{ fontSize: '0.75rem', display: 'block' }}>Mata Pelajaran:</span>
                <strong>{selectedPertemuan.jadwal?.mapel?.nama || 'Mata Pelajaran PPLG'}</strong>
              </div>
              <div style={{ marginBottom: '0.5rem' }}>
                <span className="text-muted" style={{ fontSize: '0.75rem', display: 'block' }}>Kelas / Rombel:</span>
                <strong>{selectedPertemuan.jadwal?.kelas?.nama || 'Kelas PPLG'}</strong>
              </div>
              <div style={{ marginBottom: '0.5rem' }}>
                <span className="text-muted" style={{ fontSize: '0.75rem', display: 'block' }}>Tanggal Pelaksanaan:</span>
                <strong>📅 {selectedPertemuan.tanggal}</strong>
              </div>
              {jurnalInfo?.updated_at && jurnalInfo?.uraian_kegiatan && (
                <div style={{ marginTop: '0.75rem', paddingTop: '0.75rem', borderTop: '1px solid var(--border)', fontSize: '0.75rem', color: '#047857' }}>
                  💾 Status: <strong>Tersimpan di Database</strong>
                  <div style={{ color: 'var(--muted)', marginTop: '0.15rem' }}>
                    Diperbarui: {new Date(jurnalInfo.updated_at).toLocaleString('id-ID')}
                  </div>
                </div>
              )}
            </div>
          ) : (
            <p className="text-muted" style={{ fontSize: '0.85rem' }}>Tidak ada sesi pertemuan yang dipilih.</p>
          )}
        </Card>

        {/* Right Column: Form Jurnal */}
        <Card
          title={`Catatan Jurnal — ${selectedPertemuan ? `Pertemuan Ke-${selectedPertemuan.pertemuan_ke}` : 'Mengajar'}`}
          subtitle="Isi deskripsi kegiatan pembelajaran dan catatan kendala jika ada"
          badge={
            jurnalInfo?.uraian_kegiatan ? (
              <span className="badge badge-accent">Tersimpan</span>
            ) : (
              <span className="badge badge-neutral">Belum Diisi</span>
            )
          }
        >
          {loading ? (
            <div style={{ padding: '2rem', textAlign: 'center', color: 'var(--muted)' }}>
              Memuat catatan jurnal...
            </div>
          ) : !selectedPertemuan ? (
            <div className="empty-state" style={{ padding: '2rem' }}>
              <div className="empty-state-icon">📖</div>
              <div className="empty-state-title">Pilih pertemuan terlebih dahulu</div>
            </div>
          ) : (
            <form onSubmit={handleSave}>
              {isGuru && (
                <div style={{ marginBottom: '1.25rem', padding: '0.75rem 1rem', background: 'var(--card-subtle)', borderRadius: 'var(--radius-md)', border: '1px solid var(--border)' }}>
                  <span style={{ fontSize: '0.775rem', fontWeight: 700, color: 'var(--text)', display: 'block', marginBottom: '0.5rem' }}>
                    ⚡ Template Cepat Pengisian Jurnal (Klik untuk Gunakan):
                  </span>
                  <div style={{ display: 'flex', gap: '0.5rem', flexWrap: 'wrap' }}>
                    <button
                      type="button"
                      className="btn btn-sm btn-secondary"
                      style={{ fontSize: '0.75rem', padding: '0.35rem 0.75rem' }}
                      onClick={() => setForm({
                        uraian_kegiatan: `1. Pendahuluan: Berdoa bersama, mengecek presensi siswa, dan apersepsi materi ${selectedPertemuan?.topik || 'Bahasa Indonesia'}.\n2. Kegiatan Inti: Penjelasan struktur dan kaidah kebahasaan, telaah contoh teks, serta diskusi kelompok siswa.\n3. Penutup: Refleksi pembelajaran, sesi tanya jawab interaktif, dan arahan belajar di sistem CBT.`,
                        hambatan: 'KBM berlangsung tertib dan lancar. Siswa antusias berpartisipasi dalam diskusi.'
                      })}
                      title="Gunakan format KBM Pembelajaran & Diskusi"
                    >
                      📝 KBM Teori & Diskusi
                    </button>
                    <button
                      type="button"
                      className="btn btn-sm btn-secondary"
                      style={{ fontSize: '0.75rem', padding: '0.35rem 0.75rem' }}
                      onClick={() => setForm({
                        uraian_kegiatan: `1. Pendahuluan: Salam, presensi, dan penyampaian lembar kerja praktik penulisan dokumen teknis.\n2. Kegiatan Inti: Siswa menyusun teks laporan hasil observasi dan surat resmi di lab komputer sesuai kaidah EYD V.\n3. Penutup: Pengumpulan draf tugas ke sistem CBT dan pembahasan kendala teknis penulisan.`,
                        hambatan: 'Terdapat siswa yang dispensasi kegiatan sekolah, materi telah dibagikan via modul online.'
                      })}
                      title="Gunakan format Praktik Lab Komputer"
                    >
                      💻 Praktik Lab / Dokumen
                    </button>
                    <button
                      type="button"
                      className="btn btn-sm btn-secondary"
                      style={{ fontSize: '0.75rem', padding: '0.35rem 0.75rem' }}
                      onClick={() => setForm({
                        uraian_kegiatan: `1. Pendahuluan: Pengondisian lab/kelas dan pengecekan perangkat CBT siswa.\n2. Kegiatan Inti: Pelaksanaan evaluasi kompetensi materi Bahasa Indonesia melalui tes daring CBT.\n3. Penutup: Perekapan nilai otomatis dan tindak lanjut program remedial/pengayaan.`,
                        hambatan: 'Seluruh peserta hadir tepat waktu dan koneksi CBT berjalan lancar tanpa kendala teknis.'
                      })}
                      title="Gunakan format Evaluasi / Ujian CBT"
                    >
                      📊 Evaluasi & Asesmen CBT
                    </button>
                  </div>
                </div>
              )}

              <FormInput
                type="textarea"
                label="Uraian Kegiatan Pembelajaran (Pendahuluan, Inti, Penutup)"
                name="uraian_kegiatan"
                value={form.uraian_kegiatan}
                onChange={(e) => setForm({ ...form, uraian_kegiatan: e.target.value })}
                placeholder="Contoh:&#10;1. Guru membuka kelas dengan berdoa dan presensi siswa.&#10;2. Menjelaskan materi dasar teks laporan observasi.&#10;3. Siswa berdiskusi dan menyusun draf laporan.&#10;4. Penutup dan refleksi belajar."
                rows={6}
                required
                disabled={!isGuru}
              />

              <FormInput
                type="textarea"
                label="Kendala / Hambatan & Solusi (Opsional)"
                name="hambatan"
                value={form.hambatan}
                onChange={(e) => setForm({ ...form, hambatan: e.target.value })}
                placeholder="Contoh: Beberapa laptop siswa mengalami kendala node_modules, diselesaikan dengan menggunakan cache lokal dan instalasi bersama."
                rows={4}
                disabled={!isGuru}
              />

              {isGuru && (
                <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', marginTop: '1.25rem' }}>
                  <div style={{ fontSize: '0.8rem', color: 'var(--muted)' }}>
                    Data tersimpan ke tabel <code>jurnal_mengajar</code> di MySQL.
                  </div>
                  <Button type="submit" variant="primary" icon="💾" loading={saving}>
                    Simpan Jurnal Mengajar
                  </Button>
                </div>
              )}
            </form>
          )}
        </Card>
      </div>
    </div>
  )
}
